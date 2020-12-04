<?php


namespace App\DomainService\TasksDeferredService;

use App\DomainService\TasksDeferredService\Entity\Task;
use App\DomainService\TasksDeferredService\Event\TaskCreatedEvent;
use App\DomainService\TasksDeferredService\Repository\TaskRepository;
use App\InfrastructureService\TriggerHookService\Event\TaskExecuteEvent;
use App\InfrastructureService\TriggerHookService\Exception\DeferredServiceExceptionInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Proto\Request;
use Psr\EventDispatcher\EventDispatcherInterface;

class TasksDeferredService
{
    private $delayService;
    private $entityManager;
    private $eventDispatcher;

    public function __construct(
        DelayServiceInterface $delayService,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    )
    {
        $this->delayService = $delayService;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param $execTime
     * @param $taskType
     * @param $entityId
     * @throws \Exception
     */
    public function create($execTime, $taskType, $entityId)
    {
        try {
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

            return new TaskCreatedEvent($task);
        } catch (DeferredServiceExceptionInterface | ORMException $exception) {
            throw new \Exception($exception);
        }
    }

    public function onTaskExecuteEvent(TaskExecuteEvent $event): void
    {
        /** @var TaskRepository $repository */
        $repository = $this
            ->entityManager
            ->getRepository(Task::class);
        $task = $repository->findOneByExternalId($event->getTaskId());

        $eventClass = Config::getEventByType($task->getTaskType());

        $this->eventDispatcher->dispatch(new $eventClass($task->getEntityId()));
    }
}
