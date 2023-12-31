<?php

namespace Autoborna\WebhookBundle\EventListener;

use Autoborna\CoreBundle\Helper\IpLookupHelper;
use Autoborna\CoreBundle\Model\AuditLogModel;
use Autoborna\WebhookBundle\Event\WebhookEvent;
use Autoborna\WebhookBundle\Notificator\WebhookKillNotificator;
use Autoborna\WebhookBundle\WebhookEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class WebhookSubscriber implements EventSubscriberInterface
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
     * @var WebhookKillNotificator
     */
    private $webhookKillNotificator;

    public function __construct(
        IpLookupHelper $ipLookupHelper,
        AuditLogModel $auditLogModel,
        WebhookKillNotificator $webhookKillNotificator
    ) {
        $this->ipLookupHelper         = $ipLookupHelper;
        $this->auditLogModel          = $auditLogModel;
        $this->webhookKillNotificator = $webhookKillNotificator;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            WebhookEvents::WEBHOOK_POST_SAVE   => ['onWebhookSave', 0],
            WebhookEvents::WEBHOOK_POST_DELETE => ['onWebhookDelete', 0],
            WebhookEvents::WEBHOOK_KILL        => ['onWebhookKill', 0],
        ];
    }

    /**
     * Add an entry to the audit log.
     */
    public function onWebhookSave(WebhookEvent $event)
    {
        $webhook = $event->getWebhook();

        if ($details = $event->getChanges()) {
            $log = [
                'bundle'    => 'webhook',
                'object'    => 'webhook',
                'objectId'  => $webhook->getId(),
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
    public function onWebhookDelete(WebhookEvent $event)
    {
        $webhook = $event->getWebhook();
        $log     = [
            'bundle'    => 'webhook',
            'object'    => 'webhook',
            'objectId'  => $event->getWebhook()->deletedId,
            'action'    => 'delete',
            'details'   => ['name' => $webhook->getName()],
            'ipAddress' => $this->ipLookupHelper->getIpAddressFromRequest(),
        ];
        $this->auditLogModel->writeToLog($log);
    }

    /**
     * Send notification about killed webhook.
     */
    public function onWebhookKill(WebhookEvent $event)
    {
        $this->webhookKillNotificator->send($event->getWebhook(), $event->getReason());
    }
}
