<?php


namespace App\DomainService\TasksDeferredService\Event;


use App\DomainService\TasksDeferredService\Entity\Task;
use Symfony\Contracts\EventDispatcher\Event;

class TaskCreatedEvent extends Event
{
    private $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * @return Task
     */
    public function getTask(): Task
    {
        return $this->task;
    }
}
