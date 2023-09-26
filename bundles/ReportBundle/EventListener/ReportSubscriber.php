<?php

namespace Autoborna\ReportBundle\EventListener;

use Autoborna\CoreBundle\Helper\IpLookupHelper;
use Autoborna\CoreBundle\Model\AuditLogModel;
use Autoborna\ReportBundle\Event\ReportEvent;
use Autoborna\ReportBundle\ReportEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ReportSubscriber implements EventSubscriberInterface
{
    /**
     * @var IpLookupHelper
     */
    private $ipLookupHelper;

    /**
     * @var AuditLogModel
     */
    private $auditLogModel;

    public function __construct(IpLookupHelper $ipLookupHelper, AuditLogModel $auditLogModel)
    {
        $this->ipLookupHelper = $ipLookupHelper;
        $this->auditLogModel  = $auditLogModel;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ReportEvents::REPORT_POST_SAVE   => ['onReportPostSave', 0],
            ReportEvents::REPORT_POST_DELETE => ['onReportDelete', 0],
        ];
    }

    /**
     * Add an entry to the audit log.
     */
    public function onReportPostSave(ReportEvent $event)
    {
        $report = $event->getReport();
        if ($details = $event->getChanges()) {
            $log = [
                'bundle'    => 'report',
                'object'    => 'report',
                'objectId'  => $report->getId(),
                'action'    => ($event->isNew()) ? 'create' : 'update',
                'details'   => $details,
                'ipAddress' => $this->ipLookupHelper->getIpAddressFromRequest(),
            ];
            $this->auditLogModel->writeToLog($log);
        }
    }

    /**
     * Add a delete entry to the audit log.
     */
    public function onReportDelete(ReportEvent $event)
    {
        $report = $event->getReport();
        $log    = [
            'bundle'    => 'report',
            'object'    => 'report',
            'objectId'  => $report->deletedId,
            'action'    => 'delete',
            'details'   => ['name' => $report->getName()],
            'ipAddress' => $this->ipLookupHelper->getIpAddressFromRequest(),
        ];
        $this->auditLogModel->writeToLog($log);
    }
}
