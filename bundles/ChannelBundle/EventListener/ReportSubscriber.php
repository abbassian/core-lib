<?php

namespace Autoborna\ChannelBundle\EventListener;

use Autoborna\LeadBundle\Model\CompanyReportData;
use Autoborna\ReportBundle\Event\ReportBuilderEvent;
use Autoborna\ReportBundle\Event\ReportDataEvent;
use Autoborna\ReportBundle\Event\ReportGeneratorEvent;
use Autoborna\ReportBundle\ReportEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;

class ReportSubscriber implements EventSubscriberInterface
{
    const CONTEXT_MESSAGE_CHANNEL = 'message.channel';

    /**
     * @var CompanyReportData
     */
    private $companyReportData;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(CompanyReportData $companyReportData, RouterInterface $router)
    {
        $this->companyReportData = $companyReportData;
        $this->router            = $router;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            ReportEvents::REPORT_ON_BUILD    => ['onReportBuilder', 0],
            ReportEvents::REPORT_ON_GENERATE => ['onReportGenerate', 0],
            ReportEvents::REPORT_ON_DISPLAY  => ['onReportDisplay', 0],
        ];
    }

    /**
     * Add available tables and columns to the report builder lookup.
     */
    public function onReportBuilder(ReportBuilderEvent $event)
    {
        if (!$event->checkContext([self::CONTEXT_MESSAGE_CHANNEL])) {
            return;
        }

        // message queue
        $prefix  = 'mq.';
        $columns = [
            $prefix.'channel' => [
                'label' => 'autoborna.message.queue.report.channel',
                'type'  => 'html',
            ],
            $prefix.'channel_id' => [
                'label' => 'autoborna.message.queue.report.channel_id',
                'type'  => 'int',
            ],
            $prefix.'priority' => [
                'label' => 'autoborna.message.queue.report.priority',
                'type'  => 'string',
            ],
            $prefix.'max_attempts' => [
                'label' => 'autoborna.message.queue.report.max_attempts',
                'type'  => 'int',
            ],
            $prefix.'attempts' => [
                'label' => 'autoborna.message.queue.report.attempts',
                'type'  => 'int',
            ],
            $prefix.'success' => [
                'label' => 'autoborna.message.queue.report.success',
                'type'  => 'boolean',
            ],
            $prefix.'status' => [
                'label' => 'autoborna.message.queue.report.status',
                'type'  => 'string',
            ],
            $prefix.'last_attempt' => [
                'label' => 'autoborna.message.queue.report.last_attempt',
                'type'  => 'datetime',
            ],
            $prefix.'date_sent' => [
                'label' => 'autoborna.message.queue.report.date_sent',
                'type'  => 'datetime',
            ],
            $prefix.'scheduled_date' => [
                'label' => 'autoborna.message.queue.report.scheduled_date',
                'type'  => 'datetime',
            ],
            $prefix.'date_published' => [
                'label' => 'autoborna.message.queue.report.date_published',
                'type'  => 'datetime',
            ],
        ];

        $companyColumns = $this->companyReportData->getCompanyData();

        $columns = array_merge(
            $columns,
            $event->getLeadColumns(),
            $companyColumns
        );

        $event->addTable(
            self::CONTEXT_MESSAGE_CHANNEL,
            [
                'display_name' => 'autoborna.message.queue',
                'columns'      => $columns,
            ]
        );
    }

    /**
     * Initialize the QueryBuilder object to generate reports from.
     */
    public function onReportGenerate(ReportGeneratorEvent $event)
    {
        if (!$event->checkContext([self::CONTEXT_MESSAGE_CHANNEL])) {
            return;
        }

        $queryBuilder = $event->getQueryBuilder();
        $queryBuilder->from(MAUTIC_TABLE_PREFIX.'message_queue', 'mq')
            ->leftJoin('mq', MAUTIC_TABLE_PREFIX.'leads', 'l', 'l.id = mq.lead_id');

        if ($this->companyReportData->eventHasCompanyColumns($event)) {
            $event->addCompanyLeftJoin($queryBuilder);
        }

        $event->setQueryBuilder($queryBuilder);
    }

    public function onReportDisplay(ReportDataEvent $event)
    {
        $data = $event->getData();
        if ($event->checkContext([self::CONTEXT_MESSAGE_CHANNEL])) {
            if (isset($data[0]['channel']) && isset($data[0]['channel_id'])) {
                foreach ($data as &$row) {
                    $href = $this->router->generate('autoborna_'.$row['channel'].'_action', ['objectAction' => 'view', 'objectId' => $row['channel_id']]);
                    if (isset($row['channel'])) {
                        $row['channel'] = '<a href="'.$href.'">'.$row['channel'].'</a>';
                    }
                    unset($row);
                }
            }
        }

        $event->setData($data);
        unset($data);
    }
}
