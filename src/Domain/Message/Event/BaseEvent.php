<?php


namespace App\Domain\Message\Event;


use App\Domain\Message\Entity\Message;
use Symfony\Contracts\EventDispatcher\Event;

class BaseEvent extends Event
{
    private $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * @return Message
     */
    public function getMessage(): Message
    {
        return $this->message;
    }
}
