<?php

namespace Autoborna\ReportBundle\Tests\Model;

use Autoborna\CoreBundle\Helper\CoreParametersHelper;
use Autoborna\ReportBundle\Model\ReportExportOptions;

class ReportExportOptionsTest extends \PHPUnit\Framework\TestCase
{
    public function testBatch()
    {
        $coreParametersHelper = $this->getMockBuilder(CoreParametersHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $coreParametersHelper->expects($this->once())
            ->method('get')
            ->with('report_export_batch_size')
            ->willReturn(3);

        $reportExportOptions = new ReportExportOptions($coreParametersHelper);

        $this->assertSame(1, $reportExportOptions->getPage());
        $this->assertSame(3, $reportExportOptions->getBatchSize());

        $reportExportOptions->beginExport();
        $this->assertSame(1, $reportExportOptions->getPage());
        $this->assertSame(3, $reportExportOptions->getNumberOfProcessedResults());

        $reportExportOptions->nextBatch();
        $this->assertSame(2, $reportExportOptions->getPage());
        $this->assertSame(6, $reportExportOptions->getNumberOfProcessedResults());

        $reportExportOptions->nextBatch();
        $this->assertSame(3, $reportExportOptions->getPage());
        $this->assertSame(9, $reportExportOptions->getNumberOfProcessedResults());
    }
}
