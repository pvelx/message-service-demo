<?php


namespace App\DomainService\TaskService\Event;


use Symfony\Contracts\EventDispatcher\Event;

class BaseEvent extends Event
{
    private $entityId;

    private $taskId;

    public function __construct(int $taskId, $entityId)
    {
        $this->taskId = $taskId;
        $this->entityId = $entityId;
    }

    public function getEntityId(): int
    {
        return $this->entityId;
    }

    public function getTaskId(): int
    {
        return $this->taskId;
    }
}
