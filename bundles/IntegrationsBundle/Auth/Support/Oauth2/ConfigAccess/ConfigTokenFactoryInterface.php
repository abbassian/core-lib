<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Auth\Support\Oauth2\ConfigAccess;

use Autoborna\IntegrationsBundle\Auth\Provider\AuthConfigInterface;
use Autoborna\IntegrationsBundle\Auth\Support\Oauth2\Token\TokenFactoryInterface;

interface ConfigTokenFactoryInterface extends AuthConfigInterface
{
    public function getTokenFactory(): TokenFactoryInterface;
}
