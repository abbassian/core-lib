<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Auth\Provider\Oauth2ThreeLegged\Credentials;

use Autoborna\IntegrationsBundle\Auth\Provider\AuthCredentialsInterface;

interface AccessTokenInterface extends AuthCredentialsInterface
{
    public function getAccessToken(): ?string;

    public function getAccessTokenExpiry(): ?\DateTimeImmutable;
}
