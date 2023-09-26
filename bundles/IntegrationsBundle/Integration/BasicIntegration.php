<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Integration;

use Autoborna\IntegrationsBundle\Integration\BC\BcIntegrationSettingsTrait;
use Autoborna\IntegrationsBundle\Integration\Interfaces\IntegrationInterface;

abstract class BasicIntegration implements IntegrationInterface
{
    use BcIntegrationSettingsTrait;
    use ConfigurationTrait;

    public function getDisplayName(): string
    {
        return $this->getName();
    }
}
