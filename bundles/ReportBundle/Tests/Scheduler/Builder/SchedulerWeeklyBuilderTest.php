<?php

namespace Autoborna\ReportBundle\Tests\Scheduler\Builder;

use Autoborna\ReportBundle\Scheduler\Builder\SchedulerWeeklyBuilder;
use Autoborna\ReportBundle\Scheduler\Entity\SchedulerEntity;
use Autoborna\ReportBundle\Scheduler\Enum\SchedulerEnum;
use Autoborna\ReportBundle\Scheduler\Exception\InvalidSchedulerException;
use Recurr\Exception\InvalidArgument;
use Recurr\Rule;

class SchedulerWeeklyBuilderTest extends \PHPUnit\Framework\TestCase
{
    public function testBuilEvent()
    {
        $schedulerDailyBuilder = new SchedulerWeeklyBuilder();

        $schedulerEntity = new SchedulerEntity(true, SchedulerEnum::UNIT_DAILY, null, null);

        $startDate = (new \DateTime())->setTime(0, 0)->modify('+1 day');
        $rule      = new Rule();
        $rule->setStartDate($startDate)
            ->setCount(1);

        $schedulerDailyBuilder->build($rule, $schedulerEntity);

        $this->assertEquals(Rule::$freqs['WEEKLY'], $rule->getFreq());
    }

    public function testBuilEventFails()
    {
        $schedulerDailyBuilder = new SchedulerWeeklyBuilder();

        $schedulerEntity = new SchedulerEntity(true, SchedulerEnum::UNIT_DAILY, null, null);

        $rule = $this->getMockBuilder(Rule::class)
            ->disableOriginalConstructor()
            ->getMock();

        $rule->expects($this->once())
            ->method('setFreq')
            ->with('WEEKLY')
            ->willThrowException(new InvalidArgument());

        $this->expectException(InvalidSchedulerException::class);

        $schedulerDailyBuilder->build($rule, $schedulerEntity);
    }
}
