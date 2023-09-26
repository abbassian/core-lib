<?php

namespace Autoborna\ReportBundle\Tests\Scheduler\Factory;

use Autoborna\ReportBundle\Scheduler\Builder\SchedulerDailyBuilder;
use Autoborna\ReportBundle\Scheduler\Builder\SchedulerMonthBuilder;
use Autoborna\ReportBundle\Scheduler\Builder\SchedulerNowBuilder;
use Autoborna\ReportBundle\Scheduler\Builder\SchedulerWeeklyBuilder;
use Autoborna\ReportBundle\Scheduler\Entity\SchedulerEntity;
use Autoborna\ReportBundle\Scheduler\Enum\SchedulerEnum;
use Autoborna\ReportBundle\Scheduler\Exception\NotSupportedScheduleTypeException;
use Autoborna\ReportBundle\Scheduler\Factory\SchedulerTemplateFactory;

class SchedulerTemplateFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testNowBuilder()
    {
        $schedulerEntity          = new SchedulerEntity(true, SchedulerEnum::UNIT_NOW, null, null);
        $schedulerTemplateFactory = new SchedulerTemplateFactory();
        $builder                  = $schedulerTemplateFactory->getBuilder($schedulerEntity);

        $this->assertInstanceOf(SchedulerNowBuilder::class, $builder);
    }

    public function testDailyBuilder()
    {
        $schedulerEntity          = new SchedulerEntity(true, SchedulerEnum::UNIT_DAILY, null, null);
        $schedulerTemplateFactory = new SchedulerTemplateFactory();
        $builder                  = $schedulerTemplateFactory->getBuilder($schedulerEntity);

        $this->assertInstanceOf(SchedulerDailyBuilder::class, $builder);
    }

    public function testWeeklyBuilder()
    {
        $schedulerEntity          = new SchedulerEntity(true, SchedulerEnum::UNIT_WEEKLY, null, null);
        $schedulerTemplateFactory = new SchedulerTemplateFactory();
        $builder                  = $schedulerTemplateFactory->getBuilder($schedulerEntity);

        $this->assertInstanceOf(SchedulerWeeklyBuilder::class, $builder);
    }

    public function testMonthlyBuilder()
    {
        $schedulerEntity          = new SchedulerEntity(true, SchedulerEnum::UNIT_MONTHLY, null, null);
        $schedulerTemplateFactory = new SchedulerTemplateFactory();
        $builder                  = $schedulerTemplateFactory->getBuilder($schedulerEntity);

        $this->assertInstanceOf(SchedulerMonthBuilder::class, $builder);
    }

    public function testNotSupportedBuilder()
    {
        $schedulerEntity          = new SchedulerEntity(true, 'xx', null, null);
        $schedulerTemplateFactory = new SchedulerTemplateFactory();

        $this->expectException(NotSupportedScheduleTypeException::class);
        $schedulerTemplateFactory->getBuilder($schedulerEntity);
    }
}
