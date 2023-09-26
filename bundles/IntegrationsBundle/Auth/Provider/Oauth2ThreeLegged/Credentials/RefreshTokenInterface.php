<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Auth\Provider\Oauth2ThreeLegged\Credentials;

use Autoborna\IntegrationsBundle\Auth\Provider\AuthCredentialsInterface;

interface RefreshTokenInterface extends AuthCredentialsInterface
{
    public function getRefreshToken(): ?string;
}
