<?php


namespace App\Domain\Message\Contract;


interface SendingTimeMessageTriggeredEventInterface
{
    public function getMessageId(): int;

    public function getTaskId(): int;
}
