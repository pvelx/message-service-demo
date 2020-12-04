<?php


namespace App\Domain\Push\Contract;


interface SendingTimePushTriggeredEventInterface
{
    public function getPushId(): int;

    public function getTaskId(): int;
}
