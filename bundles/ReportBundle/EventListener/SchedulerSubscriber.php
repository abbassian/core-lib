<?php

namespace Autoborna\ReportBundle\EventListener;

use Autoborna\ReportBundle\Event\ReportScheduleSendEvent;
use Autoborna\ReportBundle\ReportEvents;
use Autoborna\ReportBundle\Scheduler\Model\SendSchedule;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class SchedulerSubscriber.
 */
class SchedulerSubscriber implements EventSubscriberInterface
{
    /**
     * @var SendSchedule
     */
    private $sendSchedule;

    public function __construct(SendSchedule $sendSchedule)
    {
        $this->sendSchedule = $sendSchedule;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ReportEvents::REPORT_SCHEDULE_SEND => ['onScheduleSend', 0],
        ];
    }

    public function onScheduleSend(ReportScheduleSendEvent $event)
    {
        $scheduler = $event->getScheduler();
        $file      = $event->getFile();

        $this->sendSchedule->send($scheduler, $file);
    }
}
