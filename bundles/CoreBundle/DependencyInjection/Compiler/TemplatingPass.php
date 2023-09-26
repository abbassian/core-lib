<?php

namespace Autoborna\CoreBundle\DependencyInjection\Compiler;

use Autoborna\CoreBundle\Templating\Engine\PhpEngine;
use Autoborna\CoreBundle\Templating\Helper\AssetsHelper;
use Autoborna\CoreBundle\Templating\Helper\FormHelper;
use Autoborna\CoreBundle\Templating\Helper\SlotsHelper;
use Autoborna\CoreBundle\Templating\Helper\TranslatorHelper;
use Autoborna\CoreBundle\Templating\TemplateNameParser;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class TemplatingPass.
 */
class TemplatingPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('templating')) {
            return;
        }

        if ($container->hasDefinition('templating.helper.assets')) {
            $container->getDefinition('templating.helper.assets')
                ->setClass(AssetsHelper::class)
                ->addMethodCall('setPathsHelper', [new Reference('autoborna.helper.paths')])
                ->addMethodCall('setAssetHelper', [new Reference('autoborna.helper.assetgeneration')])
                ->addMethodCall('setBuilderIntegrationsHelper', [new Reference('autoborna.integrations.helper.builder_integrations')])
                ->addMethodCall('setInstallService', [new Reference('autoborna.install.service')])
                ->addMethodCall('setSiteUrl', ['%autoborna.site_url%'])
                ->addMethodCall('setVersion', ['%autoborna.secret_key%', MAUTIC_VERSION])
                ->setPublic(true);
        }

        if ($container->hasDefinition('templating.engine.php')) {
            $container->getDefinition('templating.engine.php')
                ->setClass(PhpEngine::class)
                ->addMethodCall(
                    'setDispatcher',
                    [new Reference('event_dispatcher')]
                )
                ->addMethodCall(
                    'setRequestStack',
                    [new Reference('request_stack')]
                )
                ->setPublic(true);
        }

        if ($container->hasDefinition('debug.templating.engine.php')) {
            $container->getDefinition('debug.templating.engine.php')
                ->setClass(PhpEngine::class)
                ->addMethodCall(
                    'setDispatcher',
                    [new Reference('event_dispatcher')]
                )
                ->addMethodCall(
                    'setRequestStack',
                    [new Reference('request_stack')]
                )
                ->setPublic(true);
        }

        if ($container->hasDefinition('templating.helper.slots')) {
            $container->getDefinition('templating.helper.slots')
                ->setClass(SlotsHelper::class)
                ->setPublic(true);
        }

        if ($container->hasDefinition('templating.name_parser')) {
            $container->getDefinition('templating.name_parser')
                ->setClass(TemplateNameParser::class)
                ->setPublic(true);
        }

        if ($container->hasDefinition('templating.helper.form')) {
            $container->getDefinition('templating.helper.form')
                ->setClass(FormHelper::class)
                ->setPublic(true);
        }

        if ($container->hasDefinition('templating.helper.translator')) {
            $container->getDefinition('templating.helper.translator')
                ->setClass(TranslatorHelper::class)
                ->setPublic(true);
        }
    }
}
