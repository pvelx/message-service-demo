<?php declare(strict_types=1);


namespace App\Contract\TaskService;

use App\Service\TaskService\Event\TaskCanceledEvent;
use App\Service\TaskService\Event\TaskCreatedEvent;
use DateTime;

interface TaskServiceInterface
{
    /**
     * @param $execTime
     * @param $taskType
     * @param $entityId
     * @return TaskCreatedEvent
     * @throws TaskServiceExceptionInterface
     */
    public function create(DateTime $execTime, string $taskType, int $entityId): TaskCreatedEvent;

    /**
     * @param string $taskType
     * @param int $entityId
     * @return TaskCanceledEvent
     * @throws TaskServiceExceptionInterface
     */
    public function cancel(string $taskType, int $entityId): TaskCanceledEvent;
}
