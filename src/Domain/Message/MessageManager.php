<?php


namespace App\Domain\Message;

use App\Contract\Mailer\MailerExceptionInterface;
use App\Contract\Mailer\MailerInterface;
use App\Domain\Message\Contract\SendingTimeMessageTriggeredEventInterface;
use App\Domain\Message\Dto\ScheduleSendingMessage;
use App\Domain\Message\Entity\Message;
use App\Domain\Message\Event\MessageScheduledEvent;
use App\Domain\Message\Event\MessageShippedEvent;
use App\Domain\Message\Event\MessageShippingFailedEvent;
use App\Domain\Message\Exception\MessageManagerException;
use App\Domain\Message\Repository\MessageRepository;
use App\Service\TaskService\Config;
use App\Service\TaskService\TaskService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use LogicException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class MessageManager
{
    private $taskService;
    private $entityManager;
    private $eventDispatcher;
    private $messageRepository;
    private $logger;
    private $mailer;

    public function __construct(
        TaskService $taskService,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        MessageRepository $messageRepository,
        MailerInterface $mailer,
        LoggerInterface $logger
    )
    {
        $this->taskService = $taskService;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->messageRepository = $messageRepository;
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    /**
     * @param ScheduleSendingMessage $data
     * @return MessageScheduledEvent
     * @throws Exception
     */
    public function scheduleSending(ScheduleSendingMessage $data)
    {
        try {
            $this->entityManager->beginTransaction();

            $message = (new Message())
                ->setStatus(Message::STATUS_PENDING)
                ->setText($data->getText())
                ->setEmail($data->getEmail())
                ->setExecTime($data->getExecTime());

            $this->entityManager->persist($message);
            $this->entityManager->flush();

            $this->taskService->create($data->getExecTime(), Config::TASK_TYPE_SENDING_PUSH, $message->getId());

            $this->entityManager->commit();
            return new MessageScheduledEvent($message);
        } catch (Throwable $e) {
            $this->entityManager->rollback();
            $error = 'Error while schedule new message';
            $this->logger->error($error, ['exception' => $e, 'method' => __METHOD__, 'data' => $data]);
            throw new MessageManagerException($error);
        }
    }

    /**
     * @param Message $message
     * @return MessageScheduledEvent
     * @throws Exception
     */
    public function cancelSending(Message $message)
    {
        try {
            $this->entityManager->beginTransaction();

            if (!$message->isValidNewStatus(Message::STATUS_CANCELED)) {
                throw new LogicException('Status is not valid');
            }

            $message->setStatus(Message::STATUS_CANCELED);

            $this->entityManager->persist($message);
            $this->entityManager->flush();

            $task = $this->taskService->getTaskByTypeAndEntityId(Config::TASK_TYPE_SENDING_PUSH, $message->getId());
            if ($task === null) {
                throw new NotFoundHttpException('Task for the message does not exist');
            }

            $this->taskService->cancel($task);

            $this->entityManager->commit();
            return new MessageScheduledEvent($message);

        } catch (LogicException $e) {
            $this->entityManager->rollback();
            $m = sprintf('Logic was violated %s', $e->getMessage());
            $this->logger->warning($m, ['exception' => $e, 'method' => __METHOD__, 'entityId' => $message->getId()]);
            throw new MessageManagerException($m);

        } catch (Throwable $e) {
            $this->entityManager->rollback();
            $m = 'Error while cancel sending message';
            $this->logger->error($m, ['exception' => $e, 'method' => __METHOD__, 'entityId' => $message->getId()]);
            throw new MessageManagerException($m);
        }
    }

    /**
     * @param $limit
     * @param $offset
     * @return Message[]
     */
    public function getMessages($limit, $offset): array
    {
        return $this->messageRepository->findBy([], [], $limit, $offset);
    }

    /**
     * @param int $id
     * @return Message|null
     */
    public function getMessage(int $id): ?Message
    {
        return $this->messageRepository->find($id);
    }

    /**
     * @param SendingTimeMessageTriggeredEventInterface $event
     */
    public function onSendingTimeMessageTriggeredEvent(SendingTimeMessageTriggeredEventInterface $event)
    {
        try {
            $this->entityManager->beginTransaction();

            $message = $this->messageRepository->find($event->getMessageId());
            if (null === $message) {
                throw new NotFoundHttpException('The message does not exist');
            }

            if (!$message->isValidNewStatus(Message::STATUS_SHIPPED)) {
                throw new LogicException('Status violate logic of state transition');
            }

            $message->setStatus(Message::STATUS_SHIPPED);

            try {
                $this->mailer->send($message->getEmail(), $message->getText());
                $event = new MessageShippedEvent($message);
            } catch (MailerExceptionInterface $e) {
                $message->setStatus(Message::STATUS_SHIPPING_FAILED);
                $event = new MessageShippingFailedEvent($message);
            }

            $this->entityManager->persist($message);
            $this->entityManager->flush();
            $this->entityManager->commit();
            $this->eventDispatcher->dispatch($event);
        } catch (Throwable $e) {
            $this->entityManager->rollback();
            $m = 'Error while handle event from task service';
            $this->logger->error($m, ['exception' => $e, 'method' => __METHOD__, 'entityId' => $event->getMessageId()]);
        }
    }
}
