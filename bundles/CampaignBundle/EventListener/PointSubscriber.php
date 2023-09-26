<?php

namespace Autoborna\CampaignBundle\EventListener;

use Autoborna\CampaignBundle\Form\Type\CampaignEventAddRemoveLeadType;
use Autoborna\PointBundle\Event\TriggerBuilderEvent;
use Autoborna\PointBundle\PointEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PointSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            PointEvents::TRIGGER_ON_BUILD => ['onTriggerBuild', 0],
        ];
    }

    public function onTriggerBuild(TriggerBuilderEvent $event)
    {
        $changeLists = [
            'group'    => 'autoborna.campaign.point.trigger',
            'label'    => 'autoborna.campaign.point.trigger.changecampaigns',
            'callback' => ['\\Autoborna\\CampaignBundle\\Helper\\CampaignEventHelper', 'addRemoveLead'],
            'formType' => CampaignEventAddRemoveLeadType::class,
        ];

        $event->addEvent('campaign.changecampaign', $changeLists);
    }
}
