<?php declare(strict_types=1);


namespace App\Service\TaskService;

use App\Contract\TaskServerAdapter\TaskServerAdapterInterface;
use App\Contract\TaskService\TaskServiceInterface;
use App\Service\TaskService\Entity\Task;
use App\Service\TaskService\Event\TaskCanceledEvent;
use App\Service\TaskService\Event\TaskCreatedEvent;
use App\Service\TaskService\Exception\TaskServiceException;
use App\Service\TaskService\Repository\TaskRepository;
use App\Service\TaskServerGrpcAdapter\Event\TaskExecuteEvent;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use LogicException;
use Proto\Request;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class TaskService implements TaskServiceInterface
{
    private $taskServerAdapter;
    private $entityManager;
    private $eventDispatcher;
    private $repository;
    private $logger;

    public function __construct(
        TaskServerAdapterInterface $taskServerAdapter,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        TaskRepository $repository,
        LoggerInterface $logger
    )
    {
        $this->taskServerAdapter = $taskServerAdapter;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->repository = $repository;
        $this->logger = $logger;
    }

    /**
     * @param $execTime
     * @param $taskType
     * @param $entityId
     * @return TaskCreatedEvent
     * @throws TaskServiceException
     */
    public function create(DateTime $execTime, string $taskType, int $entityId): TaskCreatedEvent
    {
        try {
            $this->entityManager->beginTransaction();

            $request = new Request();
            $request->setExecTime($execTime->getTimestamp());

            $response = $this->taskServerAdapter->create($request);

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
        } catch (Throwable $e) {
            $this->entityManager->rollback();
            $this->logger->error('Error creating the task', ['exception' => $e, 'method' => __METHOD__]);
            throw new TaskServiceException($e);
        }
    }

    /**
     * @param string $taskType
     * @param int $entityId
     * @return TaskCanceledEvent
     * @throws TaskServiceException
     */
    public function cancel(string $taskType, int $entityId): TaskCanceledEvent
    {
        try {
            $this->entityManager->beginTransaction();

            $task = $this->repository->findOneByTaskTypeAndEntityId($taskType, $entityId);

            if (null === $task) {
                throw new EntityNotFoundException('Task does not exist');
            }

            if (!$task->isValidNewStatus(Task::STATUS_CANCELED)) {
                throw new LogicException(sprintf(
                    'Status of the task is not valid. Current "%s", need "%s"',
                    $task->getStatus(),
                    Task::STATUS_CANCELED
                ));
            }

            $task->setStatus(Task::STATUS_CANCELED);
            $this->entityManager->persist($task);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return new TaskCanceledEvent($task);
        } catch (Throwable  $e) {
            $this->entityManager->rollback();

            $m = 'Error canceling of the task';
            $context = ['exception' => $e, 'method' => __METHOD__, 'taskType' => $taskType, 'entityId' => $entityId];

            if ($e instanceof EntityNotFoundException || $e instanceof LogicException) {
                $this->logger->warning($m, $context);
            } else {
                $this->logger->error($m, $context);
            }

            throw new TaskServiceException($m);
        }
    }

    /**
     * @param TaskExecuteEvent $event
     */
    public function onTaskExecuteEvent(TaskExecuteEvent $event): void
    {
        try {
            $this->entityManager->beginTransaction();
            $task = $this->repository->findOneByExternalId($event->getTaskId());

            if (null === $task) {
                throw new EntityNotFoundException('Task does not exist');
            }

            if ($task->getStatus() === Task::STATUS_CANCELED) {
                return;
            }

            if (!$task->isValidNewStatus(Task::STATUS_COMPLETED)) {
                throw new LogicException(sprintf(
                    'Status of the task is not valid. Current "%s", need "%s"',
                    $task->getStatus(),
                    Task::STATUS_COMPLETED
                ));
            }

            $task->setStatus(Task::STATUS_COMPLETED);
            $this->entityManager->persist($task);
            $this->entityManager->flush();
            $this->entityManager->commit();

            $eventClass = Config::getEventByType($task->getTaskType());
            $this->eventDispatcher->dispatch(new $eventClass($task->getId(), $task->getEntityId()));
        } catch (Throwable $e) {
            $this->entityManager->rollback();
            $this->logger->error(
                'Error while handle the task',
                ['exception' => $e, 'method' => __METHOD__, 'entityId' => $event->getTaskId()]
            );
        }
    }
}
