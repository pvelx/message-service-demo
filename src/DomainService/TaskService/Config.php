<?php


namespace App\DomainService\TaskService;


use App\DomainService\TaskService\Event\BaseEvent;
use App\DomainService\TaskService\Event\SendingTimePushTriggeredEvent;

class Config
{
    const TASK_TYPE_SENDING_PUSH = 'sendingPush';

    static private $bindTaskTypeToEvent = [
        self::TASK_TYPE_SENDING_PUSH => SendingTimePushTriggeredEvent::class
    ];

    static public function getEventByType($type): BaseEvent
    {
        return self::$bindTaskTypeToEvent[$type];
    }
}
