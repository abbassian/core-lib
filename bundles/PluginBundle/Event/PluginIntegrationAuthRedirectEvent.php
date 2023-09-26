<?php

namespace Autoborna\PluginBundle\Event;

use Autoborna\PluginBundle\Integration\UnifiedIntegrationInterface;

/**
 * Class PluginIntegrationAuthRedirectEvent.
 */
class PluginIntegrationAuthRedirectEvent extends AbstractPluginIntegrationEvent
{
    /**
     * @var string
     */
    private $authUrl;

    public function __construct(UnifiedIntegrationInterface $integration, $authUrl)
    {
        $this->integration = $integration;
        $this->authUrl     = $authUrl;
    }

    /**
     * @return string
     */
    public function getAuthUrl()
    {
        return $this->authUrl;
    }

    /**
     * @param string $authUrl
     */
    public function setAuthUrl($authUrl)
    {
        $this->authUrl = $authUrl;

        $this->stopPropagation();
    }
}
