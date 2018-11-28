<?php
declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\EventFactory;
use App\Entity\EventRecorder;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class EventRecorderTest extends TestCase
{
    public function testRecord()
    {
        $eventFactory = new EventFactory();
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $eventRecorder = new EventRecorder(
            $entityManagerMock,
            $eventFactory
        );

        $expected = $eventFactory->create();
        $expected->setTaskName('test')
            ->setEventData(['test' => 'data']);

        $result = $eventRecorder->record($expected->getTaskName(), $expected->getEventData());

        $this->assertEquals($expected, $result);
    }
}
