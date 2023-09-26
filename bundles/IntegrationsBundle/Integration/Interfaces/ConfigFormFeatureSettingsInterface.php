<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Integration\Interfaces;

interface ConfigFormFeatureSettingsInterface
{
    /**
     * Return the name of the form type service for the feature settings.
     */
    public function getFeatureSettingsConfigFormName(): string;
}
