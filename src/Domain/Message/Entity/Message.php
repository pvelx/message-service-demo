<?php

namespace App\Domain\Message\Entity;

use App\Doctrine\StateMachineExtension;
use App\Domain\Message\Repository\MessageRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass=MessageRepository::class)
 * @JMS\ExclusionPolicy("all")
 */
class Message
{
    use StateMachineExtension;

    const STATUS_PENDING = 'pending';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_CANCELED = 'canceled';
    const STATUS_FAILED = 'failed';

    private static $statusStateMachine = [
        self::STATUS_PENDING => [
            self::STATUS_SHIPPED => [
                self::STATUS_FAILED => null
            ],
            self::STATUS_CANCELED => [
                self::STATUS_FAILED => null
            ]
        ]
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @JMS\Expose()
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Expose()
     */
    private $text;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Expose()
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Expose()
     */
    private $email;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Expose()
     */
    private $execTime;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isValidNewStatus(string $newStatus): bool
    {
        return $this->isValidChangeState(self::$statusStateMachine, $this->getStatus(), $newStatus);
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getExecTime(): DateTime
    {
        return $this->execTime;
    }

    /**
     * @param DateTime $execTime
     * @return Message
     */
    public function setExecTime(DateTime $execTime)
    {
        $this->execTime = $execTime;
        return $this;
    }
}
