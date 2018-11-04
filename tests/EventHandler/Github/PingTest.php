<?php

namespace App\Tests\EventHandler\Github;

use App\EventHandler\Github\Ping;
use PHPUnit\Framework\TestCase;

class PingTest extends TestCase
{
    public function testHandle()
    {
        $handler = new Ping();

        $this->assertNull($handler->handle([]));
    }
}
