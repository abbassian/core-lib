<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Auth\Support\Oauth2\ConfigAccess;

use kamermans\OAuth2\Persistence\TokenPersistenceInterface as KamermansTokenPersistenceInterface;
use Autoborna\IntegrationsBundle\Auth\Provider\AuthConfigInterface;

interface ConfigTokenPersistenceInterface extends AuthConfigInterface
{
    public function getTokenPersistence(): KamermansTokenPersistenceInterface;
}
