<?php

namespace App\Tests\ActionHandler\Github\PullRequest\Opened\MergeCondition;

use App\ActionHandler\Github\PullRequest\Opened\MergeCondition\WhitelistedFiles;
use App\Github\PullRequestManagement;
use PHPUnit\Framework\TestCase;

class WhitelistedFilesTest extends TestCase
{
    /**
     * @var array
     */
    private $fileResponse;

    protected function setUp()
    {
        $this->fileResponse = json_decode(file_get_contents(__DIR__ . '/../../../../../data/github/pullrequest/files.json'), true);
    }

    public function testAllowMergeTooManyFiles()
    {
        $pullRequestManagementStub = $this->createMock(PullRequestManagement::class);
        $fileList = ['a.txt' => ['modified'], 'b.txt' => ['modified']];

        $whitelistedFiles = new WhitelistedFiles($pullRequestManagementStub, $fileList);

        $this->assertFalse($whitelistedFiles->allowMerge(['pull_request' => ['changed_files' => 3]]));
    }

    public function testAllowMerge()
    {
        $pullRequestManagementMock = $this->createMock(PullRequestManagement::class);
        $pullRequestManagementMock->method('getFiles')
            ->willReturn($this->fileResponse);

        $fileList = [
            'file1.txt' => ['modified', 'added'],
            'file2.txt' => ['modified', 'added'],
            'file3.txt' => ['modified', 'added']
        ];

        $whitelistedFiles = new WhitelistedFiles($pullRequestManagementMock, $fileList);

        $this->assertTrue($whitelistedFiles->allowMerge(['pull_request' => ['changed_files' => 3]]));
    }

    public function testDisAllowMerge()
    {
        $pullRequestManagementMock = $this->createMock(PullRequestManagement::class);
        $pullRequestManagementMock->method('getFiles')
            ->willReturn($this->fileResponse);

        $fileList = [
            'file1.txt' => ['modified'],
            'file2.txt' => ['modified', 'added'],
            'file3.txt' => ['modified', 'added']
        ];

        $whitelistedFiles = new WhitelistedFiles($pullRequestManagementMock, $fileList);

        $this->assertFalse($whitelistedFiles->allowMerge(['pull_request' => ['changed_files' => 3]]));
    }
}
