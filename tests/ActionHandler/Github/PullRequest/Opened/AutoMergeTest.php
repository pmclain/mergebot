<?php

namespace App\Tests\ActionHandler\Github\PullRequest\Opened;

use App\ActionHandler\Github\PullRequest\Opened\AutoMerge;
use App\ActionHandler\Github\PullRequest\Opened\MergeCondition\MergeConditionInterface;
use App\Model\Github\PullRequestManagement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class AutoMergeTest extends TestCase
{
    /**
     * @var MergeConditionInterface|MockObject
     */
    private $mergeConditionMock;

    /**
     * @var PullRequestManagement|MockObject
     */
    private $pullRequestManagementMock;

    /**
     * @var LoggerInterface|MockObject
     */
    private $loggerMock;

    /**
     * @var array
     */
    private $eventMessage;

    protected function setUp()
    {
        $this->mergeConditionMock = $this->createMock(MergeConditionInterface::class);
        $this->pullRequestManagementMock = $this->createMock(PullRequestManagement::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->eventMessage = json_decode(file_get_contents(__DIR__ . '/../../../../data/github/event/pullrequest_open.json'), true);
    }

    public function testExecute()
    {
        $this->mergeConditionMock->method('allowMerge')
            ->willReturn(true);

        $this->pullRequestManagementMock->expects($this->once())
            ->method('merge');

        $action = new AutoMerge(
            [$this->mergeConditionMock],
            $this->pullRequestManagementMock,
            $this->loggerMock
        );
        $action->execute($this->eventMessage);
    }

    public function testExecuteNoMerge()
    {
        $this->mergeConditionMock->method('allowMerge')
            ->willReturn(false);

        $this->pullRequestManagementMock->expects($this->never())
            ->method('merge');

        $action = new AutoMerge(
            [$this->mergeConditionMock],
            $this->pullRequestManagementMock,
            $this->loggerMock
        );
        $action->execute($this->eventMessage);
    }
}
