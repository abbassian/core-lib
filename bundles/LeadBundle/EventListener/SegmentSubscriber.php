<?php

namespace Autoborna\LeadBundle\EventListener;

use Autoborna\CoreBundle\Exception\RecordCanNotUnpublishException;
use Autoborna\CoreBundle\Helper\IpLookupHelper;
use Autoborna\CoreBundle\Model\AuditLogModel;
use Autoborna\LeadBundle\Event\LeadListEvent as SegmentEvent;
use Autoborna\LeadBundle\LeadEvents;
use Autoborna\LeadBundle\Model\ListModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;

class SegmentSubscriber implements EventSubscriberInterface
{
    /**
     * @var IpLookupHelper
     */
    private $ipLookupHelper;

    /**
     * @var AuditLogModel
     */
    private $auditLogModel;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ListModel
     */
    private $listModel;

    public function __construct(
        IpLookupHelper $ipLookupHelper,
        AuditLogModel $auditLogModel,
        ListModel $listModel,
        TranslatorInterface $translator
    ) {
        $this->ipLookupHelper    = $ipLookupHelper;
        $this->auditLogModel     = $auditLogModel;
        $this->listModel         = $listModel;
        $this->translator        = $translator;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            LeadEvents::LIST_PRE_UNPUBLISH => ['onSegmentPreUnpublish', 0],
            LeadEvents::LIST_POST_SAVE     => ['onSegmentPostSave', 0],
            LeadEvents::LIST_POST_DELETE   => ['onSegmentDelete', 0],
        ];
    }

    /**
     * Add a segment entry to the audit log.
     */
    public function onSegmentPostSave(SegmentEvent $event)
    {
        $segment = $event->getList();
        if ($details = $event->getChanges()) {
            $log = [
                'bundle'    => 'lead',
                'object'    => 'segment',
                'objectId'  => $segment->getId(),
                'action'    => ($event->isNew()) ? 'create' : 'update',
                'details'   => $details,
                'ipAddress' => $this->ipLookupHelper->getIpAddressFromRequest(),
            ];
            $this->auditLogModel->writeToLog($log);
        }
    }

    public function onSegmentPreUnpublish(SegmentEvent $event): ?RecordCanNotUnpublishException
    {
        $leadList = $event->getList();
        $lists    = $this->listModel->getSegmentsWithDependenciesOnSegment($leadList->getId(), 'name');
        if (count($lists)) {
            $message = $this->translator->trans('autoborna.lead_list.is_in_use', ['%segments%' => implode(',', $lists)], 'validators');
            throw new RecordCanNotUnpublishException($message);
        }

        return null;
    }

    /**
     * Add a segment delete entry to the audit log.
     */
    public function onSegmentDelete(SegmentEvent $event)
    {
        $segment = $event->getList();
        $log     = [
            'bundle'    => 'lead',
            'object'    => 'segment',
            'objectId'  => $segment->deletedId,
            'action'    => 'delete',
            'details'   => ['name', $segment->getName()],
            'ipAddress' => $this->ipLookupHelper->getIpAddressFromRequest(),
        ];
        $this->auditLogModel->writeToLog($log);
    }
}
