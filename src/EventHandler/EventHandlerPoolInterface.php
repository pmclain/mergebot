<?php

namespace App\EventHandler;

use App\Exception\EventNotFoundException;

interface EventHandlerPoolInterface
{
    /**
     * @param string $event
     * @param array $data
     * @throws EventNotFoundException
     */
    public function handle(string $event, array $data);
}
