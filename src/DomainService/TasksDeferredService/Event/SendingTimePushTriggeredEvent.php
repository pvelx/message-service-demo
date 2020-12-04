<?php


namespace App\DomainService\TasksDeferredService\Event;


class SendingTimePushTriggeredEvent extends BaseEvent
{
    public function getEntityClassName(): string
    {
        return '';
//        return Push::class;
    }
}
