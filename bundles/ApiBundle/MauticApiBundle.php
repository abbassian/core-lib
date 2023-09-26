<?php

namespace Autoborna\ApiBundle;

use Autoborna\ApiBundle\DependencyInjection\Compiler\SerializerPass;
use Autoborna\ApiBundle\DependencyInjection\Factory\ApiFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class AutobornaApiBundle.
 */
class AutobornaApiBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new SerializerPass());

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new ApiFactory());
    }
}
