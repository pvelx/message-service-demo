<?php declare(strict_types=1);


namespace App\Service\TaskServerGrpcAdapter\BusMessage;


class TaskExecuteMessage
{
    private $taskId;

    public function __construct(string $taskId)
    {
        $this->taskId = $taskId;
    }

    /**
     * @return string
     */
    public function getTaskId(): string
    {
        return $this->taskId;
    }
}
