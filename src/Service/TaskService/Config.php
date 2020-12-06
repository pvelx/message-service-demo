<?php declare(strict_types=1);


namespace App\Service\TaskService;


use App\Service\TaskService\Event\BaseEvent;
use App\Service\TaskService\Event\SendingTimeMessageTriggeredEvent;

class Config
{
    const TASK_TYPE_SENDING_PUSH = 'sendingMessage';

    static private $bindTaskTypeToEvent = [
        self::TASK_TYPE_SENDING_PUSH => SendingTimeMessageTriggeredEvent::class
    ];

    static public function getEventByType($type): BaseEvent
    {
        return self::$bindTaskTypeToEvent[$type];
    }
}
