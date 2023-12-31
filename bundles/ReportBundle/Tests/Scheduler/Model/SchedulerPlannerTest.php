<?php

namespace Autoborna\ReportBundle\Tests\Scheduler\Model;

use Doctrine\ORM\EntityManager;
use Autoborna\ReportBundle\Entity\Report;
use Autoborna\ReportBundle\Entity\Scheduler;
use Autoborna\ReportBundle\Entity\SchedulerRepository;
use Autoborna\ReportBundle\Scheduler\Date\DateBuilder;
use Autoborna\ReportBundle\Scheduler\Exception\NoScheduleException;
use Autoborna\ReportBundle\Scheduler\Model\SchedulerPlanner;

class SchedulerPlannerTest extends \PHPUnit\Framework\TestCase
{
    public function testComputeSchedule()
    {
        $dateBuilder = $this->getMockBuilder(DateBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $schedulerRepository = $this->getMockBuilder(SchedulerRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $entityManager = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $entityManager->expects($this->once())
            ->method('getRepository')
            ->with(Scheduler::class)
            ->willReturn($schedulerRepository);

        $schedulerPlanner = new SchedulerPlanner($dateBuilder, $entityManager);

        $report = new Report();

        $oldScheduler = new Scheduler($report, new \DateTime());

        $schedulerRepository->expects($this->once())
            ->method('getSchedulerByReport')
            ->with($report)
            ->willReturn($oldScheduler);

        $entityManager->expects($this->once())
            ->method('remove')
            ->with($oldScheduler);

        $entityManager->expects($this->exactly(2))
            ->method('flush')
            ->with();

        $dateOfNextSchedule = new \DateTime();

        $dateBuilder->expects($this->once())
            ->method('getNextEvent')
            ->with($report)
            ->willReturn($dateOfNextSchedule);

        $entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($scheduler) use ($report, $dateOfNextSchedule) {
                return
                    $scheduler instanceof Scheduler &&
                    $scheduler->getReport() === $report &&
                    $scheduler->getScheduleDate() === $dateOfNextSchedule;
            }));

        $schedulerPlanner->computeScheduler($report);
    }

    public function testNoSchedule()
    {
        $dateBuilder = $this->getMockBuilder(DateBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $schedulerRepository = $this->getMockBuilder(SchedulerRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $entityManager = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $entityManager->expects($this->once())
            ->method('getRepository')
            ->with(Scheduler::class)
            ->willReturn($schedulerRepository);

        $schedulerPlanner = new SchedulerPlanner($dateBuilder, $entityManager);

        $report = new Report();

        $oldScheduler = new Scheduler($report, new \DateTime());

        $schedulerRepository->expects($this->once())
            ->method('getSchedulerByReport')
            ->with($report)
            ->willReturn($oldScheduler);

        $entityManager->expects($this->once())
            ->method('remove')
            ->with($oldScheduler);

        $entityManager->expects($this->once())
            ->method('flush')
            ->with();

        $dateBuilder->expects($this->once())
            ->method('getNextEvent')
            ->with($report)
            ->willThrowException(new NoScheduleException());

        $schedulerPlanner->computeScheduler($report);
    }

    public function testNoRemoveNoSchedule()
    {
        $dateBuilder = $this->getMockBuilder(DateBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $schedulerRepository = $this->getMockBuilder(SchedulerRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $entityManager = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $entityManager->expects($this->once())
            ->method('getRepository')
            ->with(Scheduler::class)
            ->willReturn($schedulerRepository);

        $schedulerPlanner = new SchedulerPlanner($dateBuilder, $entityManager);

        $report = new Report();

        $schedulerRepository->expects($this->once())
            ->method('getSchedulerByReport')
            ->with($report)
            ->willReturn(null);

        $dateBuilder->expects($this->once())
            ->method('getNextEvent')
            ->with($report)
            ->willThrowException(new NoScheduleException());

        $schedulerPlanner->computeScheduler($report);
    }
}
