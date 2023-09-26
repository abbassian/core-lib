<?php

namespace Autoborna\CoreBundle\EventListener;

use Autoborna\ConfigBundle\ConfigEvents;
use Autoborna\ConfigBundle\Event\ConfigBuilderEvent;
use Autoborna\CoreBundle\Form\Type\ConfigThemeType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConfigThemeSubscriber implements EventSubscriberInterface
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
        $event->addForm(
            [
                'bundle'     => 'CoreBundle',
                'formAlias'  => 'themeconfig',
                'formType'   => ConfigThemeType::class,
                'formTheme'  => 'AutobornaCoreBundle:FormTheme\Config',
                'parameters' => [
                    'theme'                           => $event->getParametersFromConfig('AutobornaCoreBundle')['theme'],
                    'theme_import_allowed_extensions' => $event->getParametersFromConfig('AutobornaCoreBundle')['theme_import_allowed_extensions'],
                ],
            ]
        );
    }
}
