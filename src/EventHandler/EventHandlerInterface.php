<?php
declare(strict_types=1);

namespace App\EventHandler;

interface EventHandlerInterface
{
    public function handle(array $data): void;
}
