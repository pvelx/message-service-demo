<?php


namespace App\DomainService\TasksDeferredService\Event;


use Symfony\Contracts\EventDispatcher\Event;

abstract class BaseEvent extends Event
{
    private $entityId;

    public function __construct(int $entityId)
    {
        $this->entityId = $entityId;
    }

    /**
     * @return int
     */
    public function getEntityId(): int
    {
        return $this->getEntityId();
    }

    abstract public function getEntityClassName(): string;
}
