<?php

namespace Autoborna\AssetBundle\EventListener;

use Autoborna\AssetBundle\Form\Type\ConfigType;
use Autoborna\ConfigBundle\ConfigEvents;
use Autoborna\ConfigBundle\Event\ConfigBuilderEvent;
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
            'bundle'     => 'AssetBundle',
            'formAlias'  => 'assetconfig',
            'formType'   => ConfigType::class,
            'formTheme'  => 'AutobornaAssetBundle:FormTheme\Config',
            'parameters' => $event->getParametersFromConfig('AutobornaAssetBundle'),
        ]);
    }
}
