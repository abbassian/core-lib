<?php

namespace Autoborna\ApiBundle\EventListener;

use Autoborna\ApiBundle\Form\Type\ConfigType;
use Autoborna\ConfigBundle\ConfigEvents;
use Autoborna\ConfigBundle\Event\ConfigBuilderEvent;
use Autoborna\ConfigBundle\Event\ConfigEvent;
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
            ConfigEvents::CONFIG_PRE_SAVE    => ['onConfigSave', 0],
        ];
    }

    public function onConfigGenerate(ConfigBuilderEvent $event)
    {
        $event->addForm([
            'bundle'     => 'ApiBundle',
            'formAlias'  => 'apiconfig',
            'formType'   => ConfigType::class,
            'formTheme'  => 'AutobornaApiBundle:FormTheme\Config',
            'parameters' => $event->getParametersFromConfig('AutobornaApiBundle'),
        ]);
    }

    public function onConfigSave(ConfigEvent $event)
    {
        // Symfony craps out with integer for firewall settings
        $data = $event->getConfig('apiconfig');
        if (isset($data['api_enable_basic_auth'])) {
            $data['api_enable_basic_auth'] = (bool) $data['api_enable_basic_auth'];
            $event->setConfig($data, 'apiconfig');
        }
    }
}
