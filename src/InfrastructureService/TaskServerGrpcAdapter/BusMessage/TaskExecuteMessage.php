<?php


namespace App\InfrastructureService\TaskServerGrpcAdapter\BusMessage;


class TaskExecuteMessage
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
