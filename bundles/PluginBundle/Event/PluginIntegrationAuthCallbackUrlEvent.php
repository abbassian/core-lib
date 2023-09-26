<?php

namespace Autoborna\PluginBundle\Event;

use Autoborna\PluginBundle\Integration\UnifiedIntegrationInterface;

/**
 * Class PluginIntegrationAuthCallbackUrlEvent.
 */
class PluginIntegrationAuthCallbackUrlEvent extends AbstractPluginIntegrationEvent
{
    /**
     * @var string
     */
    private $callbackUrl;

    public function __construct(UnifiedIntegrationInterface $integration, $callbackUrl)
    {
        $this->integration = $integration;
        $this->callbackUrl = $callbackUrl;
    }

    /**
     * @return string
     */
    public function getCallbackUrl()
    {
        return $this->callbackUrl;
    }

    /**
     * @param string $callbackUrl
     */
    public function setCallbackUrl($callbackUrl)
    {
        $this->callbackUrl = $callbackUrl;

        $this->stopPropagation();
    }
}
