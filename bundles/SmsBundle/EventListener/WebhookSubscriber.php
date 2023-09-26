<?php

declare(strict_types=1);

namespace Autoborna\SmsBundle\EventListener;

use Autoborna\SmsBundle\Event\SmsSendEvent;
use Autoborna\SmsBundle\SmsEvents;
use Autoborna\WebhookBundle\Event\WebhookBuilderEvent;
use Autoborna\WebhookBundle\Model\WebhookModel;
use Autoborna\WebhookBundle\WebhookEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class WebhookSubscriber implements EventSubscriberInterface
{
    /**
     * @var WebhookModel
     */
    private $webhookModel;

    public function __construct(WebhookModel $webhookModel)
    {
        $this->webhookModel = $webhookModel;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SmsEvents::SMS_ON_SEND          => 'onSend',
            WebhookEvents::WEBHOOK_ON_BUILD => 'onWebhookBuild',
        ];
    }

    /**
     * Add event triggers and actions.
     */
    public function onWebhookBuild(WebhookBuilderEvent $event): void
    {
        $event->addEvent(
            SmsEvents::SMS_ON_SEND,
            [
                'label'       => 'autoborna.sms.webhook.event.send',
                'description' => 'autoborna.sms.webhook.event.send_desc',
            ]
        );
    }

    public function onSend(SmsSendEvent $event): void
    {
        $this->webhookModel->queueWebhooksByType(
            SmsEvents::SMS_ON_SEND,
            [
                'smsId'   => $event->getSmsId(),
                'contact' => $event->getLead(),
                'content' => $event->getContent(),
            ]
        );
    }
}
