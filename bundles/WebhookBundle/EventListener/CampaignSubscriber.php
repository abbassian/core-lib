<?php

namespace Autoborna\WebhookBundle\EventListener;

use Autoborna\CampaignBundle\CampaignEvents;
use Autoborna\CampaignBundle\Event as Events;
use Autoborna\CampaignBundle\Event\CampaignExecutionEvent;
use Autoborna\WebhookBundle\Form\Type\CampaignEventSendWebhookType;
use Autoborna\WebhookBundle\Helper\CampaignHelper;
use Autoborna\WebhookBundle\WebhookEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CampaignSubscriber implements EventSubscriberInterface
{
    /**
     * @var CampaignHelper
     */
    private $campaignHelper;

    public function __construct(CampaignHelper $campaignHelper)
    {
        $this->campaignHelper = $campaignHelper;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            CampaignEvents::CAMPAIGN_ON_BUILD         => ['onCampaignBuild', 0],
            WebhookEvents::ON_CAMPAIGN_TRIGGER_ACTION => ['onCampaignTriggerAction', 0],
        ];
    }

    /**
     * @return CampaignExecutionEvent
     */
    public function onCampaignTriggerAction(CampaignExecutionEvent $event)
    {
        if ($event->checkContext('campaign.sendwebhook')) {
            try {
                $this->campaignHelper->fireWebhook($event->getConfig(), $event->getLead());
                $event->setResult(true);
            } catch (\Exception $e) {
                $event->setFailed($e->getMessage());
            }
        }
    }

    /**
     * Add event triggers and actions.
     */
    public function onCampaignBuild(Events\CampaignBuilderEvent $event)
    {
        $sendWebhookAction = [
            'label'              => 'autoborna.webhook.event.sendwebhook',
            'description'        => 'autoborna.webhook.event.sendwebhook_desc',
            'formType'           => CampaignEventSendWebhookType::class,
            'formTypeCleanMasks' => 'clean',
            'eventName'          => WebhookEvents::ON_CAMPAIGN_TRIGGER_ACTION,
        ];
        $event->addAction('campaign.sendwebhook', $sendWebhookAction);
    }
}
