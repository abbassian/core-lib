<?php

namespace Autoborna\LeadBundle\EventListener;

use Autoborna\LeadBundle\Model\CompanyReportData;
use Autoborna\LeadBundle\Report\FieldsBuilder;
use Autoborna\ReportBundle\Event\ReportBuilderEvent;
use Autoborna\ReportBundle\Event\ReportGeneratorEvent;
use Autoborna\ReportBundle\ReportEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ReportUtmTagSubscriber implements EventSubscriberInterface
{
    const UTM_TAG = 'lead.utmTag';

    /**
     * @var FieldsBuilder
     */
    private $fieldsBuilder;

    /**
     * @var CompanyReportData
     */
    private $companyReportData;

    public function __construct(
        FieldsBuilder $fieldsBuilder,
        CompanyReportData $companyReportData
    ) {
        $this->fieldsBuilder     = $fieldsBuilder;
        $this->companyReportData = $companyReportData;
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
        if (!$event->checkContext([self::UTM_TAG])) {
            return;
        }

        $columns        = $this->fieldsBuilder->getLeadFieldsColumns('l.');
        $companyColumns = $this->companyReportData->getCompanyData();

        $utmTagColumns = [
            'utm.utm_campaign' => [
                'label' => 'autoborna.lead.report.utm.campaign',
                'type'  => 'text',
            ],
            'utm.utm_content' => [
                'label' => 'autoborna.lead.report.utm.content',
                'type'  => 'text',
            ],
            'utm.utm_medium' => [
                'label' => 'autoborna.lead.report.utm.medium',
                'type'  => 'text',
            ],
            'utm.utm_source' => [
                'label' => 'autoborna.lead.report.utm.source',
                'type'  => 'text',
            ],
            'utm.utm_term' => [
                'label' => 'autoborna.lead.report.utm.term',
                'type'  => 'text',
            ],
        ];

        $data = [
            'display_name' => 'autoborna.lead.report.utm.utm_tag',
            'columns'      => array_merge($columns, $companyColumns, $utmTagColumns),
        ];
        $event->addTable(self::UTM_TAG, $data, ReportSubscriber::GROUP_CONTACTS);
    }

    /**
     * Initialize the QueryBuilder object to generate reports from.
     */
    public function onReportGenerate(ReportGeneratorEvent $event)
    {
        if (!$event->checkContext([self::UTM_TAG])) {
            return;
        }

        $qb = $event->getQueryBuilder();
        $qb->from(MAUTIC_TABLE_PREFIX.'lead_utmtags', 'utm')
            ->leftJoin('utm', MAUTIC_TABLE_PREFIX.'leads', 'l', 'l.id = utm.lead_id');

        if ($event->usesColumn(['u.first_name', 'u.last_name'])) {
            $qb->leftJoin('l', MAUTIC_TABLE_PREFIX.'users', 'u', 'u.id = l.owner_id');
        }

        if ($event->usesColumn('i.ip_address')) {
            $event->addLeadIpAddressLeftJoin($qb);
        }

        if ($this->companyReportData->eventHasCompanyColumns($event)) {
            $qb->leftJoin('l', MAUTIC_TABLE_PREFIX.'companies_leads', 'companies_lead', 'l.id = companies_lead.lead_id');
            $qb->leftJoin('companies_lead', MAUTIC_TABLE_PREFIX.'companies', 'comp', 'companies_lead.company_id = comp.id');
        }

        $event->setQueryBuilder($qb);
    }
}
