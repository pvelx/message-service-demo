<?php declare(strict_types=1);


namespace App\Service\TriggerServiceGrpcAdapter\Event;

use Symfony\Contracts\EventDispatcher\Event;

class TriggerExecutedEvent extends Event
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
