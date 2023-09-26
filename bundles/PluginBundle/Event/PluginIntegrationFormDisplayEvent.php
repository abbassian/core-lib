<?php

namespace Autoborna\PluginBundle\Event;

use Autoborna\PluginBundle\Integration\UnifiedIntegrationInterface;

/**
 * Class PluginIntegrationFormDisplayEvent.
 */
class PluginIntegrationFormDisplayEvent extends AbstractPluginIntegrationEvent
{
    /**
     * @var string
     */
    private $settings = [];

    public function __construct(UnifiedIntegrationInterface $integration, array $settings)
    {
        $this->integration = $integration;
        $this->settings    = $settings;
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }

    public function setSettings(array $settings)
    {
        $this->settings = $settings;

        $this->stopPropagation();
    }
}
