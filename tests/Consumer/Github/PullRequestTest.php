<?php

namespace App\Tests\Consumer\Github;

use App\ActionHandler\TaskInterface;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use App\Consumer\Github\PullRequest as Consumer;

class PullRequestTest extends TestCase
{
    /**
     * @var AMQPMessage|MockObject
     */
    private $msgMock;

    protected function setUp()
    {
        $this->msgMock = $this->createMock(AMQPMessage::class);
        $this->msgMock->body = file_get_contents(__DIR__ . '/../../data/github/event/pullrequest_open.json');
    }

    public function testExecute()
    {
        $taskMock = $this->createMock(TaskInterface::class);
        $taskMock->expects($this->once())
            ->method('execute');

        $consumer = new Consumer([
            'opened' => [$taskMock]
        ]);

        $consumer->execute($this->msgMock);
    }

    public function testExecuteUnsupportedAction()
    {
        $taskMock = $this->createMock(TaskInterface::class);
        $taskMock->expects($this->never())
            ->method('execute');

        $consumer = new Consumer([
            'other_action' => [$taskMock]
        ]);

        $consumer->execute($this->msgMock);
    }
}
