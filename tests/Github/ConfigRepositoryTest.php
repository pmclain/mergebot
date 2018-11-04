<?php

namespace App\Tests\Github;

use App\ActionHandler\ConfigFactory;
use App\Github\Adapter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use App\Github\ConfigRepository;

class ConfigRepositoryTest extends TestCase
{
    /**
     * @var Adapter|MockObject
     */
    private $adapterMock;

    /**
     * @var ConfigFactory
     */
    private $configFactory;

    protected function setUp()
    {
        $this->adapterMock = $this->createMock(Adapter::class);
        $this->configFactory = new ConfigFactory();
    }

    public function testGetConfig()
    {
        $this->adapterMock->expects($this->once())->method('getRaw')->willReturn(
            file_get_contents(__DIR__ . '/../data/configs/config_1.yml')
        );

        $configRepository = new ConfigRepository($this->adapterMock, $this->configFactory);

        $config = $configRepository->getConfig('test', 'test', 'test');
        $this->assertEquals(null, $config->getValue('pullRequest/opened/autoMerge'));
        $this->assertTrue($config->hasValue('pullRequest/opened/autoMerge'));

        // Make sure the config is only requested from GH once
        $configRepository->getConfig('test', 'test', 'test');
    }
}
