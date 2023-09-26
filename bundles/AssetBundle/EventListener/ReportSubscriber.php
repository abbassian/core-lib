<?php

namespace Autoborna\AssetBundle\EventListener;

use Autoborna\AssetBundle\Entity\DownloadRepository;
use Autoborna\CoreBundle\Helper\Chart\LineChart;
use Autoborna\LeadBundle\Model\CompanyReportData;
use Autoborna\ReportBundle\Event\ReportBuilderEvent;
use Autoborna\ReportBundle\Event\ReportGeneratorEvent;
use Autoborna\ReportBundle\Event\ReportGraphEvent;
use Autoborna\ReportBundle\ReportEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ReportSubscriber implements EventSubscriberInterface
{
    const CONTEXT_ASSET          = 'assets';
    const CONTEXT_ASSET_DOWNLOAD = 'asset.downloads';

    /**
     * @var CompanyReportData
     */
    private $companyReportData;

    /**
     * @var DownloadRepository
     */
    private $downloadRepository;

    public function __construct(CompanyReportData $companyReportData, DownloadRepository $downloadRepository)
    {
        $this->companyReportData  = $companyReportData;
        $this->downloadRepository = $downloadRepository;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            ReportEvents::REPORT_ON_BUILD          => ['onReportBuilder', 0],
            ReportEvents::REPORT_ON_GENERATE       => ['onReportGenerate', 0],
            ReportEvents::REPORT_ON_GRAPH_GENERATE => ['onReportGraphGenerate', 0],
        ];
    }

    /**
     * Add available tables and columns to the report builder lookup.
     */
    public function onReportBuilder(ReportBuilderEvent $event)
    {
        if (!$event->checkContext([self::CONTEXT_ASSET, self::CONTEXT_ASSET_DOWNLOAD])) {
            return;
        }

        // Assets
        $prefix  = 'a.';
        $columns = [
            $prefix.'download_count' => [
                'alias' => 'download_count',
                'label' => 'autoborna.asset.report.download_count',
                'type'  => 'int',
            ],
            $prefix.'unique_download_count' => [
                'alias' => 'unique_download_count',
                'label' => 'autoborna.asset.report.unique_download_count',
                'type'  => 'int',
            ],
            $prefix.'alias' => [
                'label' => 'autoborna.core.alias',
                'type'  => 'string',
            ],
            $prefix.'lang' => [
                'label' => 'autoborna.core.language',
                'type'  => 'string',
            ],
            $prefix.'title' => [
                'label' => 'autoborna.core.title',
                'type'  => 'string',
            ],
        ];

        $columns = array_merge(
            $columns,
            $event->getStandardColumns($prefix, ['name'], 'autoborna_asset_action'),
            $event->getCategoryColumns()
        );

        $event->addTable(
            self::CONTEXT_ASSET,
            [
                'display_name' => 'autoborna.asset.assets',
                'columns'      => $columns,
            ]
        );

        if ($event->checkContext([self::CONTEXT_ASSET_DOWNLOAD])) {
            // asset downloads calculate this columns
            $columns[$prefix.'download_count']['formula']        = 'COUNT(ad.id)';
            $columns[$prefix.'unique_download_count']['formula'] = 'COUNT(DISTINCT ad.lead_id)';

            // Downloads
            $downloadPrefix  = 'ad.';
            $downloadColumns = [
                $downloadPrefix.'date_download' => [
                    'label'          => 'autoborna.asset.report.download.date_download',
                    'type'           => 'datetime',
                    'groupByFormula' => 'DATE('.$downloadPrefix.'date_download)',
                ],
                $downloadPrefix.'code' => [
                    'label' => 'autoborna.asset.report.download.code',
                    'type'  => 'string',
                ],
                $downloadPrefix.'referer' => [
                    'label' => 'autoborna.core.referer',
                    'type'  => 'string',
                ],
                $downloadPrefix.'source' => [
                    'label' => 'autoborna.report.field.source',
                    'type'  => 'string',
                ],
                $downloadPrefix.'source_id' => [
                    'label' => 'autoborna.report.field.source_id',
                    'type'  => 'int',
                ],
            ];

            $companyColumns = $this->companyReportData->getCompanyData();

            $event->addTable(
                self::CONTEXT_ASSET_DOWNLOAD,
                [
                    'display_name' => 'autoborna.asset.report.downloads.table',
                    'columns'      => array_merge(
                        $columns,
                        $downloadColumns,
                        $event->getCampaignByChannelColumns(),
                        $event->getLeadColumns(),
                        $event->getIpColumn(),
                        $companyColumns
                    ),
                ],
                self::CONTEXT_ASSET
            );

            // Add Graphs
            $context = self::CONTEXT_ASSET_DOWNLOAD;
            $event->addGraph($context, 'line', 'autoborna.asset.graph.line.downloads');
            $event->addGraph($context, 'table', 'autoborna.asset.table.most.downloaded');
            $event->addGraph($context, 'table', 'autoborna.asset.table.top.referrers');
            $event->addGraph($context, 'pie', 'autoborna.asset.graph.pie.statuses', ['translate' => false]);
        }
    }

    /**
     * Initialize the QueryBuilder object to generate reports from.
     */
    public function onReportGenerate(ReportGeneratorEvent $event)
    {
        if (!$event->checkContext([self::CONTEXT_ASSET, self::CONTEXT_ASSET_DOWNLOAD])) {
            return;
        }

        $queryBuilder = $event->getQueryBuilder();

        if ($event->checkContext(self::CONTEXT_ASSET)) {
            $queryBuilder->from(MAUTIC_TABLE_PREFIX.'assets', 'a');
            $event->addCategoryLeftJoin($queryBuilder, 'a');
        } elseif ($event->checkContext(self::CONTEXT_ASSET_DOWNLOAD)) {
            $event->applyDateFilters($queryBuilder, 'date_download', 'ad');

            $queryBuilder->from(MAUTIC_TABLE_PREFIX.'asset_downloads', 'ad')
                ->leftJoin('ad', MAUTIC_TABLE_PREFIX.'assets', 'a', 'a.id = ad.asset_id');
            $event->addCategoryLeftJoin($queryBuilder, 'a');
            $event->addLeadLeftJoin($queryBuilder, 'ad');
            $event->addIpAddressLeftJoin($queryBuilder, 'ad');
            $event->addCampaignByChannelJoin($queryBuilder, 'a', 'asset');

            if ($this->companyReportData->eventHasCompanyColumns($event)) {
                $event->addCompanyLeftJoin($queryBuilder);
            }

            if (!$event->hasGroupBy()) {
                $queryBuilder->groupBy('ad.id');
            }
        }

        $event->setQueryBuilder($queryBuilder);
    }

    /**
     * Initialize the QueryBuilder object to generate reports from.
     */
    public function onReportGraphGenerate(ReportGraphEvent $event)
    {
        // Context check, we only want to fire for Lead reports
        if (!$event->checkContext(self::CONTEXT_ASSET_DOWNLOAD)) {
            return;
        }

        $graphs = $event->getRequestedGraphs();
        $qb     = $event->getQueryBuilder();

        foreach ($graphs as $g) {
            $options      = $event->getOptions($g);
            $queryBuilder = clone $qb;
            $chartQuery   = clone $options['chartQuery'];
            $chartQuery->applyDateFilters($queryBuilder, 'date_download', 'ad');

            switch ($g) {
                case 'autoborna.asset.graph.line.downloads':
                    $chart = new LineChart(null, $options['dateFrom'], $options['dateTo']);
                    $chartQuery->modifyTimeDataQuery($queryBuilder, 'date_download', 'ad');
                    $downloads = $chartQuery->loadAndBuildTimeData($queryBuilder);
                    $chart->setDataset($options['translator']->trans($g), $downloads);
                    $data         = $chart->render();
                    $data['name'] = $g;

                    $event->setGraph($g, $data);
                    break;
                case 'autoborna.asset.table.most.downloaded':
                    $limit                  = 10;
                    $offset                 = 0;
                    $items                  = $this->downloadRepository->getMostDownloaded($queryBuilder, $limit, $offset);
                    $graphData              = [];
                    $graphData['data']      = $items;
                    $graphData['name']      = $g;
                    $graphData['iconClass'] = 'fa-download';
                    $graphData['link']      = 'autoborna_asset_action';
                    $event->setGraph($g, $graphData);
                    break;
                case 'autoborna.asset.table.top.referrers':
                    $limit                  = 10;
                    $offset                 = 0;
                    $items                  = $this->downloadRepository->getTopReferrers($queryBuilder, $limit, $offset);
                    $graphData              = [];
                    $graphData['data']      = $items;
                    $graphData['name']      = $g;
                    $graphData['iconClass'] = 'fa-download';
                    $graphData['link']      = 'autoborna_asset_action';
                    $event->setGraph($g, $graphData);
                    break;
                case 'autoborna.asset.graph.pie.statuses':
                    $items                  = $this->downloadRepository->getHttpStatuses($queryBuilder);
                    $graphData              = [];
                    $graphData['data']      = $items;
                    $graphData['name']      = $g;
                    $graphData['iconClass'] = 'fa-globe';
                    $event->setGraph($g, $graphData);
                    break;
            }

            unset($queryBuilder);
        }
    }
}
