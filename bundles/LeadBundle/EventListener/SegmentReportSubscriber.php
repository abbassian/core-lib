<?php

namespace Autoborna\LeadBundle\EventListener;

use Autoborna\LeadBundle\Report\FieldsBuilder;
use Autoborna\ReportBundle\Event\ReportBuilderEvent;
use Autoborna\ReportBundle\Event\ReportGeneratorEvent;
use Autoborna\ReportBundle\ReportEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SegmentReportSubscriber implements EventSubscriberInterface
{
    const SEGMENT_MEMBERSHIP = 'segment.membership';

    /**
     * @var FieldsBuilder
     */
    private $fieldsBuilder;

    public function __construct(FieldsBuilder $fieldsBuilder)
    {
        $this->fieldsBuilder = $fieldsBuilder;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            ReportEvents::REPORT_ON_BUILD    => ['onReportBuilder', 0],
            ReportEvents::REPORT_ON_GENERATE => ['onReportGenerate', 0],
        ];
    }

    /**
     * Add available tables and columns to the report builder lookup.
     */
    public function onReportBuilder(ReportBuilderEvent $event)
    {
        if (!$event->checkContext([self::SEGMENT_MEMBERSHIP])) {
            return;
        }

        $columns = $this->fieldsBuilder->getLeadFieldsColumns('l.');

        $filters = $this->fieldsBuilder->getLeadFilter('l.', 'lll.');

        $segmentColumns = [
            'lll.manually_removed' => [
                'label' => 'autoborna.lead.report.segment.manually_removed',
                'type'  => 'bool',
            ],
            'lll.manually_added' => [
                'label' => 'autoborna.lead.report.segment.manually_added',
                'type'  => 'bool',
            ],
        ];

        $data = [
            'display_name' => 'autoborna.lead.report.segment.membership',
            'columns'      => array_merge($columns, $segmentColumns, $event->getStandardColumns('s.', ['publish_up', 'publish_down'])),
            'filters'      => $filters,
        ];
        $event->addTable(self::SEGMENT_MEMBERSHIP, $data, ReportSubscriber::GROUP_CONTACTS);

        unset($columns, $filters, $segmentColumns, $data);
    }

    /**
     * Initialize the QueryBuilder object to generate reports from.
     */
    public function onReportGenerate(ReportGeneratorEvent $event)
    {
        if (!$event->checkContext([self::SEGMENT_MEMBERSHIP])) {
            return;
        }

        $qb = $event->getQueryBuilder();
        $qb->from(MAUTIC_TABLE_PREFIX.'lead_lists_leads', 'lll')
            ->leftJoin('lll', MAUTIC_TABLE_PREFIX.'leads', 'l', 'l.id = lll.lead_id')
            ->leftJoin('lll', MAUTIC_TABLE_PREFIX.'lead_lists', 's', 's.id = lll.leadlist_id')
            ->andWhere('lll.manually_removed = 0');

        if ($event->usesColumn(['u.first_name', 'u.last_name'])) {
            $qb->leftJoin('l', MAUTIC_TABLE_PREFIX.'users', 'u', 'u.id = l.owner_id');
        }

        if ($event->usesColumn('i.ip_address')) {
            $event->addLeadIpAddressLeftJoin($qb);
        }

        $event->setQueryBuilder($qb);
    }
}
