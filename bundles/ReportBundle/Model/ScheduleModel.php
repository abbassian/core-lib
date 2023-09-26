<?php

namespace Autoborna\ReportBundle\Model;

use Doctrine\ORM\EntityManager;
use Autoborna\ReportBundle\Entity\Report;
use Autoborna\ReportBundle\Entity\Scheduler;
use Autoborna\ReportBundle\Entity\SchedulerRepository;
use Autoborna\ReportBundle\Scheduler\Model\SchedulerPlanner;
use Autoborna\ReportBundle\Scheduler\Option\ExportOption;

class ScheduleModel
{
    /**
     * @var SchedulerRepository
     */
    private $schedulerRepository;

    /**
     * @var SchedulerPlanner
     */
    private $schedulerPlanner;

    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager, SchedulerPlanner $schedulerPlanner)
    {
        $this->entityManager       = $entityManager;
        $this->schedulerRepository = $entityManager->getRepository(Scheduler::class);
        $this->schedulerPlanner    = $schedulerPlanner;
    }

    /**
     * @return Scheduler[]
     */
    public function getScheduledReportsForExport(ExportOption $exportOption)
    {
        return $this->schedulerRepository->getScheduledReportsForExport($exportOption);
    }

    public function reportWasScheduled(Report $report)
    {
        $this->schedulerPlanner->computeScheduler($report);
    }

    public function turnOffScheduler(Report $report): void
    {
        $report->setIsScheduled(false);
        $this->entityManager->persist($report);
        $this->entityManager->flush();
    }
}
