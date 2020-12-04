<?php

namespace App\Domain\Push\Entity;

use App\Doctrine\StateMachineExtension;
use App\Domain\Push\Repository\PushRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PushRepository::class)
 */
class Push
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
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $message;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="integer")
     */
    private $userId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isValidNewStatus(string $newStatus): bool
    {
        return $this->isValidChangeState(self::$statusStateMachine, $this->getStatus(), $newStatus);
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

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

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }
}
