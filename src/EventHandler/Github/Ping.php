<?php

namespace App\EventHandler\Github;

use App\EventHandler\EventHandlerInterface;
use App\Exception\EventNotFoundException;
use OldSound\RabbitMqBundle\RabbitMq\Producer;

class Ping implements EventHandlerInterface
{
    /**
     * @param array $data
     */
    public function handle(array $data)
    {
        //Dummy event used to validate the hook endpoint
        return;
    }
}
