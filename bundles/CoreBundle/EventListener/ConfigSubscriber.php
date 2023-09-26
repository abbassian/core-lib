<?php

namespace Autoborna\CoreBundle\EventListener;

use Autoborna\ConfigBundle\ConfigEvents;
use Autoborna\ConfigBundle\Event\ConfigBuilderEvent;
use Autoborna\ConfigBundle\Event\ConfigEvent;
use Autoborna\CoreBundle\Form\Type\ConfigType;
use Autoborna\CoreBundle\Helper\LanguageHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConfigSubscriber implements EventSubscriberInterface
{
    /**
     * @var LanguageHelper
     */
    private $languageHelper;

    public function __construct(LanguageHelper $languageHelper)
    {
        $this->languageHelper = $languageHelper;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            ConfigEvents::CONFIG_ON_GENERATE => ['onConfigGenerate', 0],
            ConfigEvents::CONFIG_PRE_SAVE    => ['onConfigBeforeSave', 0],
        ];
    }

    public function onConfigGenerate(ConfigBuilderEvent $event)
    {
        $coreParams = $event->getParametersFromConfig('AutobornaCoreBundle');
        unset($coreParams['theme']);
        unset($coreParams['theme_import_allowed_extensions']);
        $event->addForm([
            'bundle'     => 'CoreBundle',
            'formType'   => ConfigType::class,
            'formAlias'  => 'coreconfig',
            'formTheme'  => 'AutobornaCoreBundle:FormTheme\Config',
            'parameters' => $coreParams,
        ]);
    }

    public function onConfigBeforeSave(ConfigEvent $event)
    {
        $values = $event->getConfig();

        // Preserve existing value
        $event->unsetIfEmpty('transifex_password');

        // Check if the selected locale has been downloaded already, fetch it if not
        if (!array_key_exists($values['coreconfig']['locale'], $this->languageHelper->getSupportedLanguages())) {
            $fetchLanguage = $this->languageHelper->extractLanguagePackage($values['coreconfig']['locale']);

            // If there is an error, fall back to 'en_US' as it is our system default
            if ($fetchLanguage['error']) {
                $values['coreconfig']['locale'] = 'en_US';
                $message                        = 'autoborna.core.could.not.set.language';
                $messageVars                    = [];

                if (isset($fetchLanguage['message'])) {
                    $message = $fetchLanguage['message'];
                }

                if (isset($fetchLanguage['vars'])) {
                    $messageVars = $fetchLanguage['vars'];
                }

                $event->setError($message, $messageVars);
            }
        }

        $event->setConfig($values);
    }
}
