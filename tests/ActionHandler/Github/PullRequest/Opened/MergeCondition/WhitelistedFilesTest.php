<?php

namespace App\Tests\ActionHandler\Github\PullRequest\Opened\MergeCondition;

use App\ActionHandler\Config;
use App\ActionHandler\Github\PullRequest\Opened\MergeCondition\WhitelistedFiles;
use App\Github\ConfigRepository;
use App\Github\PullRequestManagement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class WhitelistedFilesTest extends TestCase
{
    /**
     * @var ConfigRepository|MockObject
     */
    private $configRepositoryMock;

    /**
     * @var Config|MockObject
     */
    private $configMock;

    /**
     * @var array
     */
    private $fileResponse;

    private $pullRequestData = [
        'pull_request' => [
            'changed_files' => 3,
            'base' => [
                'repo' => [
                    'owner' => [
                        'login' => 'test',
                    ],
                    'name' => 'test',
                ],
                'ref' => 'master',
            ],
        ],
    ];

    protected function setUp()
    {
        $this->fileResponse = json_decode(
            file_get_contents(__DIR__ . '/../../../../../data/github/pullrequest/files.json'),
            true
        );
        $this->configRepositoryMock = $this->createMock(ConfigRepository::class);
        $this->configMock = $this->createMock(Config::class);
    }

    public function testAllowMergeTooManyFiles()
    {
        $pullRequestManagementStub = $this->createMock(PullRequestManagement::class);

        $this->configMock->method('getValue')
            ->willReturn(['a.txt' => ['modified'], 'b.txt' => ['modified']]);

        $this->configRepositoryMock->method('getConfig')->willReturn($this->configMock);

        $whitelistedFiles = new WhitelistedFiles($pullRequestManagementStub, $this->configRepositoryMock);

        $this->assertFalse($whitelistedFiles->allowMerge($this->pullRequestData));
    }

    public function testAllowMerge()
    {
        $pullRequestManagementMock = $this->createMock(PullRequestManagement::class);
        $pullRequestManagementMock->method('getFiles')
            ->willReturn($this->fileResponse);

        $this->configMock->method('getValue')
            ->willReturn([
                'file1.txt' => ['modified', 'added'],
                'file2.txt' => ['modified', 'added'],
                'file3.txt' => ['modified', 'added']
            ]);

        $this->configRepositoryMock->method('getConfig')->willReturn($this->configMock);

        $whitelistedFiles = new WhitelistedFiles($pullRequestManagementMock, $this->configRepositoryMock);

        $this->assertTrue($whitelistedFiles->allowMerge($this->pullRequestData));
    }

    public function testDisAllowMerge()
    {
        $pullRequestManagementMock = $this->createMock(PullRequestManagement::class);
        $pullRequestManagementMock->method('getFiles')
            ->willReturn($this->fileResponse);

        $this->configMock->method('getValue')
            ->willReturn([
                'file1.txt' => ['modified'],
                'file2.txt' => ['modified', 'added'],
                'file3.txt' => ['modified', 'added']
            ]);

        $this->configRepositoryMock->method('getConfig')->willReturn($this->configMock);

        $whitelistedFiles = new WhitelistedFiles($pullRequestManagementMock, $this->configRepositoryMock);

        $this->assertFalse($whitelistedFiles->allowMerge($this->pullRequestData));
    }

    public function testDisAllowMergeNoFileConfig()
    {
        $pullRequestManagementMock = $this->createMock(PullRequestManagement::class);
        $pullRequestManagementMock->method('getFiles')
            ->willReturn($this->fileResponse);

        $this->configMock->method('getValue')
            ->willReturn(null);

        $this->configRepositoryMock->method('getConfig')->willReturn($this->configMock);

        $whitelistedFiles = new WhitelistedFiles($pullRequestManagementMock, $this->configRepositoryMock);

        $this->assertFalse($whitelistedFiles->allowMerge($this->pullRequestData));
    }
}
