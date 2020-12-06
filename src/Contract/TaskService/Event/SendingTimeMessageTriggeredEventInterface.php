<?php


namespace App\Contract\TaskService\Event;


interface SendingTimeMessageTriggeredEventInterface
{
    public function getMessageId(): int;

    public function getTaskId(): int;
}
