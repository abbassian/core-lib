<?php

namespace Autoborna\PluginBundle\EventListener;

use Autoborna\PluginBundle\Form\Type\IntegrationsListType;
use Autoborna\PluginBundle\Helper\EventHelper;
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
        $action = [
            'group'     => 'autoborna.plugin.point.action',
            'label'     => 'autoborna.plugin.actions.push_lead',
            'formType'  => IntegrationsListType::class,
            'formTheme' => 'AutobornaPluginBundle:FormTheme\Integration',
            'callback'  => [EventHelper::class, 'pushLead'],
        ];

        $event->addEvent('plugin.leadpush', $action);
    }
}
