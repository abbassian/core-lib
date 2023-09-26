<?php

namespace Autoborna\EmailBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class StatHelperPass.
 */
class StatHelperPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition     = $container->getDefinition('autoborna.email.stats.helper_container');
        $taggedServices = $container->findTaggedServiceIds('autoborna.email_stat_helper');
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addHelper', [
                new Reference($id),
            ]);
        }
    }
}
