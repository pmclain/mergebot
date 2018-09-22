<?php

namespace App\Tests\Controller;

use App\Controller\Github;
use App\EventHandler\EventHandlerPoolInterface;
use App\Exception\EventNotFoundException;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;

class GithubTest extends TestCase
{
    /**
     * @var Request|MockObject
     */
    private $requestMock;

    protected function setUp()
    {
        $this->requestMock = $this->createMock(Request::class);
        $this->requestMock->method('getContent')
            ->willReturn('{"request": "body"}');

        $headersMock = $this->createMock(HeaderBag::class);

        $headersMock->expects($this->once())
            ->method('get')
            ->with('X-GitHub-Event')
            ->willReturn('event_code');

        $this->requestMock->headers = $headersMock;
    }

    public function testWebhook()
    {
        $handlerPoolMock = $this->createMock(EventHandlerPoolInterface::class);

        $controller = new Github($handlerPoolMock);
        $result = $controller->webhook($this->requestMock);

        $this->assertEquals(202, $result->getStatusCode());
    }

    public function testWebhookWithException()
    {
        $handlerPoolMock = $this->createMock(EventHandlerPoolInterface::class);
        $handlerPoolMock->method('handle')
            ->willThrowException(new EventNotFoundException(''));

        $controller = new Github($handlerPoolMock);
        $result = $controller->webhook($this->requestMock);

        $this->assertEquals(400, $result->getStatusCode());
    }
}
