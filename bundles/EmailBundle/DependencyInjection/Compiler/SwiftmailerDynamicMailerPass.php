<?php

namespace Autoborna\EmailBundle\DependencyInjection\Compiler;

use Autoborna\EmailBundle\Swiftmailer\SwiftmailerTransportFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SwiftmailerDynamicMailerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('swiftmailer.mailer.default.transport.dynamic')) {
            return;
        }

        $definitionDecorator = $container->getDefinition('swiftmailer.mailer.default.transport.dynamic');
        $definitionDecorator->setFactory([SwiftmailerTransportFactory::class, 'createTransport']);
        $definitionDecorator->setArgument(3, new Reference('service_container'));
    }
}
