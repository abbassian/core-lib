<?php

namespace Autoborna\CoreBundle\Loader\EnvVars;

use Symfony\Component\HttpFoundation\ParameterBag;

interface EnvVarsInterface
{
    /**
     * @param ParameterBag $config        Bag with Autoborna local parameters
     * @param ParameterBag $defaultConfig Bag with bundle and plugin's default parameters
     * @param ParameterBag $envVars       Set environment variables into this bag
     */
    public static function load(ParameterBag $config, ParameterBag $defaultConfig, ParameterBag $envVars): void;
}
