<?php

namespace Autoborna\ReportBundle\Tests\Crate;

use Autoborna\ReportBundle\Crate\ReportDataResult;
use Autoborna\ReportBundle\Tests\Fixtures;

class ReportDataResultTest extends \PHPUnit\Framework\TestCase
{
    public function testValidData()
    {
        $reportDataResult = new ReportDataResult(Fixtures::getValidReportResult());

        $this->assertSame(Fixtures::getValidReportData(), $reportDataResult->getData());
        $this->assertSame(Fixtures::getValidReportHeaders(), $reportDataResult->getHeaders());
        $this->assertSame(Fixtures::getValidReportTotalResult(), $reportDataResult->getTotalResults());
        $this->assertSame(Fixtures::getStringType(), $reportDataResult->getType('city'));
        $this->assertSame(Fixtures::getDateType(), $reportDataResult->getType('date_identified'));
        $this->assertSame(Fixtures::getEmailType(), $reportDataResult->getType('email'));
    }

    public function testNoDataProvided()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Keys 'data', 'dataColumns' and 'columns' have to be provided");

        $data = Fixtures::getValidReportResult();
        unset($data['data']);
        new ReportDataResult($data);
    }

    public function testNoDataColumnProvided()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Keys 'data', 'dataColumns' and 'columns' have to be provided");

        $data = Fixtures::getValidReportResult();
        unset($data['dataColumns']);
        new ReportDataResult($data);
    }

    public function testNoColumnProvided()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Keys 'data', 'dataColumns' and 'columns' have to be provided");

        $data = Fixtures::getValidReportResult();
        unset($data['columns']);
        new ReportDataResult($data);
    }
}
