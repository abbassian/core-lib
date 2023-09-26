<?php

declare(strict_types=1);

namespace Autoborna\InstallBundle\Exception;

class AlreadyInstalledException extends \Exception
{
    protected $message = 'Autoborna is already installed.';
}
