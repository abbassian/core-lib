<?php

namespace Autoborna\PageBundle\EventListener;

use Autoborna\ConfigBundle\ConfigEvents;
use Autoborna\ConfigBundle\Event\ConfigBuilderEvent;
use Autoborna\ConfigBundle\Event\ConfigEvent;
use Autoborna\PageBundle\Form\Type\ConfigTrackingPageType;
use Autoborna\PageBundle\Form\Type\ConfigType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConfigSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            ConfigEvents::CONFIG_ON_GENERATE => [
                ['onConfigGenerate', 0],
                ['onConfigGenerateTracking', 0],
            ],
            ConfigEvents::CONFIG_PRE_SAVE => ['onConfigSave', 0],
        ];
    }

    public function onConfigGenerate(ConfigBuilderEvent $event)
    {
        $event->addForm([
            'bundle'     => 'PageBundle',
            'formAlias'  => 'pageconfig',
            'formType'   => ConfigType::class,
            'formTheme'  => 'AutobornaPageBundle:FormTheme\Config',
            // parameters must be defined directly in case there are 2 config forms per bundle.
            // $event->getParametersFromConfig('AutobornaPageBundle') would return all params for PageBundle
            // and trackingconfig form would overwrote values in the pageconfig form. See #5559.
            'parameters' => [
                'cat_in_page_url'  => false,
                'google_analytics' => false,
            ],
        ]);
    }

    public function onConfigGenerateTracking(ConfigBuilderEvent $event)
    {
        $event->addForm([
            'bundle'     => 'PageBundle',
            'formAlias'  => 'trackingconfig',
            'formType'   => ConfigTrackingPageType::class,
            'formTheme'  => 'AutobornaPageBundle:FormTheme\Config',
            // parameters defined this way because of the reason as above.
            'parameters' => [
                'anonymize_ip'                          => false,
                'track_contact_by_ip'                   => false,
                'track_by_tracking_url'                 => false,
                'facebook_pixel_id'                     => null,
                'facebook_pixel_trackingpage_enabled'   => false,
                'facebook_pixel_landingpage_enabled'    => false,
                'google_analytics_id'                   => null,
                'google_analytics_trackingpage_enabled' => false,
                'google_analytics_landingpage_enabled'  => false,
                'google_analytics_anonymize_ip'         => false,
                'do_not_track_404_anonymous'            => false,
            ],
        ]);
    }

    public function onConfigSave(ConfigEvent $event)
    {
        $values = $event->getConfig();

        if (!empty($values['pageconfig']['google_analytics'])) {
            $values['pageconfig']['google_analytics'] = htmlspecialchars($values['pageconfig']['google_analytics']);
            $event->setConfig($values);
        }
    }
}
