<?php

namespace Autoborna\EmailBundle;

use Autoborna\EmailBundle\DependencyInjection\Compiler\EmailTransportPass;
use Autoborna\EmailBundle\DependencyInjection\Compiler\SpoolTransportPass;
use Autoborna\EmailBundle\DependencyInjection\Compiler\StatHelperPass;
use Autoborna\EmailBundle\DependencyInjection\Compiler\SwiftmailerDynamicMailerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class AutobornaEmailBundle.
 */
class AutobornaEmailBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new SpoolTransportPass());
        $container->addCompilerPass(new EmailTransportPass());
        $container->addCompilerPass(new SwiftmailerDynamicMailerPass());
        $container->addCompilerPass(new StatHelperPass());
    }
}
