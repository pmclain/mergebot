<?php

namespace App\Controller;

use App\Repository\EventRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class Event
{
    /**
     * @var EventRepository
     */
    private $eventRepository;

    public function __construct(
        EventRepository $eventRepository
    ) {
        $this->eventRepository = $eventRepository;
    }

    public function list(Request $request): JsonResponse
    {
        $responseItems = [];
        //TODO: add filter/sort support
        $events = $this->eventRepository->findAll();

        foreach ($events as $event) {
            $responseItems[$event->getId()] = $event->toArray();
        }

        return new JsonResponse($responseItems);
    }
}
