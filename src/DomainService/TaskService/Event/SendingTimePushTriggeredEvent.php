<?php


namespace App\DomainService\TaskService\Event;


use App\Domain\Push\Contract\SendingTimePushTriggeredEventInterface;

class SendingTimePushTriggeredEvent extends BaseEvent implements SendingTimePushTriggeredEventInterface
{
    public function getPushId(): int
    {
        return $this->getEntityId();
    }
}
