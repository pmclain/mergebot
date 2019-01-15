<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @codeCoverageIgnore
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Event
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $taskName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string|null
     */
    private $message;

    /**
     * @ORM\Column(type="blob")
     * @var string|resource
     */
    private $eventData;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTimeInterface
     */
    private $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTaskName(): ?string
    {
        return $this->taskName;
    }

    public function setTaskName(string $taskName): self
    {
        $this->taskName = $taskName;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getEventData(): array
    {
        $eventData = $this->eventData;
        if (is_resource($eventData)) {
            $eventData = (string) stream_get_contents($eventData);
        }

        return json_decode($eventData, true);
    }

    public function setEventData(array $eventData): self
    {
        $this->eventData = json_encode($eventData) ?: '';

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAt(): self
    {
        $this->createdAt = new \DateTime('now');

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'task_name' => $this->getTaskName(),
            'event_data' => $this->getEventData(),
            'created_at' => $this->getCreatedAt(),
        ];
    }
}
