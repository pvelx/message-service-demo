<?php declare(strict_types=1);


namespace App\Service\TriggerServiceGrpcAdapter\BusMessage;


class TriggerExecutedMessage
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
