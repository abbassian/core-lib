<?php

namespace Autoborna\ReportBundle\Scheduler\Model;

use Doctrine\ORM\EntityManager;
use Autoborna\ReportBundle\Entity\Report;
use Autoborna\ReportBundle\Entity\Scheduler;
use Autoborna\ReportBundle\Entity\SchedulerRepository;
use Autoborna\ReportBundle\Scheduler\Date\DateBuilder;
use Autoborna\ReportBundle\Scheduler\Exception\NoScheduleException;

class SchedulerPlanner
{
    /**
     * @var DateBuilder
     */
    private $dateBuilder;

    /**
     * @var SchedulerRepository
     */
    private $schedulerRepository;

    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(DateBuilder $dateBuilder, EntityManager $entityManager)
    {
        $this->dateBuilder         = $dateBuilder;
        $this->entityManager       = $entityManager;
        $this->schedulerRepository = $entityManager->getRepository(Scheduler::class);
    }

    public function computeScheduler(Report $report)
    {
        $this->removeSchedulerOfReport($report);
        $this->planScheduler($report);
    }

    private function planScheduler(Report $report)
    {
        try {
            $date = $this->dateBuilder->getNextEvent($report);
        } catch (NoScheduleException $e) {
            return;
        }

        $scheduler = new Scheduler($report, $date);
        $this->entityManager->persist($scheduler);
        $this->entityManager->flush();
    }

    private function removeSchedulerOfReport(Report $report)
    {
        $scheduler = $this->schedulerRepository->getSchedulerByReport($report);
        if (!$scheduler) {
            return;
        }

        $this->entityManager->remove($scheduler);
        $this->entityManager->flush();
    }
}
