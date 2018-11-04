<?php

namespace App\Tests\EventHandler\Github;

use App\EventHandler\EventHandlerInterface;
use App\EventHandler\Github\HandlerPool;
use PHPUnit\Framework\TestCase;

class HandlerPoolTest extends TestCase
{
    public function testHandle()
    {
        $eventHandlerMock = $this->createMock(EventHandlerInterface::class);
        $eventHandlerMock->expects($this->once())->method('handle');

        $pool = new HandlerPool(['testEvent' => $eventHandlerMock]);

        $pool->handle('testEvent', []);
    }

    /**
     * @expectedException \App\Exception\EventNotFoundException
     */
    public function testHandleException()
    {
        $pool = new HandlerPool([]);

        $pool->handle('testEvent', []);

    }
}
