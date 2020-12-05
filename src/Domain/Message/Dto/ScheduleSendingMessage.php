<?php


namespace App\Domain\Message\Dto;


use DateTime;

class ScheduleSendingMessage
{

    /** @var string */
    private $text;

    /** @var string */
    private $email;

    /** @var DateTime */
    private $execTime;

    /**
     * ScheduleSendingMessage constructor.
     * @param string $text
     * @param string $email
     * @param DateTime $execTime
     */
    public function __construct(string $text, string $email, DateTime $execTime)
    {
        $this->text = $text;
        $this->email = $email;
        $this->execTime = $execTime;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return DateTime
     */
    public function getExecTime(): DateTime
    {
        return $this->execTime;
    }
}
