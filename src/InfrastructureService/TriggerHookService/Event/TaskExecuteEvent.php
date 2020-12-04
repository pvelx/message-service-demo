<?php


namespace App\InfrastructureService\TriggerHookService\Event;


use Symfony\Contracts\EventDispatcher\Event;

class TaskExecuteEvent extends Event
{
    private $taskId;

    public function __construct(int $taskId)
    {
        $this->taskId = $taskId;
    }

    /**
     * @return int
     */
    public function getTaskId(): int
    {
        return $this->taskId;
    }
}
