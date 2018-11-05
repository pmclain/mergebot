<?php

namespace App\Tests\Github;

use App\Exception\HttpResponseException;
use App\Github\Adapter;
use App\Github\ReleaseRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ReleaseRepositoryTest extends TestCase
{
    /**
     * @var Adapter|MockObject
     */
    private $adapterMock;

    protected function setUp()
    {
        $this->adapterMock = $this->createMock(Adapter::class);
    }

    public function testGetLatest()
    {
        $latest = ['latest' => 'release'];
        $this->adapterMock->method('get')->willReturn($latest);
        $releaseRepository = new ReleaseRepository($this->adapterMock);

        $this->assertEquals($latest, $releaseRepository->getLatest('tests', 'test'));
    }

    public function testGetLatestNoneFound()
    {
        $this->adapterMock->method('get')
            ->willThrowException(new HttpResponseException(''));

        $releaseRepository = new ReleaseRepository($this->adapterMock);

        $this->assertNull($releaseRepository->getLatest('oh', 'no'));
    }
}
