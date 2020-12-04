<?php


namespace App\DomainService\TasksDeferredService;


use App\DomainService\TasksDeferredService\Event\BaseEvent;
use App\DomainService\TasksDeferredService\Event\SendingTimePushTriggeredEvent;

class Config
{
    const ENTITY_TYPE_PUSH = 'push';

    const TASK_TYPE_SENDING_PUSH = 'sendingPush';

    static public $bindEventToEntity = [
        SendingTimePushTriggeredEvent::class => self::ENTITY_TYPE_PUSH
    ];

    static private $bindTaskTypeToEvent = [
        self::TASK_TYPE_SENDING_PUSH => SendingTimePushTriggeredEvent::class
    ];

    static public function getEventByType($type): BaseEvent
    {
        return self::$bindTaskTypeToEvent[$type];
    }
}
