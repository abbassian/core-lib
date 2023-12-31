<?php

namespace Autoborna\ApiBundle\EventListener;

use Autoborna\ApiBundle\ApiEvents;
use Autoborna\ApiBundle\Event as Events;
use Autoborna\CoreBundle\Helper\IpLookupHelper;
use Autoborna\CoreBundle\Model\AuditLogModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ClientSubscriber implements EventSubscriberInterface
{
    /**
     * @var IpLookupHelper
     */
    private $ipLookupHelper;

    /**
     * @var AuditLogModel
     */
    private $auditLogModel;

    public function __construct(
        IpLookupHelper $ipLookupHelper,
        AuditLogModel $auditLogModel
    ) {
        $this->ipLookupHelper       = $ipLookupHelper;
        $this->auditLogModel        = $auditLogModel;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ApiEvents::CLIENT_POST_SAVE   => ['onClientPostSave', 0],
            ApiEvents::CLIENT_POST_DELETE => ['onClientDelete', 0],
        ];
    }

    /**
     * Add a client change entry to the audit log.
     */
    public function onClientPostSave(Events\ClientEvent $event): void
    {
        $client = $event->getClient();
        if (!$details = $event->getChanges()) {
            return;
        }

        $log = [
            'bundle'    => 'api',
            'object'    => 'client',
            'objectId'  => $client->getId(),
            'action'    => ($event->isNew()) ? 'create' : 'update',
            'details'   => $details,
            'ipAddress' => $this->ipLookupHelper->getIpAddressFromRequest(),
        ];
        $this->auditLogModel->writeToLog($log);
    }

    /**
     * Add a role delete entry to the audit log.
     */
    public function onClientDelete(Events\ClientEvent $event): void
    {
        $client = $event->getClient();
        $log    = [
            'bundle'    => 'api',
            'object'    => 'client',
            'objectId'  => $client->deletedId,
            'action'    => 'delete',
            'details'   => ['name' => $client->getName()],
            'ipAddress' => $this->ipLookupHelper->getIpAddressFromRequest(),
        ];
        $this->auditLogModel->writeToLog($log);
    }
}
