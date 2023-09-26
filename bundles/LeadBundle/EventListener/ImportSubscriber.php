<?php

namespace Autoborna\LeadBundle\EventListener;

use Autoborna\CoreBundle\Helper\IpLookupHelper;
use Autoborna\CoreBundle\Model\AuditLogModel;
use Autoborna\LeadBundle\Event\ImportEvent;
use Autoborna\LeadBundle\LeadEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ImportSubscriber implements EventSubscriberInterface
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
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            LeadEvents::IMPORT_POST_SAVE   => ['onImportPostSave', 0],
            LeadEvents::IMPORT_POST_DELETE => ['onImportDelete', 0],
        ];
    }

    /**
     * Add an entry to the audit log.
     */
    public function onImportPostSave(ImportEvent $event)
    {
        $entity = $event->getEntity();
        if ($details = $event->getChanges()) {
            $log = [
                'bundle'    => 'lead',
                'object'    => 'import',
                'objectId'  => $entity->getId(),
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
    public function onImportDelete(ImportEvent $event)
    {
        $entity = $event->getEntity();
        $log    = [
            'bundle'    => 'lead',
            'object'    => 'import',
            'objectId'  => $entity->deletedId,
            'action'    => 'delete',
            'details'   => ['originalFile' => $entity->getOriginalFile()],
            'ipAddress' => $this->ipLookupHelper->getIpAddressFromRequest(),
        ];
        $this->auditLogModel->writeToLog($log);

        //In case of batch delete, this method call remove the uploaded file
        $entity->removeFile();
    }
}
