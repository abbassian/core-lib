<?php

namespace Autoborna\ReportBundle\Tests\Adapter;

use Autoborna\CoreBundle\Helper\CoreParametersHelper;
use Autoborna\ReportBundle\Adapter\ReportDataAdapter;
use Autoborna\ReportBundle\Entity\Report;
use Autoborna\ReportBundle\Model\ReportExportOptions;
use Autoborna\ReportBundle\Model\ReportModel;
use Autoborna\ReportBundle\Tests\Fixtures;

class ReportDataAdapterTest extends \PHPUnit\Framework\TestCase
{
    public function testNoEmailsProvided()
    {
        $reportModelMock = $this->getMockBuilder(ReportModel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $coreParametersHelperMock = $this->getMockBuilder(CoreParametersHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $coreParametersHelperMock->expects($this->once())
            ->method('get')
            ->with('report_export_batch_size')
            ->willReturn(11);

        $reportDataAdapter = new ReportDataAdapter($reportModelMock);

        $report              = new Report();
        $reportExportOptions = new ReportExportOptions($coreParametersHelperMock);

        $options = [
            'paginate'        => true,
            'limit'           => 11,
            'ignoreGraphData' => true,
            'page'            => 1,
            'dateTo'          => null,
            'dateFrom'        => null,
        ];

        $reportModelMock->expects($this->once())
            ->method('getReportData')
            ->with($report, null, $options)
            ->willReturn(Fixtures::getValidReportResult());

        $result = $reportDataAdapter->getReportData($report, $reportExportOptions);

        $this->assertSame(Fixtures::getValidReportData(), $result->getData());
        $this->assertSame(Fixtures::getValidReportHeaders(), $result->getHeaders());
        $this->assertSame(Fixtures::getValidReportTotalResult(), $result->getTotalResults());
        $this->assertSame(Fixtures::getStringType(), $result->getType('city'));
        $this->assertSame(Fixtures::getDateType(), $result->getType('date_identified'));
        $this->assertSame(Fixtures::getEmailType(), $result->getType('email'));
    }
}
