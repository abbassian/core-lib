<?php

namespace Autoborna\CoreBundle\DependencyInjection\EnvProcessor;

use Symfony\Component\DependencyInjection\EnvVarProcessorInterface;

class AutobornaConstProcessor implements EnvVarProcessorInterface
{
    public function getEnv($prefix, $name, \Closure $getEnv)
    {
        return defined($name) ? constant($name) : null;
    }

    public static function getProvidedTypes()
    {
        return [
            'autobornaconst' => 'string',
        ];
    }
}
