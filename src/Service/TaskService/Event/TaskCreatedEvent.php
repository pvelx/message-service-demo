<?php declare(strict_types=1);


namespace App\Service\TaskService\Event;


use App\Service\TaskService\Entity\Task;
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
