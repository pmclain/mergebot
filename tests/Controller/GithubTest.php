<?php

namespace App\Tests\Controller;

use App\Controller\Github;
use App\EventHandler\EventHandlerPoolInterface;
use App\Exception\EventNotFoundException;
use App\Github\RequestValidator;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class GithubTest extends TestCase
{
    /**
     * @var Request|MockObject
     */
    private $requestMock;

    /**
     * @var RequestValidator|MockObject
     */
    private $validatorMock;

    protected function setUp()
    {
        $this->requestMock = $this->createMock(Request::class);
        $this->requestMock->method('getContent')
            ->willReturn('{"request": "body"}');

        $this->validatorMock = $this->createMock(RequestValidator::class);

        $headersMock = $this->createMock(HeaderBag::class);

        $headersMock->method('get')
            ->with('X-GitHub-Event')
            ->willReturn('event_code');

        $this->requestMock->headers = $headersMock;
    }

    public function testWebhook()
    {
        $handlerPoolMock = $this->createMock(EventHandlerPoolInterface::class);

        $controller = new Github($handlerPoolMock, $this->validatorMock);
        $result = $controller->webhook($this->requestMock);

        $this->assertEquals(202, $result->getStatusCode());
    }

    public function testWebhookWithException()
    {
        $handlerPoolMock = $this->createMock(EventHandlerPoolInterface::class);
        $handlerPoolMock->method('handle')
            ->willThrowException(new EventNotFoundException(''));

        $controller = new Github($handlerPoolMock, $this->validatorMock);
        $result = $controller->webhook($this->requestMock);

        $this->assertEquals(400, $result->getStatusCode());
    }

    public function testWebhookFailedSecretValidation()
    {
        $handlerPoolMock = $this->createMock(EventHandlerPoolInterface::class);

        $this->validatorMock->method('validate')
            ->willThrowException(new UnauthorizedHttpException(''));

        $controller = new Github($handlerPoolMock, $this->validatorMock);
        $result = $controller->webhook($this->requestMock);

        $this->assertEquals(403, $result->getStatusCode());
    }
}
