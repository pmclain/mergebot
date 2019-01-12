<?php

namespace App\Tests\Controller\Github;

use App\Controller\Github\Webhook;
use App\EventHandler\EventHandlerPoolInterface;
use App\Exception\EventNotFoundException;
use App\Github\RequestValidator;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\EventRecorder;

class WebhookTest extends TestCase
{
    /**
     * @var Request|MockObject
     */
    private $requestMock;

    /**
     * @var RequestValidator|MockObject
     */
    private $validatorMock;

    /**
     * @var EventRecorder|MockObject
     */
    private $eventRecorderMock;

    protected function setUp()
    {
        $this->requestMock = $this->createMock(Request::class);
        $this->requestMock->method('getContent')
            ->willReturn('{"request": "body"}');

        $this->validatorMock = $this->createMock(RequestValidator::class);
        $this->eventRecorderMock = $this->createMock(EventRecorder::class);

        $headersMock = $this->createMock(HeaderBag::class);

        $headersMock->method('get')
            ->with('X-GitHub-Event')
            ->willReturn('event_code');

        $this->requestMock->headers = $headersMock;
    }

    public function testExecute()
    {
        $handlerPoolMock = $this->createMock(EventHandlerPoolInterface::class);

        $controller = new Webhook($handlerPoolMock, $this->validatorMock, $this->eventRecorderMock);
        $result = $controller->execute($this->requestMock);

        $this->assertEquals(202, $result->getStatusCode());
    }

    public function testExecuteWithException()
    {
        $handlerPoolMock = $this->createMock(EventHandlerPoolInterface::class);
        $handlerPoolMock->method('handle')
            ->willThrowException(new EventNotFoundException(''));

        $controller = new Webhook($handlerPoolMock, $this->validatorMock, $this->eventRecorderMock);
        $result = $controller->execute($this->requestMock);

        $this->assertEquals(400, $result->getStatusCode());
    }

    public function testExecuteFailedSecretValidation()
    {
        $handlerPoolMock = $this->createMock(EventHandlerPoolInterface::class);

        $this->validatorMock->method('validate')
            ->willThrowException(new \App\Exception\RequestValidationException('message'));

        $controller = new Webhook($handlerPoolMock, $this->validatorMock, $this->eventRecorderMock);
        $result = $controller->execute($this->requestMock);

        $this->assertEquals(403, $result->getStatusCode());
    }
}
