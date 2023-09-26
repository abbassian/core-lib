<?php

namespace Autoborna\PageBundle\EventListener;

use Autoborna\PageBundle\Event\PageHitEvent;
use Autoborna\PageBundle\PageEvents;
use Autoborna\WebhookBundle\Event\WebhookBuilderEvent;
use Autoborna\WebhookBundle\Model\WebhookModel;
use Autoborna\WebhookBundle\WebhookEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class WebhookSubscriber implements EventSubscriberInterface
{
    /**
     * @var WebhookModel
     */
    private $webhookModel;

    public function __construct(WebhookModel $webhookModel)
    {
        $this->webhookModel = $webhookModel;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            WebhookEvents::WEBHOOK_ON_BUILD => ['onWebhookBuild', 0],
            PageEvents::PAGE_ON_HIT         => ['onPageHit', 0],
        ];
    }

    /**
     * Add event triggers and actions.
     */
    public function onWebhookBuild(WebhookBuilderEvent $event)
    {
        // add checkbox to the webhook form for new leads
        $pageHit = [
            'label'       => 'autoborna.page.webhook.event.hit',
            'description' => 'autoborna.page.webhook.event.hit_desc',
        ];

        // add it to the list
        $event->addEvent(PageEvents::PAGE_ON_HIT, $pageHit);
    }

    public function onPageHit(PageHitEvent $event)
    {
        $this->webhookModel->queueWebhooksByType(
            PageEvents::PAGE_ON_HIT,
            [
                'hit' => $event->getHit(),
            ],
            [
                'hitDetails',
                'emailDetails',
                'pageList',
                'leadList',
            ]
        );
    }
}
