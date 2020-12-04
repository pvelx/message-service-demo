<?php


namespace App\Domain\Push\Event;


use App\Domain\Push\Entity\Push;
use Symfony\Contracts\EventDispatcher\Event;

class BaseEvent extends Event
{
    private $push;

    public function __construct(Push $push)
    {
        $this->push = $push;
    }

    /**
     * @return Push
     */
    public function getPush(): Push
    {
        return $this->push;
    }
}
