<?php

namespace App\EventHandler;

interface EventHandlerInterface
{
    public function handle(array $data);
}
