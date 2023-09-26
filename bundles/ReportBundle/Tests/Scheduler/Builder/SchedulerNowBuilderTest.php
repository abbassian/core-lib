<?php

declare(strict_types=1);

namespace Autoborna\ReportBundle\Tests\Scheduler\Builder;

use Autoborna\ReportBundle\Scheduler\Builder\SchedulerNowBuilder;
use Autoborna\ReportBundle\Scheduler\Entity\SchedulerEntity;
use Autoborna\ReportBundle\Scheduler\Enum\SchedulerEnum;
use Autoborna\ReportBundle\Scheduler\Exception\InvalidSchedulerException;
use Recurr\Exception\InvalidArgument;
use Recurr\Rule;

class SchedulerNowBuilderTest extends \PHPUnit\Framework\TestCase
{
    public function testBuilEvent(): void
    {
        $schedulerNowBuilder = new SchedulerNowBuilder();
        $schedulerEntity     = new SchedulerEntity(true, SchedulerEnum::UNIT_NOW, null, null);
        $startDate           = new \DateTime();
        $rule                = new Rule();

        $rule->setStartDate($startDate)
            ->setCount(1);

        $schedulerNowBuilder->build($rule, $schedulerEntity);

        $this->assertEquals(Rule::$freqs['SECONDLY'], $rule->getFreq());
    }

    public function testBuilEventFails(): void
    {
        $schedulerNowBuilder = new SchedulerNowBuilder();
        $schedulerEntity     = new SchedulerEntity(true, SchedulerEnum::UNIT_NOW, null, null);
        $rule                = $this->createMock(Rule::class);

        $rule->expects($this->once())
            ->method('setFreq')
            ->with('SECONDLY')
            ->willThrowException(new InvalidArgument());

        $this->expectException(InvalidSchedulerException::class);

        $schedulerNowBuilder->build($rule, $schedulerEntity);
    }
}
