<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Event;

use Autoborna\PluginBundle\Entity\Integration;
use Symfony\Component\EventDispatcher\Event;

class KeysDecryptionEvent extends Event
{
    /**
     * @var Integration
     */
    private $integrationConfiguration;

    /**
     * @var array
     */
    private $keys;

    /**
     * KeysEncryptionEvent constructor.
     */
    public function __construct(Integration $integrationConfiguration, array $keys)
    {
        $this->integrationConfiguration = $integrationConfiguration;
        $this->keys                     = $keys;
    }

    public function getIntegrationConfiguration(): Integration
    {
        return $this->integrationConfiguration;
    }

    public function getKeys(): array
    {
        return $this->keys;
    }

    public function setKeys(array $keys): void
    {
        $this->keys = $keys;
    }
}
