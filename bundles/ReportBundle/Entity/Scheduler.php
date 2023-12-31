<?php

namespace Autoborna\ReportBundle\Entity;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping as ORM;
use Autoborna\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;

/**
 * Class Scheduler.
 */
class Scheduler
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var Report
     */
    private $report;

    /**
     * @var \DateTimeInterface
     */
    private $scheduleDate;

    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('reports_schedulers')
            ->setCustomRepositoryClass(SchedulerRepository::class);

        $builder->addId();

        $builder->createManyToOne('report', Report::class)
            ->addJoinColumn('report_id', 'id', false, false, 'CASCADE')
            ->build();

        $builder->createField('scheduleDate', Type::DATETIME)
            ->columnName('schedule_date')
            ->nullable(false)
            ->build();
    }

    public function __construct(Report $report, \DateTimeInterface $scheduleDate)
    {
        $this->report       = $report;
        $this->scheduleDate = $scheduleDate;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Report
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getScheduleDate()
    {
        return $this->scheduleDate;
    }
}
