<?php

namespace Autoborna\LeadBundle\EventListener;

use Autoborna\LeadBundle\Form\Type\ListActionType;
use Autoborna\LeadBundle\Form\Type\ModifyLeadTagsType;
use Autoborna\LeadBundle\Model\LeadModel;
use Autoborna\PointBundle\Event\TriggerBuilderEvent;
use Autoborna\PointBundle\Event\TriggerExecutedEvent;
use Autoborna\PointBundle\PointEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PointSubscriber implements EventSubscriberInterface
{
    /**
     * @var LeadModel
     */
    private $leadModel;

    public function __construct(LeadModel $leadModel)
    {
        $this->leadModel = $leadModel;
    }

    public static function getSubscribedEvents()
    {
        return [
            PointEvents::TRIGGER_ON_BUILD         => ['onTriggerBuild', 0],
            PointEvents::TRIGGER_ON_EVENT_EXECUTE => ['onTriggerExecute', 0],
        ];
    }

    public function onTriggerBuild(TriggerBuilderEvent $event)
    {
        $event->addEvent(
            'lead.changelists',
            [
                'group'    => 'autoborna.lead.point.trigger',
                'label'    => 'autoborna.lead.point.trigger.changelists',
                'callback' => ['\\Autoborna\\LeadBundle\\Helper\\PointEventHelper', 'changeLists'],
                'formType' => ListActionType::class,
            ]
        );

        $event->addEvent(
            'lead.changetags',
            [
                'group'     => 'autoborna.lead.point.trigger',
                'label'     => 'autoborna.lead.lead.events.changetags',
                'formType'  => ModifyLeadTagsType::class,
                'eventName' => PointEvents::TRIGGER_ON_EVENT_EXECUTE,
            ]
        );
    }

    public function onTriggerExecute(TriggerExecutedEvent $event): void
    {
        if ('lead.changetags' !== $event->getTriggerEvent()->getType()) {
            return;
        }

        $properties = $event->getTriggerEvent()->getProperties();
        $addTags    = $properties['add_tags'] ?: [];
        $removeTags = $properties['remove_tags'] ?: [];

        $this->leadModel->modifyTags($event->getLead(), $addTags, $removeTags);
    }
}
