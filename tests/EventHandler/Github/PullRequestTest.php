<?php

namespace App\Tests\EventHandler\Github;

use App\EventHandler\Github\PullRequest;
use OldSound\RabbitMqBundle\RabbitMq\Producer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PullRequestTest extends TestCase
{
    /**
     * @var Producer|MockObject
     */
    private $producerMock;

    protected function setUp()
    {
        $this->producerMock = $this->createMock(Producer::class);
    }

    public function testHandle()
    {
        $eventData = ['action' => 'test'];
        $this->producerMock->expects($this->once())
            ->method('publish')
            ->with(json_encode($eventData));

        $handler = new PullRequest(['test' => true], $this->producerMock);
        $handler->handle($eventData);
    }

    /**
     * @expectedException \App\Exception\EventNotFoundException
     */
    public function testHandleException()
    {
        $handler = new PullRequest([], $this->producerMock);
        $handler->handle(['action' => 'test']);
    }
}
