<?php

namespace Autoborna\PluginBundle\Helper;

use Autoborna\CoreBundle\Factory\AutobornaFactory;
use Autoborna\PluginBundle\EventListener\PushToIntegrationTrait;

/**
 * Class EventHelper.
 */
class EventHelper
{
    use PushToIntegrationTrait;

    /**
     * @param $lead
     */
    public static function pushLead($config, $lead, AutobornaFactory $factory)
    {
        $contact = $factory->getEntityManager()->getRepository('AutobornaLeadBundle:Lead')->getEntityWithPrimaryCompany($lead);

        /** @var \Autoborna\PluginBundle\Helper\IntegrationHelper $integrationHelper */
        $integrationHelper = $factory->getHelper('integration');

        static::setStaticIntegrationHelper($integrationHelper);
        $errors  = [];

        return static::pushIt($config, $contact, $errors);
    }
}
