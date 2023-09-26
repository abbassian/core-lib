<?php

namespace Autoborna\WebhookBundle\EventListener;

use Autoborna\ConfigBundle\ConfigEvents;
use Autoborna\ConfigBundle\Event\ConfigBuilderEvent;
use Autoborna\WebhookBundle\Form\Type\ConfigType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConfigSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            ConfigEvents::CONFIG_ON_GENERATE => ['onConfigGenerate', 0],
        ];
    }

    public function onConfigGenerate(ConfigBuilderEvent $event)
    {
        $event->addForm([
            'bundle'     => 'WebhookBundle',
            'formAlias'  => 'webhookconfig',
            'formType'   => ConfigType::class,
            'formTheme'  => 'AutobornaWebhookBundle:FormTheme\Config',
            'parameters' => $event->getParametersFromConfig('AutobornaWebhookBundle'),
        ]);
    }
}
