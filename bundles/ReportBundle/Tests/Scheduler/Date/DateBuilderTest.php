<?php

namespace Autoborna\ReportBundle\Tests\Scheduler\Date;

use Autoborna\ReportBundle\Scheduler\Builder\SchedulerBuilder;
use Autoborna\ReportBundle\Scheduler\Date\DateBuilder;
use Autoborna\ReportBundle\Scheduler\Entity\SchedulerEntity;
use Autoborna\ReportBundle\Scheduler\Enum\SchedulerEnum;
use Autoborna\ReportBundle\Scheduler\Exception\InvalidSchedulerException;
use Autoborna\ReportBundle\Scheduler\Exception\NoScheduleException;
use Autoborna\ReportBundle\Scheduler\Exception\NotSupportedScheduleTypeException;
use Autoborna\ReportBundle\Scheduler\Factory\SchedulerTemplateFactory;
use PHPUnit\Framework\MockObject\MockObject;

class DateBuilderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var MockObject|SchedulerBuilder
     */
    private $schedulerBuilder;

    /**
     * @var DateBuilder
     */
    private $dateBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->schedulerBuilder = $this->createMock(SchedulerBuilder::class);
        $this->dateBuilder      = new DateBuilder($this->schedulerBuilder);
    }

    public function testGetNextEvent(): void
    {
        $schedulerTemplateFactory = new SchedulerTemplateFactory();
        $schedulerBuilder         = new SchedulerBuilder($schedulerTemplateFactory);
        $dateBuilder              = new DateBuilder($schedulerBuilder);
        $schedulerEntity          = new SchedulerEntity(true, SchedulerEnum::UNIT_DAILY, null, null);
        $date                     = $dateBuilder->getNextEvent($schedulerEntity);
        $expectedDate             = (new \DateTime())->setTime(0, 0)->modify('+1 day');

        $this->assertEquals($expectedDate, $date);
    }

    public function testInvalidScheduler(): void
    {
        $schedulerEntity = new SchedulerEntity(true, SchedulerEnum::UNIT_DAILY, null, null);

        $this->schedulerBuilder->expects($this->once())
            ->method('getNextEvent')
            ->with($schedulerEntity)
            ->willThrowException(new InvalidSchedulerException());

        $this->expectException(NoScheduleException::class);

        $this->dateBuilder->getNextEvent($schedulerEntity);
    }

    public function testSchedulerNotSupported(): void
    {
        $schedulerEntity = new SchedulerEntity(true, SchedulerEnum::UNIT_DAILY, null, null);

        $this->schedulerBuilder->expects($this->once())
            ->method('getNextEvent')
            ->with($schedulerEntity)
            ->willThrowException(new NotSupportedScheduleTypeException());

        $this->expectException(NoScheduleException::class);

        $this->dateBuilder->getNextEvent($schedulerEntity);
    }

    public function testNoResult(): void
    {
        $schedulerEntity = new SchedulerEntity(true, SchedulerEnum::UNIT_DAILY, null, null);

        $this->schedulerBuilder->expects($this->once())
            ->method('getNextEvent')
            ->with($schedulerEntity)
            ->willReturn([]);

        $this->expectException(NoScheduleException::class);

        $this->dateBuilder->getNextEvent($schedulerEntity);
    }

    public function testGetPreviewDaysForNow(): void
    {
        $this->schedulerBuilder->expects($this->once())
            ->method('getNextEvents')
            ->with($this->isInstanceOf(SchedulerEntity::class), 1)
            ->willReturn([]);

        $this->dateBuilder->getPreviewDays(true, SchedulerEnum::UNIT_NOW, '', '');
    }

    public function testGetPreviewDaysForMonths(): void
    {
        $this->schedulerBuilder->expects($this->once())
            ->method('getNextEvents')
            ->with($this->isInstanceOf(SchedulerEntity::class), 10)
            ->willReturn([]);

        $this->dateBuilder->getPreviewDays(true, SchedulerEnum::UNIT_MONTHLY, 'MON', '1');
    }
}
