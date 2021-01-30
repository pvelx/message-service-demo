<?php declare(strict_types=1);


namespace App\Domain\Message\Dto;

use JMS\Serializer\Annotation as JMS;
use DateTime;

class ScheduleSendingMessage
{

    /**
     * @var string
     * @JMS\Type("string")
     */
    private $text;

    /**
     * @var string
     * @JMS\Type("string")
     */
    private $email;

    /**
     * @var DateTime
     * @JMS\Type("DateTime<'Y-m-d H:i:s'>")
     */
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
