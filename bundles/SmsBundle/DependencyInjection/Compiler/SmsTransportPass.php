<?php

namespace Autoborna\SmsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SmsTransportPass implements CompilerPassInterface
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    public function process(ContainerBuilder $container)
    {
        $this->container = $container;

        $this->registerTransports();
        $this->registerCallbacks();
    }

    private function registerTransports()
    {
        if (!$this->container->has('autoborna.sms.transport_chain')) {
            return;
        }

        $definition     = $this->container->getDefinition('autoborna.sms.transport_chain');
        $taggedServices = $this->container->findTaggedServiceIds('autoborna.sms_transport');
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addTransport', [
                $id,
                new Reference($id),
                !empty($tags[0]['alias']) ? $tags[0]['alias'] : $id,
                !empty($tags[0]['integrationAlias']) ? $tags[0]['integrationAlias'] : $id,
            ]);
        }
    }

    private function registerCallbacks()
    {
        if (!$this->container->has('autoborna.sms.callback_handler_container')) {
            return;
        }

        $definition     = $this->container->getDefinition('autoborna.sms.callback_handler_container');
        $taggedServices = $this->container->findTaggedServiceIds('autoborna.sms_callback_handler');
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('registerHandler', [
                new Reference($id),
            ]);
        }
    }
}
