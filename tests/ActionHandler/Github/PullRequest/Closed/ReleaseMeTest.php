<?php

namespace App\ActionHandler\Github\Closed;

use App\ActionHandler\Config;
use App\ActionHandler\Github\PullRequest\Closed\ReleaseMe;
use App\ActionHandler\PermissionValidator;
use App\Exception\HttpResponseException;
use App\Github\ConfigRepository;
use App\Github\PullRequestManagement;
use App\Github\ReleaseRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ReleaseMeTest extends TestCase
{
    /**
     * @var array
     */
    private $prBase;

    /**
     * @var LoggerInterface|MockObject
     */
    private $loggerMock;

    /**
     * @var ConfigRepository|MockObject
     */
    private $configRepositoryMock;

    /**
     * @var Config|MockObject
     */
    private $configMock;

    /**
     * @var PermissionValidator|MockObject
     */
    private $permissionValidatorMock;

    /**
     * @var ReleaseRepository|MockObject
     */
    private $releaseRepositoryMock;

    /**
     * @var PullRequestManagement|MockObject
     */
    private $pullRequestManagementMock;

    /**
     * @var ReleaseMe
     */
    private $action;

    private $compare = [
        'commits' => [
            [
                'commit' => [
                    'message' => 'commit 1',
                ],
            ],
            [
                'commit' => [
                    'message' => 'commit 2',
                ],
            ],
            [
                'commit' => [
                    'message' => 'Merge pull request #82',
                ],
            ],
        ],
    ];

    protected function setUp()
    {
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->configRepositoryMock = $this->createMock(ConfigRepository::class);
        $this->configMock = $this->createMock(Config::class);
        $this->permissionValidatorMock = $this->createMock(PermissionValidator::class);
        $this->releaseRepositoryMock = $this->createMock(ReleaseRepository::class);
        $this->pullRequestManagementMock = $this->createMock(PullRequestManagement::class);
        $this->prBase = json_decode(
            file_get_contents(__DIR__ . '/../../../../data/github/event/pullrequest_closed.json'),
            true
        );

        $this->action = new ReleaseMe(
            $this->loggerMock,
            $this->configRepositoryMock,
            $this->permissionValidatorMock,
            $this->releaseRepositoryMock,
            $this->pullRequestManagementMock
        );
    }

    public function testExecuteWithoutPermission()
    {
        $this->permissionValidatorMock->method('isAllowAction')->willReturn(false);

        $this->releaseRepositoryMock->expects($this->never())->method('create');

        $this->action->execute($this->prBase);
    }

    public function testExecuteNotMerged()
    {
        $this->permissionValidatorMock->method('isAllowAction')->willReturn(true);
        $this->prBase['pull_request']['merged'] = false;

        $this->releaseRepositoryMock->expects($this->never())->method('create');

        $this->action->execute($this->prBase);
    }

    public function testExecuteNotTargetBranch()
    {
        $this->permissionValidatorMock->method('isAllowAction')->willReturn(true);
        $this->configMock->method('getValue')->willReturn('master');
        $this->configRepositoryMock->method('getConfig')
            ->willReturn($this->configMock);
        $this->prBase['pull_request']['base']['ref'] = 'not-master';

        $this->releaseRepositoryMock->expects($this->never())->method('create');

        $this->action->execute($this->prBase);
    }

    public function testExecuteNoTrigger()
    {
        $this->permissionValidatorMock->method('isAllowAction')->willReturn(true);
        $this->configMock->method('getValue')->willReturn('master');
        $this->configRepositoryMock->method('getConfig')
            ->willReturn($this->configMock);
        $this->prBase['pull_request']['body'] = 'hello';

        $this->releaseRepositoryMock->expects($this->never())->method('create');

        $this->action->execute($this->prBase);
    }

    public function testExecuteNoRelease()
    {
        $this->permissionValidatorMock->method('isAllowAction')->willReturn(true);
        $this->configMock->method('getValue')->willReturn('master');
        $this->configRepositoryMock->method('getConfig')
            ->willReturn($this->configMock);

        $this->releaseRepositoryMock->expects($this->never())->method('create');

        $this->action->execute($this->prBase);
    }

    public function testExecute()
    {
        $this->permissionValidatorMock->method('isAllowAction')->willReturn(true);
        $this->configMock->method('getValue')->willReturn('master');
        $this->configRepositoryMock->method('getConfig')
            ->willReturn($this->configMock);

        $this->releaseRepositoryMock->method('getLatest')
            ->willReturn([
                'tag_name' => 'v1.0.0'
            ]);

        $this->releaseRepositoryMock->method('getComparison')
            ->willReturn($this->compare);

        $this->releaseRepositoryMock->expects($this->once())
            ->method('create')
            ->willReturn(['html_url' => 'http://test.test']);

        $this->action->execute($this->prBase);
    }

    public function testExecuteWithHttpException()
    {
        $this->permissionValidatorMock->method('isAllowAction')->willReturn(true);
        $this->configMock->method('getValue')->willReturn('master');
        $this->configRepositoryMock->method('getConfig')
            ->willReturn($this->configMock);

        $this->releaseRepositoryMock->method('getLatest')
            ->willReturn([
                'tag_name' => 'v1.0.0'
            ]);

        $this->releaseRepositoryMock->method('getComparison')
            ->willReturn($this->compare);


        $exceptionText = 'its really bad';
        $this->releaseRepositoryMock->method('create')
            ->willThrowException(new HttpResponseException($exceptionText));

        $this->loggerMock->expects($this->once())
            ->method('error')
            ->with($exceptionText);

        $this->action->execute($this->prBase);
    }
}
