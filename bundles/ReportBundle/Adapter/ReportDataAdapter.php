<?php

namespace Autoborna\ReportBundle\Adapter;

use Autoborna\ReportBundle\Crate\ReportDataResult;
use Autoborna\ReportBundle\Entity\Report;
use Autoborna\ReportBundle\Model\ReportExportOptions;
use Autoborna\ReportBundle\Model\ReportModel;

class ReportDataAdapter
{
    /**
     * @var ReportModel
     */
    private $reportModel;

    public function __construct(ReportModel $reportModel)
    {
        $this->reportModel = $reportModel;
    }

    public function getReportData(Report $report, ReportExportOptions $reportExportOptions)
    {
        $options                    = [];
        $options['paginate']        = true;
        $options['limit']           = $reportExportOptions->getBatchSize();
        $options['ignoreGraphData'] = true;
        $options['page']            = $reportExportOptions->getPage();
        $options['dateTo']          = $reportExportOptions->getDateTo();
        $options['dateFrom']        = $reportExportOptions->getDateFrom();

        $data = $this->reportModel->getReportData($report, null, $options);

        return new ReportDataResult($data);
    }
}
