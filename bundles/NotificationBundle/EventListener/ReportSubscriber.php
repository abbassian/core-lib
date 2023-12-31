<?php

namespace Autoborna\NotificationBundle\EventListener;

use Doctrine\DBAL\Connection;
use Autoborna\CoreBundle\Helper\Chart\LineChart;
use Autoborna\LeadBundle\Model\CompanyReportData;
use Autoborna\NotificationBundle\Entity\StatRepository;
use Autoborna\ReportBundle\Event\ReportBuilderEvent;
use Autoborna\ReportBundle\Event\ReportGeneratorEvent;
use Autoborna\ReportBundle\Event\ReportGraphEvent;
use Autoborna\ReportBundle\ReportEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ReportSubscriber implements EventSubscriberInterface
{
    const MOBILE_NOTIFICATIONS       = 'mobile_notifications';
    const MOBILE_NOTIFICATIONS_STATS = 'mobile_notifications.stats';

    /**
     * @var Connection
     */
    private $db;

    /**
     * @var CompanyReportData
     */
    private $companyReportData;

    /**
     * @var StatRepository
     */
    private $statRepository;

    public function __construct(Connection $db, CompanyReportData $companyReportData, StatRepository $statRepository)
    {
        $this->db                = $db;
        $this->companyReportData = $companyReportData;
        $this->statRepository    = $statRepository;
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
        if (!$event->checkContext([self::MOBILE_NOTIFICATIONS, self::MOBILE_NOTIFICATIONS_STATS])) {
            return;
        }

        $prefix               = 'pn.';
        $channelUrlTrackables = 'cut.';
        $columns              = [
            $prefix.'heading' => [
                'label' => 'autoborna.notification.mobile_notification.heading',
                'type'  => 'string',
            ],
            $prefix.'lang' => [
                'label' => 'autoborna.core.language',
                'type'  => 'string',
            ],
            $prefix.'read_count' => [
                'label' => 'autoborna.mobile_notification.report.read_count',
                'type'  => 'int',
            ],
            'read_ratio' => [
                'alias'   => 'read_ratio',
                'label'   => 'autoborna.mobile_notification.report.read_ratio',
                'type'    => 'string',
                'formula' => 'CONCAT(ROUND(('.$prefix.'read_count/'.$prefix.'sent_count)*100),\'%\')',
            ],
            $prefix.'sent_count' => [
                'label' => 'autoborna.mobile_notification.report.sent_count',
                'type'  => 'int',
            ],
            'hits' => [
                'alias'   => 'hits',
                'label'   => 'autoborna.mobile_notification.report.hits_count',
                'type'    => 'string',
                'formula' => $channelUrlTrackables.'hits',
            ],
            'unique_hits' => [
                'alias'   => 'unique_hits',
                'label'   => 'autoborna.mobile_notification.report.unique_hits_count',
                'type'    => 'string',
                'formula' => $channelUrlTrackables.'unique_hits',
            ],
            'hits_ratio' => [
                'alias'   => 'hits_ratio',
                'label'   => 'autoborna.mobile_notification.report.hits_ratio',
                'type'    => 'string',
                'formula' => 'CONCAT(ROUND('.$channelUrlTrackables.'hits/('.$prefix.'sent_count * '.$channelUrlTrackables
                    .'trackable_count)*100),\'%\')',
            ],
            'unique_ratio' => [
                'alias'   => 'unique_ratio',
                'label'   => 'autoborna.mobile_notification.report.unique_ratio',
                'type'    => 'string',
                'formula' => 'CONCAT(ROUND('.$channelUrlTrackables.'unique_hits/('.$prefix.'sent_count * '.$channelUrlTrackables
                    .'trackable_count)*100),\'%\')',
            ],
        ];

        $columns = array_merge(
            $columns,
            $event->getStandardColumns($prefix, [], 'autoborna_mobile_notification_action'),
            $event->getCategoryColumns()
        );
        $data = [
            'display_name' => 'autoborna.notification.mobile_notifications',
            'columns'      => $columns,
        ];

        $event->addTable(self::MOBILE_NOTIFICATIONS, $data);

        if ($event->checkContext(self::MOBILE_NOTIFICATIONS_STATS)) {
            // Ratios are not applicable for individual stats
            unset($columns['read_ratio'], $columns['unsubscribed_ratio'], $columns['hits_ratio'], $columns['unique_ratio']);

            // Mobile Notification counts are not applicable for individual stats
            unset($columns[$prefix.'read_count']);

            $statPrefix  = 'pns.';
            $statColumns = [
                $statPrefix.'date_sent' => [
                    'label'          => 'autoborna.mobile_notifications.report.stat.date_sent',
                    'type'           => 'datetime',
                    'groupByFormula' => 'DATE('.$statPrefix.'date_sent)',
                ],
                $statPrefix.'date_read' => [
                    'label'          => 'autoborna.mobile_notifications.report.stat.date_read',
                    'type'           => 'datetime',
                    'groupByFormula' => 'DATE('.$statPrefix.'date_read)',
                ],
                $statPrefix.'source' => [
                    'label' => 'autoborna.report.field.source',
                    'type'  => 'string',
                ],
                $statPrefix.'source_id' => [
                    'label' => 'autoborna.report.field.source_id',
                    'type'  => 'int',
                ],
            ];

            $companyColumns = $this->companyReportData->getCompanyData();

            $mobileStatsColumns = array_merge(
                $columns,
                $statColumns,
                $event->getLeadColumns(),
                $event->getIpColumn(),
                $companyColumns
            );

            $data = [
                'display_name' => 'autoborna.mobile_notification.stats.report.table',
                'columns'      => $mobileStatsColumns,
            ];
            $context = self::MOBILE_NOTIFICATIONS_STATS;

            // Register table
            $event->addTable($context, $data, self::MOBILE_NOTIFICATIONS);

            // Register Graphs
            $event->addGraph($context, 'line', 'autoborna.mobile_notification.graph.line.stats');
            $event->addGraph($context, 'table', 'autoborna.mobile_notification.table.most.mobile_notifications.sent');
            $event->addGraph($context, 'table', 'autoborna.mobile_notification.table.most.mobile_notifications.read');
            $event->addGraph($context, 'table', 'autoborna.mobile_notification.table.most.mobile_notifications.read.percent');
        }
    }

    /**
     * Initialize the QueryBuilder object to generate reports from.
     */
    public function onReportGenerate(ReportGeneratorEvent $event)
    {
        if (!$event->checkContext([self::MOBILE_NOTIFICATIONS, self::MOBILE_NOTIFICATIONS_STATS])) {
            return;
        }

        $qb = $event->getQueryBuilder();

        // channel_url_trackables subquery
        $qbcut        = $this->db->createQueryBuilder();
        $clickColumns = ['hits', 'unique_hits', 'hits_ratio', 'unique_ratio'];

        // Ensure this only stats mobile notifications
        $qb->andWhere('pn.mobile = 1');

        switch ($event->getContext()) {
            case self::MOBILE_NOTIFICATIONS:
                $qb->from(MAUTIC_TABLE_PREFIX.'push_notifications', 'pn');
                $event->addCategoryLeftJoin($qb, 'pn');

                if ($event->usesColumn($clickColumns)) {
                    $qbcut->select(
                        'COUNT(cut2.channel_id) AS trackable_count, SUM(cut2.hits) AS hits',
                        'SUM(cut2.unique_hits) AS unique_hits',
                        'cut2.channel_id'
                    )
                        ->from(MAUTIC_TABLE_PREFIX.'channel_url_trackables', 'cut2')
                        ->where('cut2.channel = \'notification\'')
                        ->groupBy('cut2.channel_id');
                    $qb->leftJoin('pn', sprintf('(%s)', $qbcut->getSQL()), 'cut', 'pn.id = cut.channel_id');
                }
                break;
            case self::MOBILE_NOTIFICATIONS_STATS:
                $qb->from(MAUTIC_TABLE_PREFIX.'push_notification_stats', 'pns')
                    ->leftJoin('pns', MAUTIC_TABLE_PREFIX.'push_notifications', 'pn', 'pn.id = pns.notification_id');

                $event->addCategoryLeftJoin($qb, 'pn')
                    ->addLeadLeftJoin($qb, 'pns')
                    ->addIpAddressLeftJoin($qb, 'pns')
                    ->applyDateFilters($qb, 'date_sent', 'pns');

                if ($event->usesColumn($clickColumns)) {
                    $qbcut->select('COUNT(ph.id) AS hits', 'COUNT(DISTINCT(ph.redirect_id)) AS unique_hits', 'cut2.channel_id', 'ph.lead_id')
                        ->from(MAUTIC_TABLE_PREFIX.'channel_url_trackables', 'cut2')
                        ->join(
                            'cut2',
                            MAUTIC_TABLE_PREFIX.'page_hits',
                            'ph',
                            'cut2.redirect_id = ph.redirect_id AND cut2.channel_id = ph.source_id'
                        )
                        ->where('cut2.channel = \'email\' AND ph.source = \'email\'')
                        ->groupBy('cut2.channel_id, ph.lead_id');
                    $qb->leftJoin('pn', sprintf('(%s)', $qbcut->getSQL()), 'cut', 'pn.id = cut.channel_id AND pns.lead_id = cut.lead_id');
                }

                if ($this->companyReportData->eventHasCompanyColumns($event)) {
                    $event->addCompanyLeftJoin($qb);
                }

                break;
        }

        $event->setQueryBuilder($qb);
    }

    /**
     * Initialize the QueryBuilder object to generate reports from.
     */
    public function onReportGraphGenerate(ReportGraphEvent $event)
    {
        // Context check, we only want to fire for Mobile Notification reports
        if (!$event->checkContext(self::MOBILE_NOTIFICATIONS_STATS)) {
            return;
        }

        $graphs = $event->getRequestedGraphs();
        $qb     = $event->getQueryBuilder();

        foreach ($graphs as $g) {
            $options      = $event->getOptions($g);
            $queryBuilder = clone $qb;

            $chartQuery = clone $options['chartQuery'];
            $origQuery  = clone $queryBuilder;
            $chartQuery->applyDateFilters($queryBuilder, 'date_sent', 'pns');

            switch ($g) {
                case 'autoborna.mobile_notification.graph.line.stats':
                    $chart     = new LineChart(null, $options['dateFrom'], $options['dateTo']);
                    $sendQuery = clone $queryBuilder;
                    $readQuery = clone $origQuery;

                    $readQuery->andWhere($qb->expr()->isNotNull('date_read'));
                    $chartQuery->applyDateFilters($readQuery, 'date_read', 'pns');
                    $chartQuery->modifyTimeDataQuery($sendQuery, 'date_sent', 'pns');
                    $chartQuery->modifyTimeDataQuery($readQuery, 'date_read', 'pns');

                    $sends = $chartQuery->loadAndBuildTimeData($sendQuery);
                    $reads = $chartQuery->loadAndBuildTimeData($readQuery);
                    $chart->setDataset($options['translator']->trans('autoborna.mobile_notification.sent.mobile_notifications'), $sends);
                    $chart->setDataset($options['translator']->trans('autoborna.mobile_notification.read.mobile_notifications'), $reads);
                    $data         = $chart->render();
                    $data['name'] = $g;

                    $event->setGraph($g, $data);
                    break;

                case 'autoborna.mobile_notification.table.most.mobile_notifications.sent':
                    $queryBuilder->select('pn.id, pn.heading as title, count(pns.id) as sent')
                        ->groupBy('pn.id, pn.heading')
                        ->orderBy('sent', 'DESC');
                    $limit                  = 10;
                    $offset                 = 0;
                    $items                  = $this->statRepository->getMostNotifications($queryBuilder, $limit, $offset);
                    $graphData              = [];
                    $graphData['data']      = $items;
                    $graphData['name']      = $g;
                    $graphData['iconClass'] = 'fa-paper-plane-o';
                    $graphData['link']      = 'autoborna_mobile_notification_action';
                    $event->setGraph($g, $graphData);
                    break;

                case 'autoborna.mobile_notification.table.most.mobile_notifications.read':
                    $queryBuilder->select('pn.id, pn.heading as title, count(CASE WHEN pns.date_read THEN 1 ELSE null END) as "read"')
                        ->groupBy('pn.id, pn.heading')
                        ->orderBy('"read"', 'DESC');
                    $limit                  = 10;
                    $offset                 = 0;
                    $items                  = $this->statRepository->getMostNotifications($queryBuilder, $limit, $offset);
                    $graphData              = [];
                    $graphData['data']      = $items;
                    $graphData['name']      = $g;
                    $graphData['iconClass'] = 'fa-eye';
                    $graphData['link']      = 'autoborna_mobile_notification_action';
                    $event->setGraph($g, $graphData);
                    break;

                case 'autoborna.mobile_notification.table.most.mobile_notifications.read.percent':
                    $queryBuilder->select('pn.id, pn.heading as title, round(pn.read_count / pn.sent_count * 100) as ratio')
                        ->groupBy('pn.id, pn.heading')
                        ->orderBy('ratio', 'DESC');
                    $limit                  = 10;
                    $offset                 = 0;
                    $items                  = $this->statRepository->getMostNotifications($queryBuilder, $limit, $offset);
                    $graphData              = [];
                    $graphData['data']      = $items;
                    $graphData['name']      = $g;
                    $graphData['iconClass'] = 'fa-tachometer';
                    $graphData['link']      = 'autoborna_mobile_notification_action';
                    $event->setGraph($g, $graphData);
                    break;
            }
            unset($queryBuilder);
        }
    }
}
