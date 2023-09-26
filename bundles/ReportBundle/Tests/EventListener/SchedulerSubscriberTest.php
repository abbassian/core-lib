<?php

namespace Autoborna\ReportBundle\Tests\EventListener;

use Autoborna\ReportBundle\Entity\Report;
use Autoborna\ReportBundle\Entity\Scheduler;
use Autoborna\ReportBundle\Event\ReportScheduleSendEvent;
use Autoborna\ReportBundle\EventListener\SchedulerSubscriber;
use Autoborna\ReportBundle\Scheduler\Model\SendSchedule;

class SchedulerSubscriberTest extends \PHPUnit\Framework\TestCase
{
    public function testNoEmailsProvided()
    {
        $sendScheduleMock = $this->getMockBuilder(SendSchedule::class)
            ->disableOriginalConstructor()
            ->getMock();

        $schedulerSubscriber = new SchedulerSubscriber($sendScheduleMock);

        $report                  = new Report();
        $date                    = new \DateTime();
        $scheduler               = new Scheduler($report, $date);
        $file                    = 'path-to-a-file';
        $reportScheduleSendEvent = new ReportScheduleSendEvent($scheduler, $file);

        $sendScheduleMock->expects($this->once())
            ->method('send')
            ->with($scheduler, $file);

        $schedulerSubscriber->onScheduleSend($reportScheduleSendEvent);
    }
}
