<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Auth\Support\Oauth2\Token;

use kamermans\OAuth2\Token\RawToken;
use Autoborna\IntegrationsBundle\Helper\IntegrationsHelper;
use Autoborna\PluginBundle\Entity\Integration;

class TokenPersistenceFactory
{
    /**
     * @var IntegrationsHelper
     */
    private $integrationsHelper;

    public function __construct(IntegrationsHelper $integrationsHelper)
    {
        $this->integrationsHelper = $integrationsHelper;
    }

    public function create(Integration $integration): TokenPersistence
    {
        $tokenPersistence = new TokenPersistence($this->integrationsHelper);

        $tokenPersistence->setIntegration($integration);

        $apiKeys = $integration->getApiKeys();

        $token = new RawToken(
            $apiKeys['access_token'] ?? null,
            $apiKeys['refresh_token'] ?? null,
            $apiKeys['expires_at'] ?? null
        );

        $tokenPersistence->restoreToken($token);

        return $tokenPersistence;
    }
}
