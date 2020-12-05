<?php


namespace App\Service\TaskService\Event;


use App\Domain\Message\Contract\SendingTimeMessageTriggeredEventInterface;

class SendingTimeMessageTriggeredEvent extends BaseEvent implements SendingTimeMessageTriggeredEventInterface
{
    public function getMessageId(): int
    {
        return $this->getEntityId();
    }
}
