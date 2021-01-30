<?php declare(strict_types=1);

namespace App\Service\TaskService\Entity;

use App\Doctrine\StateMachineExtension;
use App\Service\TaskService\Repository\TaskRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
class Task
{
    use StateMachineExtension;

    const STATUS_PENDING = 'pending';
    const STATUS_CANCELED = 'canceled';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    private static $statusStateMachine = [
        self::STATUS_PENDING => [
            self::STATUS_CANCELED => [
                self::STATUS_FAILED => null
            ],
            self::STATUS_COMPLETED => [
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
    private $taskType;

    /**
     * @ORM\Column(type="integer")
     */
    private $entityId;

    /**
     * @ORM\Column(type="string", length=36)
     */
    private $externalId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $execTime;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEntityId(): ?int
    {
        return $this->entityId;
    }

    public function setEntityId(int $entityId): self
    {
        $this->entityId = $entityId;

        return $this;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function setExternalId(string $externalId): self
    {
        $this->externalId = $externalId;

        return $this;
    }

    public function isValidNewStatus(string $newStatus): bool
    {
        return $this->isValidChangeState(self::$statusStateMachine, $this->getStatus(), $newStatus);
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

    /**
     * @return string
     */
    public function getTaskType(): string
    {
        return $this->taskType;
    }

    /**
     * @param string $taskType
     * @return Task
     */
    public function setTaskType(string $taskType)
    {
        $this->taskType = $taskType;
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
     * @return Task
     */
    public function setExecTime(DateTime $execTime)
    {
        $this->execTime = $execTime;
        return $this;
    }
}
