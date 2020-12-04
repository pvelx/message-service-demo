<?php


namespace App\Domain\Push;


use App\Domain\Push\Contract\SendingTimePushTriggeredEventInterface;
use App\Domain\Push\Dto\ScheduleSendingPush;
use App\Domain\Push\Entity\Push;
use App\Domain\Push\Event\PushScheduledEvent;
use App\Domain\Push\Event\PushShippedEvent;
use App\Domain\Push\Exception\PushManagerException;
use App\Domain\Push\Repository\PushRepository;
use App\Service\TaskService\Config;
use App\Service\TaskService\TaskService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Exception;
use LogicException;
use Psr\EventDispatcher\EventDispatcherInterface;

class PushManager
{
    private $tasksDeferredService;
    private $entityManager;
    private $eventDispatcher;
    private $pushRepository;

    public function __construct(
        TaskService $tasksDeferredService,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        PushRepository $pushRepository
    )
    {
        $this->tasksDeferredService = $tasksDeferredService;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->pushRepository = $pushRepository;
    }

    /**
     * @param ScheduleSendingPush $data
     * @return PushScheduledEvent
     * @throws Exception
     */
    public function scheduleSending(ScheduleSendingPush $data)
    {
        try {
            $this->entityManager->beginTransaction();

            $push = (new Push())
                ->setStatus(PUSH::STATUS_PENDING)
                ->setMessage($data->getMessage())
                ->setUserId($data->getUserId());

            $this->entityManager->persist($push);
            $this->entityManager->flush();

            $this->tasksDeferredService
                ->create($data->getExecTime(), Config::TASK_TYPE_SENDING_PUSH, $push->getId());

            $this->entityManager->commit();
            return new PushScheduledEvent($push);
        } catch (ORMException | Exception $exception) {
            $this->entityManager->rollback();
            throw new PushManagerException();
        }
    }

    /**
     * @param Push $push
     * @return PushScheduledEvent
     * @throws Exception
     */
    public function cancelSending(Push $push)
    {
        try {
            $this->entityManager->beginTransaction();

            if (!$push->isValidNewStatus(PUSH::STATUS_CANCELED)) {
                throw new LogicException();
            }

            $push->setStatus(PUSH::STATUS_CANCELED);

            $this->entityManager->persist($push);
            $this->entityManager->flush();

            $task = $this->tasksDeferredService
                ->getTaskByTaskTypeAndEntity(Config::TASK_TYPE_SENDING_PUSH, $push->getId());
            $this->tasksDeferredService->cancel($task);

            $this->entityManager->commit();
            return new PushScheduledEvent($push);

        } catch (ORMException | Exception | LogicException $exception) {
            $this->entityManager->rollback();
            throw new PushManagerException();
        }
    }

    /**
     * @param $limit
     * @param $offset
     * @return Push[]
     */
    public function getPushes($limit, $offset): array
    {
        return $this->pushRepository->findBy(null, null, $limit, $offset);
    }

    /**
     * @param SendingTimePushTriggeredEventInterface $event
     */
    public function onSendingTimePushTriggeredEvent(SendingTimePushTriggeredEventInterface $event)
    {
        $push = $this->pushRepository->find($event->getPushId());

        try {
            $this->entityManager->beginTransaction();

            if (!$push->isValidNewStatus(PUSH::STATUS_SHIPPED)) {
                throw new LogicException();
            }

            $push->setStatus(PUSH::STATUS_SHIPPED);

            $this->entityManager->persist($push);
            $this->entityManager->flush();

            //use api send

            $this->entityManager->commit();
            $this->eventDispatcher->dispatch(new PushShippedEvent($push));

        } catch (ORMException | Exception | LogicException $exception) {
            $this->entityManager->rollback();
            //log
        }
    }
}
