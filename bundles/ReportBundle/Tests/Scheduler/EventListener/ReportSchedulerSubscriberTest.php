<?php

namespace Autoborna\ReportBundle\Tests\Scheduler\EventListener;

use Autoborna\ReportBundle\Entity\Report;
use Autoborna\ReportBundle\Event\ReportEvent;
use Autoborna\ReportBundle\Scheduler\EventListener\ReportSchedulerSubscriber;
use Autoborna\ReportBundle\Scheduler\Model\SchedulerPlanner;

class ReportSchedulerSubscriberTest extends \PHPUnit\Framework\TestCase
{
    public function testOnReportSave()
    {
        $report = new Report();
        $event  = new ReportEvent($report);

        $schedulerPlanner = $this->getMockBuilder(SchedulerPlanner::class)
            ->disableOriginalConstructor()
            ->getMock();

        $schedulerPlanner->expects($this->once())
            ->method('computeScheduler')
            ->with($report);

        $reportSchedulerSubscriber = new ReportSchedulerSubscriber($schedulerPlanner);
        $reportSchedulerSubscriber->onReportSave($event);
    }
}
