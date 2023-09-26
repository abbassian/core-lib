<?php

namespace Autoborna\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class UpdateStepPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('autoborna.update.step_provider')) {
            return;
        }

        $definition     = $container->getDefinition('autoborna.update.step_provider');
        $taggedServices = $container->findTaggedServiceIds('autoborna.update_step');
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addStep', [new Reference($id)]);
        }
    }
}
