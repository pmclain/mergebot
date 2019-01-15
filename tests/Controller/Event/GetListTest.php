<?php

namespace App\Tests\Controller;

use App\Controller\Event\GetList;
use App\Repository\EventRepository;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

class GetListTest extends TestCase
{
    /**
     * @var Request|MockObject
     */
    private $requestMock;

    /**
     * @var EventRepository|MockObject
     */
    private $repositoryMock;

    protected function setUp()
    {
        $this->requestMock = $this->createMock(Request::class);
        $this->requestMock->method('getContent')
            ->willReturn('{"request": "body"}');

        $this->repositoryMock = $this->createMock(EventRepository::class);
    }

    public function testExecute()
    {
        $event = new \App\Entity\Event();
        $event->setEventData(['test' => 'test'])
            ->setTaskName('test-event')
            ->setMessage('error');

        $controller = new GetList($this->repositoryMock);
        $this->repositoryMock->method('findAll')->willReturn([$event]);

        $this->assertEquals(
            json_encode([$event->toArray()]),
            $controller->execute($this->requestMock)->getContent()
        );
    }
}
