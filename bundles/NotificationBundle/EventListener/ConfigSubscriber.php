<?php

declare(strict_types=1);

namespace Autoborna\NotificationBundle\EventListener;

use Autoborna\ConfigBundle\ConfigEvents;
use Autoborna\ConfigBundle\Event\ConfigBuilderEvent;
use Autoborna\NotificationBundle\Form\Type\NotificationConfigType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConfigSubscriber implements EventSubscriberInterface
{
    /**
     * @return array<string,array<int,string|int>>
     */
    public static function getSubscribedEvents()
    {
        return [
            ConfigEvents::CONFIG_ON_GENERATE => ['onConfigGenerate', 0],
        ];
    }

    public function onConfigGenerate(ConfigBuilderEvent $event): void
    {
        $event->addForm([
            'bundle'     => 'NotificationBundle',
            'formAlias'  => 'notification_config',
            'formType'   => NotificationConfigType::class,
            'formTheme'  => 'AutobornaNotificationBundle:FormTheme\Config',
            'parameters' => $event->getParametersFromConfig('AutobornaNotificationBundle'),
        ]);
    }
}
