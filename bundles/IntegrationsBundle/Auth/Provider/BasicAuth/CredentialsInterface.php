<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Auth\Provider\BasicAuth;

use Autoborna\IntegrationsBundle\Auth\Provider\AuthCredentialsInterface;

interface CredentialsInterface extends AuthCredentialsInterface
{
    public function getUsername(): ?string;

    public function getPassword(): ?string;
}
