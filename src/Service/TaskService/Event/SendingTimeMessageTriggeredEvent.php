<?php declare(strict_types=1);


namespace App\Service\TaskService\Event;


use App\Contract\TaskService\Event\SendingTimeMessageTriggeredEventInterface;

class SendingTimeMessageTriggeredEvent extends BaseEvent implements SendingTimeMessageTriggeredEventInterface
{
    public function getMessageId(): int
    {
        return $this->getEntityId();
    }
}
