<?php

namespace Autoborna\FormBundle\EventListener;

use Autoborna\ConfigBundle\ConfigEvents;
use Autoborna\ConfigBundle\Event\ConfigBuilderEvent;
use Autoborna\FormBundle\Form\Type\ConfigFormType;
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
            'bundle'     => 'FormBundle',
            'formAlias'  => 'formconfig',
            'formType'   => ConfigFormType::class,
            'formTheme'  => 'AutobornaFormBundle:FormTheme\Config',
            'parameters' => $event->getParametersFromConfig('AutobornaFormBundle'),
        ]);
    }
}
