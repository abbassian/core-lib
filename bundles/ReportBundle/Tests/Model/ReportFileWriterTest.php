<?php

namespace Autoborna\ReportBundle\Tests\Model;

use Autoborna\CoreBundle\Helper\CoreParametersHelper;
use Autoborna\ReportBundle\Crate\ReportDataResult;
use Autoborna\ReportBundle\Entity\Report;
use Autoborna\ReportBundle\Entity\Scheduler;
use Autoborna\ReportBundle\Model\CsvExporter;
use Autoborna\ReportBundle\Model\ExportHandler;
use Autoborna\ReportBundle\Model\ReportExportOptions;
use Autoborna\ReportBundle\Model\ReportFileWriter;
use Autoborna\ReportBundle\Tests\Fixtures;

class ReportFileWriterTest extends \PHPUnit\Framework\TestCase
{
    public function testWriteReportData()
    {
        $csvExporter = $this->getMockBuilder(CsvExporter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $exportHandler = $this->getMockBuilder(ExportHandler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $handler = 'Handler';

        $report    = new Report();
        $scheduler = new Scheduler($report, new \DateTime());

        $reportDataResult = new ReportDataResult(Fixtures::getValidReportResult());

        $coreParametersHelper = $this->getMockBuilder(CoreParametersHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $coreParametersHelper->expects($this->once())
            ->method('get')
            ->with('report_export_batch_size')
            ->willReturn(3);

        $reportExportOptions = new ReportExportOptions($coreParametersHelper);

        $exportHandler->expects($this->once())
            ->method('getHandler')
            ->willReturn($handler);

        $csvExporter->expects($this->once())
            ->method('export')
            ->with($reportDataResult, $handler, 1)
            ->willReturn($handler);

        $exportHandler->expects($this->once())
            ->method('closeHandler')
            ->willReturn($handler);

        $reportFileWriter = new ReportFileWriter($csvExporter, $exportHandler);

        $reportFileWriter->writeReportData($scheduler, $reportDataResult, $reportExportOptions);
    }

    public function testClear()
    {
        $csvExporter = $this->getMockBuilder(CsvExporter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $exportHandler = $this->getMockBuilder(ExportHandler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $report    = new Report();
        $scheduler = new Scheduler($report, new \DateTime());

        $exportHandler->expects($this->once())
            ->method('removeFile');

        $reportFileWriter = new ReportFileWriter($csvExporter, $exportHandler);

        $reportFileWriter->clear($scheduler);
    }

    public function testGetFilePath()
    {
        $csvExporter = $this->getMockBuilder(CsvExporter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $exportHandler = $this->getMockBuilder(ExportHandler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $report    = new Report();
        $scheduler = new Scheduler($report, new \DateTime());

        $exportHandler->expects($this->once())
            ->method('getPath');

        $reportFileWriter = new ReportFileWriter($csvExporter, $exportHandler);

        $reportFileWriter->getFilePath($scheduler);
    }
}
