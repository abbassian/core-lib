<?php

declare(strict_types=1);

namespace Autoborna\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PermissionsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $corePermissions = $container->findDefinition('autoborna.security');

        foreach ($container->findTaggedServiceIds('autoborna.permissions') as $id => $tags) {
            $permissionObject = $container->findDefinition($id);
            $corePermissions->addMethodCall('setPermissionObject', [$permissionObject]);
        }
    }
}
