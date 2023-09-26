<?php

namespace Autoborna\ReportBundle\Tests\Model;

use Doctrine\ORM\EntityManager;
use Autoborna\ReportBundle\Entity\Report;
use Autoborna\ReportBundle\Entity\Scheduler;
use Autoborna\ReportBundle\Entity\SchedulerRepository;
use Autoborna\ReportBundle\Model\ScheduleModel;
use Autoborna\ReportBundle\Scheduler\Model\SchedulerPlanner;
use Autoborna\ReportBundle\Scheduler\Option\ExportOption;
use PHPUnit\Framework\MockObject\MockObject;

class ScheduleModelTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var MockObject|SchedulerRepository
     */
    private $schedulerRepository;

    /**
     * @var MockObject|EntityManager
     */
    private $entityManager;

    /**
     * @var MockObject|SchedulerPlanner
     */
    private $schedulerPlanner;

    /**
     * @var MockObject|ExportOption
     */
    private $exportOption;

    /**
     * @var ScheduleModel
     */
    private $scheduleModel;

    protected function setUp(): void
    {
        $this->schedulerRepository = $this->createMock(SchedulerRepository::class);
        $this->entityManager       = $this->createMock(EntityManager::class);
        $this->schedulerPlanner    = $this->createMock(SchedulerPlanner::class);
        $this->exportOption        = $this->createMock(ExportOption::class);

        $this->entityManager->expects($this->once())
            ->method('getRepository')
            ->with(Scheduler::class)
            ->willReturn($this->schedulerRepository);

        $this->scheduleModel = new ScheduleModel($this->entityManager, $this->schedulerPlanner);
    }

    public function testGetScheduledReportsForExport()
    {
        $this->schedulerRepository->expects($this->once())
            ->method('getScheduledReportsForExport')
            ->with($this->exportOption);

        $this->scheduleModel->getScheduledReportsForExport($this->exportOption);
    }

    public function testReportWasScheduled()
    {
        $report = new Report();

        $this->schedulerPlanner->expects($this->once())
            ->method('computeScheduler')
            ->with($report);

        $this->scheduleModel->reportWasScheduled($report);
    }

    public function testTurnOffScheduler()
    {
        $report = new Report();

        $report->setIsScheduled(true);

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($report);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->scheduleModel->turnOffScheduler($report);

        $this->assertFalse($report->isScheduled());
    }
}
