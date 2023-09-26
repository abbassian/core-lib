<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Integration\Interfaces;

interface ConfigFormAuthorizeButtonInterface
{
    public function isAuthorized(): bool;

    public function getAuthorizationUrl(): string;
}
