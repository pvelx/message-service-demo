<?php


namespace App\Service\TaskService;


use App\Service\TaskService\Event\BaseEvent;
use App\Service\TaskService\Event\SendingTimePushTriggeredEvent;

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
