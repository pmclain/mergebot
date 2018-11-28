<?php
declare(strict_types=1);

namespace App\Entity;

/**
 * @codeCoverageIgnore
 */
class EventFactory
{
    public function create(): Event
    {
        return new Event();
    }
}
