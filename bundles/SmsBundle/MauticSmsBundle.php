<?php

namespace Autoborna\SmsBundle;

use Autoborna\PluginBundle\Bundle\PluginBundleBase;
use Autoborna\SmsBundle\DependencyInjection\Compiler\SmsTransportPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class AutobornaSmsBundle.
 */
class AutobornaSmsBundle extends PluginBundleBase
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new SmsTransportPass());
    }
}
