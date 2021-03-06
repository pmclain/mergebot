<?php

namespace App\Tests\ActionHandler\Github\PullRequest\Opened;

use App\ActionHandler\Github\PullRequest\Opened\AutoMerge;
use App\ActionHandler\Github\PullRequest\Opened\MergeCondition\MergeConditionInterface;
use App\ActionHandler\PermissionValidator;
use App\Entity\EventRecorder;
use App\Github\ConfigRepository;
use App\Github\PullRequestManagement;
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
     * @var ConfigRepository|MockObject
     */
    private $configRepositoryMock;

    /**
     * @var PermissionValidator|MockObject
     */
    private $permissionValidatorMock;

    /**
     * @var EventRecorder|MockObject
     */
    private $eventRecorderMock;

    /**
     * @var array
     */
    private $eventMessage;

    protected function setUp()
    {
        $this->mergeConditionMock = $this->createMock(MergeConditionInterface::class);
        $this->pullRequestManagementMock = $this->createMock(PullRequestManagement::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->configRepositoryMock = $this->createMock(ConfigRepository::class);
        $this->permissionValidatorMock = $this->createMock(PermissionValidator::class);
        $this->eventRecorderMock = $this->createMock(EventRecorder::class);
        $this->eventMessage = json_decode(
            file_get_contents(__DIR__ . '/../../../../data/github/event/pullrequest_open.json'),
            true
        );
    }

    public function testExecute()
    {
        $this->mergeConditionMock->method('allowMerge')
            ->willReturn(true);

        $this->pullRequestManagementMock->expects($this->once())
            ->method('merge');

        $this->permissionValidatorMock->method('isAllowAction')->willReturn(true);

        $action = new AutoMerge(
            [$this->mergeConditionMock],
            $this->pullRequestManagementMock,
            $this->loggerMock,
            $this->configRepositoryMock,
            $this->permissionValidatorMock,
            $this->eventRecorderMock
        );
        $action->execute($this->eventMessage);
    }

    public function testExecuteNoMerge()
    {
        $this->mergeConditionMock->method('allowMerge')
            ->willReturn(false);

        $this->pullRequestManagementMock->expects($this->never())
            ->method('merge');

        $this->permissionValidatorMock->method('isAllowAction')->willReturn(true);

        $action = new AutoMerge(
            [$this->mergeConditionMock],
            $this->pullRequestManagementMock,
            $this->loggerMock,
            $this->configRepositoryMock,
            $this->permissionValidatorMock,
            $this->eventRecorderMock
        );
        $action->execute($this->eventMessage);
    }

    public function testExecuteNoPermission()
    {
        $this->mergeConditionMock->method('allowMerge')
            ->willReturn(false);

        $this->eventRecorderMock->expects($this->once())->method('record');

        $this->pullRequestManagementMock->expects($this->never())
            ->method('merge');

        $this->permissionValidatorMock->method('isAllowAction')->willReturn(false);

        $action = new AutoMerge(
            [$this->mergeConditionMock],
            $this->pullRequestManagementMock,
            $this->loggerMock,
            $this->configRepositoryMock,
            $this->permissionValidatorMock,
            $this->eventRecorderMock
        );
        $action->execute($this->eventMessage);
    }
}
