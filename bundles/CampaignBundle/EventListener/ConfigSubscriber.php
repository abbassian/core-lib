<?php

namespace Autoborna\CampaignBundle\EventListener;

use Autoborna\CampaignBundle\Form\Type\ConfigType;
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
        $event->addForm(
            [
                'bundle'     => 'CampaignBundle',
                'formAlias'  => 'campaignconfig',
                'formType'   => ConfigType::class,
                'formTheme'  => 'AutobornaCampaignBundle:FormTheme\Config',
                'parameters' => $event->getParametersFromConfig('AutobornaCampaignBundle'),
            ]
        );
    }

    public function onConfigSave(ConfigEvent $event)
    {
        /** @var array $values */
        $values = $event->getConfig();

        // Manipulate the values
        if (!empty($values['campaignconfig']['campaign_time_wait_on_event_false'])) {
            $values['campaignconfig']['campaign_time_wait_on_event_false'] = htmlspecialchars($values['campaignconfig']['campaign_time_wait_on_event_false']);
        }

        // Set updated values
        $event->setConfig($values);
    }
}
