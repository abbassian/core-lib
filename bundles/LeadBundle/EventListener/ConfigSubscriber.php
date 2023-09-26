<?php

namespace Autoborna\LeadBundle\EventListener;

use Autoborna\ConfigBundle\ConfigEvents;
use Autoborna\ConfigBundle\Event\ConfigBuilderEvent;
use Autoborna\LeadBundle\Form\Type\ConfigCompanyType;
use Autoborna\LeadBundle\Form\Type\ConfigType;
use Autoborna\LeadBundle\Form\Type\SegmentConfigType;
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
                ['onConfigCompanyGenerate', 0],
            ],
        ];
    }

    public function onConfigGenerate(ConfigBuilderEvent $event)
    {
        $leadParameters = $event->getParametersFromConfig('AutobornaLeadBundle');
        unset($leadParameters['company_unique_identifiers_operator']);
        $event->addForm([
            'bundle'     => 'LeadBundle',
            'formAlias'  => 'leadconfig',
            'formType'   => ConfigType::class,
            'formTheme'  => 'AutobornaLeadBundle:FormTheme\Config',
            'parameters' => $leadParameters,
        ]);

        $segmentParameters = $event->getParametersFromConfig('AutobornaLeadBundle');
        unset($segmentParameters['contact_unique_identifiers_operator'], $segmentParameters['contact_columns'], $segmentParameters['background_import_if_more_rows_than']);
        $event->addForm([
            'bundle'     => 'LeadBundle',
            'formAlias'  => 'segment_config',
            'formType'   => SegmentConfigType::class,
            'formTheme'  => 'AutobornaLeadBundle:FormTheme\Config',
            'parameters' => $segmentParameters,
        ]);
    }

    public function onConfigCompanyGenerate(ConfigBuilderEvent $event)
    {
        $parameters = $event->getParametersFromConfig('AutobornaLeadBundle');
        $event->addForm([
            'bundle'     => 'LeadBundle',
            'formAlias'  => 'companyconfig',
            'formType'   => ConfigCompanyType::class,
            'formTheme'  => 'AutobornaLeadBundle:FormTheme\Config',
            'parameters' => [
                'company_unique_identifiers_operator' => $parameters['company_unique_identifiers_operator'],
            ],
        ]);
    }
}
