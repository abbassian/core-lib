<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Exception;

use Exception;

class PluginNotConfiguredException extends Exception
{
    protected $message = 'autoborna.integration.not_configured';
}
