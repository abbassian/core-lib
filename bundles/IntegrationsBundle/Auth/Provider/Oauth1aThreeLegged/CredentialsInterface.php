<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Auth\Provider\Oauth1aThreeLegged;

use Autoborna\IntegrationsBundle\Auth\Provider\AuthCredentialsInterface;

interface CredentialsInterface extends AuthCredentialsInterface
{
    public function getAuthorizationUrl(): string;

    public function getRequestTokenUrl(): string;

    public function getAccessTokenUrl(): string;

    public function getAuthCallbackUrl(): ?string;

    public function getConsumerId(): ?string;

    public function getConsumerSecret(): ?string;

    public function getAccessToken(): ?string;

    public function getRequestToken(): ?string;
}
