<?php


namespace App\Domain\Message;


use App\Domain\Message\Contract\SendingTimeMessageTriggeredEventInterface;
use App\Domain\Message\Dto\ScheduleSendingMessage;
use App\Domain\Message\Entity\Message;
use App\Domain\Message\Event\MessageScheduledEvent;
use App\Domain\Message\Event\MessageShippedEvent;
use App\Domain\Message\Exception\MessageManagerException;
use App\Domain\Message\Repository\MessageRepository;
use App\Service\TaskService\Config;
use App\Service\TaskService\TaskService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Exception;
use LogicException;
use Psr\EventDispatcher\EventDispatcherInterface;

class MessageManager
{
    private $tasksDeferredService;
    private $entityManager;
    private $eventDispatcher;
    private $messageRepository;

    public function __construct(
        TaskService $tasksDeferredService,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        MessageRepository $messageRepository
    )
    {
        $this->tasksDeferredService = $tasksDeferredService;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->messageRepository = $messageRepository;
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
                ->setEmail($data->getEmail());

            $this->entityManager->persist($message);
            $this->entityManager->flush();

            $this->tasksDeferredService
                ->create($data->getExecTime(), Config::TASK_TYPE_SENDING_PUSH, $message->getId());

            $this->entityManager->commit();
            return new MessageScheduledEvent($message);
        } catch (ORMException | Exception $exception) {
            $this->entityManager->rollback();
            throw new MessageManagerException();
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
                throw new LogicException();
            }

            $message->setStatus(Message::STATUS_CANCELED);

            $this->entityManager->persist($message);
            $this->entityManager->flush();

            $task = $this->tasksDeferredService
                ->getTaskByTaskTypeAndEntity(Config::TASK_TYPE_SENDING_PUSH, $message->getId());
            $this->tasksDeferredService->cancel($task);

            $this->entityManager->commit();
            return new MessageScheduledEvent($message);

        } catch (ORMException | Exception | LogicException $exception) {
            $this->entityManager->rollback();
            throw new MessageManagerException();
        }
    }

    /**
     * @param $limit
     * @param $offset
     * @return Message[]
     */
    public function getMessages($limit, $offset): array
    {
        return $this->messageRepository->findBy(null, null, $limit, $offset);
    }

    /**
     * @param SendingTimeMessageTriggeredEventInterface $event
     */
    public function onSendingTimeMessageTriggeredEvent(SendingTimeMessageTriggeredEventInterface $event)
    {
        $message = $this->messageRepository->find($event->getMessageId());

        try {
            $this->entityManager->beginTransaction();

            if (!$message->isValidNewStatus(Message::STATUS_SHIPPED)) {
                throw new LogicException();
            }

            $message->setStatus(Message::STATUS_SHIPPED);

            $this->entityManager->persist($message);
            $this->entityManager->flush();

            //use api to send

            $this->entityManager->commit();
            $this->eventDispatcher->dispatch(new MessageShippedEvent($message));

        } catch (ORMException | Exception | LogicException $exception) {
            $this->entityManager->rollback();
            //log
        }
    }
}
