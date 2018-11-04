<?php

namespace App\Tests\ActionHandler;

use App\ActionHandler\ConfigFactory;
use App\ActionHandler\Github\PullRequest\Opened\AutoMerge;
use App\ActionHandler\PermissionValidator;
use PHPUnit\Framework\TestCase;

class PermissionValidatorTest extends TestCase
{
    /**
     * @param $className
     * @param $config
     * @param $expected
     * @dataProvider isAllowActionDataProvider
     */
    public function testIsAllowAction($className, $config, $expected)
    {
        $validator = new PermissionValidator();
        $this->assertEquals($expected, $validator->isAllowAction($className, $config));
    }

    /**
     * @return array
     */
    public function isAllowActionDataProvider(): array
    {
        $configFactory = new ConfigFactory();
        return [
            [AutoMerge::class, $configFactory->create([
                'pullRequest' => [
                    'opened' => [
                        'autoMerge' => []
                    ]
                ],
            ]), true],
            [AutoMerge::class, $configFactory->create([
                'pullRequest' => [
                    'opened' => [
                        'autoMerge' => [
                            'mergeCondition' => [
                                'whitelistedFiles' => ['file1', 'file2']
                            ]
                        ]
                    ]
                ],
            ]), true],
            [AutoMerge::class, $configFactory->create([]), false],
        ];
    }
}
