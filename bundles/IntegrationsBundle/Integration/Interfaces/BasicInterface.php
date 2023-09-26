<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Integration\Interfaces;

use Autoborna\PluginBundle\Integration\UnifiedIntegrationInterface;

interface BasicInterface extends UnifiedIntegrationInterface
{
    /**
     * Return the integration's name.
     */
    public function getName(): string;

    public function getIcon(): string;
}
