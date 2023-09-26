<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Auth\Provider\ApiKey\Credentials;

use Autoborna\IntegrationsBundle\Auth\Provider\AuthCredentialsInterface;

interface ParameterCredentialsInterface extends AuthCredentialsInterface
{
    public function getKeyName(): string;

    public function getApiKey(): ?string;
}
