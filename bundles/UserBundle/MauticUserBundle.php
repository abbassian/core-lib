<?php

namespace Autoborna\UserBundle;

use Autoborna\UserBundle\DependencyInjection\Firewall\Factory\PluginFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class AutobornaUserBundle.
 */
class AutobornaUserBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new PluginFactory());
    }
}
