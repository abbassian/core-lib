<?php

namespace Autoborna\StageBundle\EventListener;

use Autoborna\CoreBundle\Helper\IpLookupHelper;
use Autoborna\CoreBundle\Model\AuditLogModel;
use Autoborna\StageBundle\Event as Events;
use Autoborna\StageBundle\StageEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class StageSubscriber implements EventSubscriberInterface
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
            StageEvents::STAGE_POST_SAVE   => ['onStagePostSave', 0],
            StageEvents::STAGE_POST_DELETE => ['onStageDelete', 0],
        ];
    }

    /**
     * Add an entry to the audit log.
     */
    public function onStagePostSave(Events\StageEvent $event)
    {
        $stage = $event->getStage();
        if ($details = $event->getChanges()) {
            $log = [
                'bundle'    => 'stage',
                'object'    => 'stage',
                'objectId'  => $stage->getId(),
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
    public function onStageDelete(Events\StageEvent $event)
    {
        $stage = $event->getStage();
        $log   = [
            'bundle'    => 'stage',
            'object'    => 'stage',
            'objectId'  => $stage->deletedId,
            'action'    => 'delete',
            'details'   => ['name' => $stage->getName()],
            'ipAddress' => $this->ipLookupHelper->getIpAddressFromRequest(),
        ];
        $this->auditLogModel->writeToLog($log);
    }
}
