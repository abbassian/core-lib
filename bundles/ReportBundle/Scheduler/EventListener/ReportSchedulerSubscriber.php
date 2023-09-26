<?php

namespace Autoborna\ReportBundle\Scheduler\EventListener;

use Autoborna\ReportBundle\Event\ReportEvent;
use Autoborna\ReportBundle\ReportEvents;
use Autoborna\ReportBundle\Scheduler\Model\SchedulerPlanner;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ReportSchedulerSubscriber implements EventSubscriberInterface
{
    /**
     * @var SchedulerPlanner
     */
    private $schedulerPlanner;

    public function __construct(SchedulerPlanner $schedulerPlanner)
    {
        $this->schedulerPlanner = $schedulerPlanner;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [ReportEvents::REPORT_POST_SAVE => ['onReportSave', 0]];
    }

    public function onReportSave(ReportEvent $event)
    {
        $report = $event->getReport();

        $this->schedulerPlanner->computeScheduler($report);

        return $event;
    }
}
