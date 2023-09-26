<?php

namespace Autoborna\ReportBundle\Scheduler\Date;

use Autoborna\ReportBundle\Scheduler\Builder\SchedulerBuilder;
use Autoborna\ReportBundle\Scheduler\Entity\SchedulerEntity;
use Autoborna\ReportBundle\Scheduler\Exception\InvalidSchedulerException;
use Autoborna\ReportBundle\Scheduler\Exception\NoScheduleException;
use Autoborna\ReportBundle\Scheduler\Exception\NotSupportedScheduleTypeException;
use Autoborna\ReportBundle\Scheduler\SchedulerInterface;

class DateBuilder
{
    /**
     * @var SchedulerBuilder
     */
    private $schedulerBuilder;

    public function __construct(SchedulerBuilder $schedulerBuilder)
    {
        $this->schedulerBuilder = $schedulerBuilder;
    }

    /**
     * @param bool   $isScheduled
     * @param string $scheduleUnit
     * @param string $scheduleDay
     * @param string $scheduleMonthFrequency
     *
     * @return array
     */
    public function getPreviewDays($isScheduled, $scheduleUnit, $scheduleDay, $scheduleMonthFrequency)
    {
        $entity = new SchedulerEntity($isScheduled, $scheduleUnit, $scheduleDay, $scheduleMonthFrequency);
        $count  = $entity->isScheduledNow() ? 1 : 10;

        try {
            $recurrences = $this->schedulerBuilder->getNextEvents($entity, $count);
        } catch (InvalidSchedulerException $e) {
            return [];
        } catch (NotSupportedScheduleTypeException $e) {
            return [];
        }

        $dates = [];
        foreach ($recurrences as $recurrence) {
            $dates[] = $recurrence->getStart();
        }

        return $dates;
    }

    /**
     * @return \DateTimeInterface
     *
     * @throws NoScheduleException
     */
    public function getNextEvent(SchedulerInterface $scheduler)
    {
        try {
            $recurrences = $this->schedulerBuilder->getNextEvent($scheduler);
        } catch (InvalidSchedulerException $e) {
            throw new NoScheduleException();
        } catch (NotSupportedScheduleTypeException $e) {
            throw new NoScheduleException();
        }

        if (empty($recurrences[0])) {
            throw new NoScheduleException();
        }

        return $recurrences[0]->getStart();
    }
}
