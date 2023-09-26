<?php

namespace Autoborna\NotificationBundle\EventListener;

use Autoborna\CampaignBundle\CampaignEvents;
use Autoborna\CampaignBundle\Event\CampaignBuilderEvent;
use Autoborna\CampaignBundle\Event\CampaignExecutionEvent;
use Autoborna\NotificationBundle\Entity\PushID;
use Autoborna\NotificationBundle\NotificationEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class CampaignConditionSubscriber.
 */
class CampaignConditionSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            CampaignEvents::CAMPAIGN_ON_BUILD                 => ['onCampaignBuild', 0],
            NotificationEvents::ON_CAMPAIGN_TRIGGER_CONDITION => ['onCampaignTriggerHasActiveCondition', 0],
        ];
    }

    public function onCampaignBuild(CampaignBuilderEvent $event)
    {
        $event->addCondition(
            'notification.has.active',
            [
                'label'       => 'autoborna.notification.campaign.event.notification.has.active',
                'description' => 'autoborna.notification.campaign.event.notification.has.active.desc',
                'eventName'   => NotificationEvents::ON_CAMPAIGN_TRIGGER_CONDITION,
            ]
        );
    }

    public function onCampaignTriggerHasActiveCondition(CampaignExecutionEvent $event)
    {
        if (!$event->checkContext('notification.has.active')) {
            return;
        }

        $pushIds = $event->getLead()->getPushIDs();
        /** @var PushID $pushID */
        foreach ($pushIds as $pushID) {
            if ($pushID->isEnabled()) {
                return $event->setResult(true);
            }
        }

        return $event->setResult(false);
    }
}
