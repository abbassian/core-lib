<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SyncIntegrationsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $taggedServices         = $container->findTaggedServiceIds('autoborna.sync_integration');
        $syncIntegrationsHelper = $container->findDefinition('autoborna.integrations.helper.sync_integrations');

        foreach ($taggedServices as $id => $tags) {
            $syncIntegrationsHelper->addMethodCall('addIntegration', [new Reference($id)]);
        }

        $taggedServices   = $container->findTaggedServiceIds('autoborna.sync.notification_handler');
        $handlerContainer = $container->findDefinition('autoborna.integrations.sync.notification.handler_container');

        foreach ($taggedServices as $id => $tags) {
            $handlerContainer->addMethodCall('registerHandler', [new Reference($id)]);
        }
    }
}
