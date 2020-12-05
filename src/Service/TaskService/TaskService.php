<?php


namespace App\Service\TaskService;

use App\Service\TaskService\Contract\DeferredServiceExceptionInterface;
use App\Service\TaskService\Contract\DelayServiceInterface;
use App\Service\TaskService\Entity\Task;
use App\Service\TaskService\Event\TaskCanceledEvent;
use App\Service\TaskService\Event\TaskCreatedEvent;
use App\Service\TaskService\Repository\TaskRepository;
use App\Service\TaskServerGrpcAdapter\Event\TaskExecuteEvent;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use LogicException;
use Proto\Request;
use Psr\EventDispatcher\EventDispatcherInterface;

class TaskService
{
    private $delayService;
    private $entityManager;
    private $eventDispatcher;
    private $repository;

    public function __construct(
        DelayServiceInterface $delayService,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        TaskRepository $repository
    )
    {
        $this->delayService = $delayService;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->repository = $repository;
    }

    /**
     * @param $execTime
     * @param $taskType
     * @param $entityId
     * @return TaskCreatedEvent
     * @throws \Exception
     */
    public function create($execTime, $taskType, $entityId)
    {
        try {
            $this->entityManager->beginTransaction();

            $request = new Request();
            $request->setExecTime($execTime);
            $response = $this->delayService->create($request);

            $task = (new Task())
                ->setStatus(Task::STATUS_PENDING)
                ->setExternalId($response->getId())
                ->setTaskType($taskType)
                ->setEntityId($entityId)
                ->setExecTime($execTime);

            $this->entityManager->persist($task);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return new TaskCreatedEvent($task);
        } catch (DeferredServiceExceptionInterface | ORMException $exception) {
            $this->entityManager->rollback();
            throw new \Exception($exception);
        }
    }

    /**
     * @param string $taskType
     * @param int $entityId
     * @return Task|null
     */
    public function getTaskByTypeAndEntityId(string $taskType, int $entityId): ?Task
    {
        return $this->repository->findOneByTaskTypeAndEntityId($taskType, $entityId);
    }

    /**
     * @param Task $task
     * @return TaskCanceledEvent
     * @throws \Exception
     */
    public function cancel(Task $task): TaskCanceledEvent
    {
        try {
            $this->changeStatus($task, Task::STATUS_CANCELED);
            return new TaskCanceledEvent($task);
        } catch (ORMException | LogicException $exception) {
            throw new \Exception($exception);
        }
    }

    /**
     * @param TaskExecuteEvent $event
     */
    public function onTaskExecuteEvent(TaskExecuteEvent $event): void
    {
        $task = $this->repository->findOneByExternalId($event->getTaskId());

        if ($task->getStatus() === Task::STATUS_PENDING) {
            try {
                $this->changeStatus($task, Task::STATUS_COMPLETED);
                $eventClass = Config::getEventByType($task->getTaskType());
                $this->eventDispatcher->dispatch(new $eventClass($task->getId(), $task->getEntityId()));
            } catch (ORMException | \Exception $e) {
                //log error
            }
        }
    }

    /**
     * @param Task $task
     * @param string $newStatus
     * @return Task
     * @throws ORMException
     */
    private function changeStatus(Task $task, string $newStatus)
    {
        $this->entityManager->beginTransaction();

        if (!$task->isValidNewStatus($newStatus)) {
            throw new LogicException();
        }

        $task->setStatus($newStatus);
        $this->entityManager->persist($task);
        $this->entityManager->flush();
        $this->entityManager->commit();

        return $task;
    }
}
