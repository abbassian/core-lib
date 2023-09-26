<?php

namespace Autoborna\ReportBundle\Model;

use Autoborna\CoreBundle\Helper\InputHelper;
use Autoborna\ReportBundle\Crate\ReportDataResult;
use Autoborna\ReportBundle\Entity\Scheduler;
use Autoborna\ReportBundle\Exception\FileIOException;

class ReportFileWriter
{
    /**
     * @var CsvExporter
     */
    private $csvExporter;

    /**
     * @var ExportHandler
     */
    private $exportHandler;

    public function __construct(CsvExporter $csvExporter, ExportHandler $exportHandler)
    {
        $this->csvExporter   = $csvExporter;
        $this->exportHandler = $exportHandler;
    }

    /**
     * @throws FileIOException
     */
    public function writeReportData(Scheduler $scheduler, ReportDataResult $reportDataResult, ReportExportOptions $reportExportOptions)
    {
        $fileName = $this->getFileName($scheduler);
        $handler  = $this->exportHandler->getHandler($fileName);
        $this->csvExporter->export($reportDataResult, $handler, $reportExportOptions->getPage());
        $this->exportHandler->closeHandler($handler);
    }

    public function clear(Scheduler $scheduler)
    {
        $fileName = $this->getFileName($scheduler);
        $this->exportHandler->removeFile($fileName);
    }

    /**
     * @return string
     *
     * @throws FileIOException
     */
    public function getFilePath(Scheduler $scheduler)
    {
        $fileName = $this->getFileName($scheduler);

        return $this->exportHandler->getPath($fileName);
    }

    /**
     * @return string
     */
    private function getFileName(Scheduler $scheduler)
    {
        $date       = $scheduler->getScheduleDate();
        $dateString = $date->format('Y-m-d');
        $reportName = $scheduler->getReport()->getName();

        return $dateString.'_'.InputHelper::alphanum($reportName, false, '-');
    }
}
