<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\EntityManagerInterface;

class EventRecorder
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EventFactory
     */
    private $eventFactory;

    public function __construct(
        EntityManagerInterface $entityManager,
        EventFactory $eventFactory
    ) {
        $this->entityManager = $entityManager;
        $this->eventFactory = $eventFactory;
    }

    public function record(string $taskName, array $eventData, ?string $message = null): Event
    {
        $event = $this->eventFactory->create();
        $event->setTaskName($taskName);
        $event->setEventData($eventData);
        $event->setMessage($message);

        $this->entityManager->persist($event);
        $this->entityManager->flush();

        return $event;
    }
}
