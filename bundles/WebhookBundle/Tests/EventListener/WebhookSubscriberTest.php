<?php

namespace Autoborna\WebhookBundle\Tests\EventListener;

use Autoborna\CoreBundle\Helper\IpLookupHelper;
use Autoborna\CoreBundle\Model\AuditLogModel;
use Autoborna\WebhookBundle\Entity\Webhook;
use Autoborna\WebhookBundle\Event\WebhookEvent;
use Autoborna\WebhookBundle\EventListener\WebhookSubscriber;
use Autoborna\WebhookBundle\Notificator\WebhookKillNotificator;
use Autoborna\WebhookBundle\WebhookEvents;

class WebhookSubscriberTest extends \PHPUnit\Framework\TestCase
{
    private $ipLookupHelper;
    private $auditLogModel;
    private $webhookKillNotificator;

    protected function setUp(): void
    {
        $this->ipLookupHelper         = $this->createMock(IpLookupHelper::class);
        $this->auditLogModel          = $this->createMock(AuditLogModel::class);
        $this->webhookKillNotificator = $this->createMock(WebhookKillNotificator::class);
    }

    public function testGetSubscribedEvents()
    {
        $this->assertSame(
            [
                WebhookEvents::WEBHOOK_POST_SAVE   => ['onWebhookSave', 0],
                WebhookEvents::WEBHOOK_POST_DELETE => ['onWebhookDelete', 0],
                WebhookEvents::WEBHOOK_KILL        => ['onWebhookKill', 0],
            ],
            WebhookSubscriber::getSubscribedEvents()
        );
    }

    public function testOnWebhookKill()
    {
        $webhookMock = $this->createMock(Webhook::class);
        $reason      = 'reason';

        $eventMock = $this->createMock(WebhookEvent::class);
        $eventMock
            ->expects($this->once())
            ->method('getWebhook')
            ->willReturn($webhookMock);
        $eventMock
            ->expects($this->once())
            ->method('getReason')
            ->willReturn($reason);

        $this->webhookKillNotificator
            ->expects($this->once())
            ->method('send')
            ->with($webhookMock, $reason);

        $subscriber = new WebhookSubscriber($this->ipLookupHelper, $this->auditLogModel, $this->webhookKillNotificator);
        $subscriber->onWebhookKill($eventMock);
    }
}
