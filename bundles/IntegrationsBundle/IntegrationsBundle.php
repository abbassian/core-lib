<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle;

use Autoborna\IntegrationsBundle\Bundle\AbstractPluginBundle;
use Autoborna\IntegrationsBundle\DependencyInjection\Compiler\AuthenticationIntegrationPass;
use Autoborna\IntegrationsBundle\DependencyInjection\Compiler\BuilderIntegrationPass;
use Autoborna\IntegrationsBundle\DependencyInjection\Compiler\ConfigIntegrationPass;
use Autoborna\IntegrationsBundle\DependencyInjection\Compiler\IntegrationsPass;
use Autoborna\IntegrationsBundle\DependencyInjection\Compiler\SyncIntegrationsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class IntegrationsBundle extends AbstractPluginBundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new IntegrationsPass());
        $container->addCompilerPass(new AuthenticationIntegrationPass());
        $container->addCompilerPass(new SyncIntegrationsPass());
        $container->addCompilerPass(new ConfigIntegrationPass());
        $container->addCompilerPass(new BuilderIntegrationPass());
    }
}
