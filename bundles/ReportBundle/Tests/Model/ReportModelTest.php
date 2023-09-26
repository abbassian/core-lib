<?php

namespace Autoborna\ReportBundle\Tests\Model;

use Autoborna\ChannelBundle\Helper\ChannelListHelper;
use Autoborna\CoreBundle\Helper\CoreParametersHelper;
use Autoborna\CoreBundle\Helper\TemplatingHelper;
use Autoborna\LeadBundle\Model\FieldModel;
use Autoborna\ReportBundle\Event\ReportBuilderEvent;
use Autoborna\ReportBundle\Helper\ReportHelper;
use Autoborna\ReportBundle\Model\CsvExporter;
use Autoborna\ReportBundle\Model\ExcelExporter;
use Autoborna\ReportBundle\Model\ReportModel;
use Autoborna\ReportBundle\Tests\Fixtures;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Translation\Translator;

class ReportModelTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ReportModel
     */
    private $reportModel;

    protected function setUp(): void
    {
        $fieldModelMock = $this->createMock(FieldModel::class);
        $fieldModelMock->method('getPublishedFieldArrays')->willReturn([]);

        $this->reportModel = new ReportModel(
            $this->createMock(CoreParametersHelper::class),
            $this->createMock(TemplatingHelper::class),
            $this->createMock(ChannelListHelper::class),
            $fieldModelMock,
            $this->createMock(ReportHelper::class),
            $this->createMock(CsvExporter::class),
            $this->createMock(ExcelExporter::class)
        );

        $mockDispatcher = $this->createMock(EventDispatcher::class);
        $mockDispatcher->method('dispatch')
            ->willReturnCallback(
                function ($eventName, ReportBuilderEvent $event) {
                    $reportBuilderData = Fixtures::getReportBuilderEventData();
                    $event->addTable('assets', $reportBuilderData['all']['tables']['assets']);
                }
            );
        $this->reportModel->setDispatcher($mockDispatcher);

        $translatorMock = $this->createMock(Translator::class);
        // Make the translator return whatever string is passed to it instead of null
        $translatorMock->method('trans')->withAnyParameters()->willReturnArgument(0);
        $this->reportModel->setTranslator($translatorMock);

        // Do this to build the initial set of data from the subscribers that get used in all other contexts
        $this->reportModel->buildAvailableReports('all');

        parent::setUp();
    }

    public function testGetColumnListWithContext()
    {
        $properContextFormat = 'assets';
        $actual              = $this->reportModel->getColumnList($properContextFormat);
        $expected            = Fixtures::getGoodColumnList();

        $this->assertEquals($expected->choices, $actual->choices);
        $this->assertEquals($expected->definitions, $actual->definitions);
    }
}
