<?php

declare(strict_types=1);

namespace Autoborna\ReportBundle\Tests\Model;

use Autoborna\CoreBundle\Templating\Helper\DateHelper;
use Autoborna\CoreBundle\Templating\Helper\FormatterHelper;
use Autoborna\ReportBundle\Model\ExcelExporter;
use Autoborna\ReportBundle\Tests\Fixtures;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\TranslatorInterface;

class ExcelExporterTest extends TestCase
{
    public function testExport(): void
    {
        $dateHelperMock   = $this->createMock(DateHelper::class);
        $translator       = $this->createMock(TranslatorInterface::class);
        $formatterHelper  = new FormatterHelper($dateHelperMock, $translator);
        $reportDataResult = Fixtures::getValidReportResultWithAggregatedColumns();
        $excelExporter    = new ExcelExporter($formatterHelper);

        $tmpFile = tempnam(sys_get_temp_dir(), 'autoborna_xlsx_export_test_');
        $excelExporter->export($reportDataResult, 'autoborna_xlsx_export_test', $tmpFile);

        /** @var Xlsx $objReader */
        $objReader   = IOFactory::createReader('Xlsx');
        $spreadsheet = $objReader->load($tmpFile);
        $result      = $spreadsheet->getActiveSheet()->toArray();

        $expected = [
            [
                'ID',
                'Name',
                'SUM Read',
                'COUNT Contact ID',
            ],
            [
                1,
                'Email 1',
                50,
                100,
            ],
            [
                2,
                'Email 2',
                10,
                60,
            ],
        ];

        $this->assertSame($expected, $result);

        if (file_exists($tmpFile)) {
            unlink($tmpFile);
        }
    }
}
