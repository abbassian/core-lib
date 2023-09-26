<?php

namespace Autoborna\ReportBundle\EventListener;

use Autoborna\ConfigBundle\ConfigEvents;
use Autoborna\ConfigBundle\Event\ConfigBuilderEvent;
use Autoborna\ReportBundle\Form\Type\ConfigType;
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
            'bundle'     => 'ReportBundle',
            'formAlias'  => 'reportconfig',
            'formType'   => ConfigType::class,
            'formTheme'  => 'AutobornaReportBundle:FormTheme\Config',
            'parameters' => $event->getParametersFromConfig('AutobornaReportBundle'),
        ]);
    }
}
