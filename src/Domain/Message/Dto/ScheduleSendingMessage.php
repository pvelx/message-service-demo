<?php


namespace App\Domain\Message\Dto;


use DateTime;

class ScheduleSendingMessage
{

    /** @var string */
    private $message;

    /** @var int */
    private $userId;

    /** @var DateTime */
    private $execTime;

    /**
     * ScheduleSendingMessage constructor.
     * @param string $message
     * @param int $userId
     * @param DateTime $execTime
     */
    public function __construct(string $message, int $userId, DateTime $execTime)
    {
        $this->message = $message;
        $this->userId = $userId;
        $this->execTime = $execTime;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return DateTime
     */
    public function getExecTime(): DateTime
    {
        return $this->execTime;
    }
}
