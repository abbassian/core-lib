<?php

namespace Autoborna\ReportBundle\Scheduler\Factory;

use Autoborna\ReportBundle\Scheduler\Builder\SchedulerDailyBuilder;
use Autoborna\ReportBundle\Scheduler\Builder\SchedulerMonthBuilder;
use Autoborna\ReportBundle\Scheduler\Builder\SchedulerNowBuilder;
use Autoborna\ReportBundle\Scheduler\Builder\SchedulerWeeklyBuilder;
use Autoborna\ReportBundle\Scheduler\BuilderInterface;
use Autoborna\ReportBundle\Scheduler\Exception\NotSupportedScheduleTypeException;
use Autoborna\ReportBundle\Scheduler\SchedulerInterface;

class SchedulerTemplateFactory
{
    /**
     * @return BuilderInterface
     *
     * @throws NotSupportedScheduleTypeException
     */
    public function getBuilder(SchedulerInterface $scheduler)
    {
        if ($scheduler->isScheduledNow()) {
            return new SchedulerNowBuilder();
        }
        if ($scheduler->isScheduledDaily()) {
            return new SchedulerDailyBuilder();
        }
        if ($scheduler->isScheduledWeekly()) {
            return new SchedulerWeeklyBuilder();
        }
        if ($scheduler->isScheduledMonthly()) {
            return new SchedulerMonthBuilder();
        }

        throw new NotSupportedScheduleTypeException();
    }
}
