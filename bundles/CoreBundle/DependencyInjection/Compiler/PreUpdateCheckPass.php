<?php

declare(strict_types=1);

namespace Autoborna\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class PreUpdateCheckPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $taggedServices       = $container->findTaggedServiceIds('autoborna.update_check');
        $preUpdateCheckHelper = $container->findDefinition('autoborna.helper.update_checks');

        foreach ($taggedServices as $id => $tags) {
            $preUpdateCheckHelper->addMethodCall('addCheck', [new Reference($id)]);
        }
    }
}
