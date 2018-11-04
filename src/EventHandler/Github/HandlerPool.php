<?php

namespace App\EventHandler\Github;

use App\Exception\EventNotFoundException;
use App\EventHandler\EventHandlerPoolInterface;
use App\EventHandler\EventHandlerInterface;

class HandlerPool implements EventHandlerPoolInterface
{
    /**
     * @var EventHandlerInterface[]
     */
    private $events;

    public function __construct(
        array $events = []
    ) {
        $this->events = $events;
    }

    /**
     * @param string $event
     * @param array $data
     * @throws EventNotFoundException
     */
    public function handle(string $event, array $data)
    {
        if (!isset($this->events[$event])) {
            throw new EventNotFoundException(sprintf('No handler for event %s', $event));
        }

        $this->events[$event]->handle($data);
    }
}
