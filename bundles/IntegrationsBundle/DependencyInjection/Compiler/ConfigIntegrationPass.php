<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ConfigIntegrationPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $taggedServices     = $container->findTaggedServiceIds('autoborna.config_integration');
        $integrationsHelper = $container->findDefinition('autoborna.integrations.helper.config_integrations');

        foreach ($taggedServices as $id => $tags) {
            $integrationsHelper->addMethodCall('addIntegration', [new Reference($id)]);
        }
    }
}
