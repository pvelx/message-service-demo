<?php declare(strict_types=1);


namespace App\Contract\TaskService\Event;


interface SendingTimeMessageTriggeredEventInterface
{
    public function getMessageId(): int;

    public function getTaskId(): int;
}
