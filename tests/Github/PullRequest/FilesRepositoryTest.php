<?php

namespace App\Tests\Github\PullRequest;

use App\Github\Adapter;
use App\Github\PullRequest\FilesRepository;
use PHPUnit\Framework\TestCase;

class FilesRepositoryTest extends TestCase
{
    public function testGet()
    {
        $resultA = ['files' => 'a'];
        $resultB = ['files' => 'b'];
        $adapterMock = $this->createMock(Adapter::class);
        $adapterMock->expects($this->at(0))
            ->method('get')
            ->willReturn($resultA);
        $adapterMock->expects($this->at(1))
            ->method('get')
            ->willReturn($resultB);

        $filesRepository = new FilesRepository($adapterMock);

        $this->assertEquals($resultA, $filesRepository->get('a'));
        $this->assertEquals($resultB, $filesRepository->get('b'));
        $this->assertEquals($resultA, $filesRepository->get('a'));
    }
}
