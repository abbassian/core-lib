<?php

declare(strict_types=1);

use Autoborna\MarketplaceBundle\Service\Config;
use Autoborna\MarketplaceBundle\Service\RouteProvider;

return [
    'routes' => [
        'main' => [
            RouteProvider::ROUTE_LIST => [
                'path'       => '/marketplace/{page}',
                'controller' => 'MarketplaceBundle:Package\List:list',
                'method'     => 'GET|POST',
                'defaults'   => ['page' => 1],
            ],
            RouteProvider::ROUTE_DETAIL => [
                'path'       => '/marketplace/detail/{vendor}/{package}',
                'controller' => 'MarketplaceBundle:Package\Detail:view',
                'method'     => 'GET',
            ],
            RouteProvider::ROUTE_INSTALL => [
                'path'       => '/marketplace/install/{vendor}/{package}',
                'controller' => 'MarketplaceBundle:Package\Install:view',
                'method'     => 'GET|POST',
            ],
            RouteProvider::ROUTE_REMOVE => [
                'path'       => '/marketplace/remove/{vendor}/{package}',
                'controller' => 'MarketplaceBundle:Package\Remove:view',
                'method'     => 'GET|POST',
            ],
            RouteProvider::ROUTE_CLEAR_CACHE => [
                'path'       => '/marketplace/clear/cache',
                'controller' => 'MarketplaceBundle:Cache:clear',
                'method'     => 'GET',
            ],
        ],
    ],
    'services' => [
        'controllers' => [
            'marketplace.controller.package.list' => [
                'class'     => \Autoborna\MarketplaceBundle\Controller\Package\ListController::class,
                'arguments' => [
                    'marketplace.service.plugin_collector',
                    'request_stack',
                    'marketplace.service.route_provider',
                    'autoborna.security',
                    'marketplace.service.config',
                ],
                'methodCalls' => [
                    'setContainer' => [
                        '@service_container',
                    ],
                ],
            ],
            'marketplace.controller.package.detail' => [
                'class'     => \Autoborna\MarketplaceBundle\Controller\Package\DetailController::class,
                'arguments' => [
                    'marketplace.model.package',
                    'marketplace.service.route_provider',
                    'autoborna.security',
                    'marketplace.service.config',
                    'autoborna.helper.composer',
                ],
                'methodCalls' => [
                    'setContainer' => [
                        '@service_container',
                    ],
                ],
            ],
            'marketplace.controller.package.install' => [
                'class'     => \Autoborna\MarketplaceBundle\Controller\Package\InstallController::class,
                'arguments' => [
                    'marketplace.model.package',
                    'marketplace.service.route_provider',
                    'autoborna.security',
                    'marketplace.service.config',
                ],
                'methodCalls' => [
                    'setContainer' => [
                        '@service_container',
                    ],
                ],
            ],
            'marketplace.controller.package.remove' => [
                'class'     => \Autoborna\MarketplaceBundle\Controller\Package\RemoveController::class,
                'arguments' => [
                    'marketplace.model.package',
                    'marketplace.service.route_provider',
                    'autoborna.security',
                    'marketplace.service.config',
                ],
                'methodCalls' => [
                    'setContainer' => [
                        '@service_container',
                    ],
                ],
            ],
            'marketplace.controller.cache' => [
                'class'     => \Autoborna\MarketplaceBundle\Controller\CacheController::class,
                'arguments' => [
                    'autoborna.security',
                    'marketplace.service.config',
                    'marketplace.service.allowlist',
                ],
                'methodCalls' => [
                    'setContainer' => [
                        '@service_container',
                    ],
                ],
            ],
            'marketplace.controller.ajax' => [
                'class'     => \Autoborna\MarketplaceBundle\Controller\AjaxController::class,
                'arguments' => [
                    'autoborna.helper.composer',
                    'autoborna.helper.cache',
                    'monolog.logger.autoborna',
                ],
            ],
        ],
        'commands' => [
            'marketplace.command.list' => [
                'class'     => \Autoborna\MarketplaceBundle\Command\ListCommand::class,
                'tag'       => 'console.command',
                'arguments' => ['marketplace.service.plugin_collector'],
            ],
            'marketplace.command.install' => [
                'class'     => \Autoborna\MarketplaceBundle\Command\InstallCommand::class,
                'tag'       => 'console.command',
                'arguments' => ['autoborna.helper.composer', 'marketplace.model.package'],
            ],
            'marketplace.command.remove' => [
                'class'     => \Autoborna\MarketplaceBundle\Command\RemoveCommand::class,
                'tag'       => 'console.command',
                'arguments' => ['autoborna.helper.composer', 'monolog.logger.autoborna'],
            ],
        ],
        'events' => [
            'marketplace.menu.subscriber' => [
                'class'     => \Autoborna\MarketplaceBundle\EventListener\MenuSubscriber::class,
                'arguments' => [
                    'marketplace.service.config',
                ],
            ],
        ],
        'permissions' => [
            'marketplace.permissions' => [
                'class'     => \Autoborna\MarketplaceBundle\Security\Permissions\MarketplacePermissions::class,
                'arguments' => [
                    'autoborna.helper.core_parameters',
                    'marketplace.service.config',
                ],
            ],
        ],
        'api' => [
            'marketplace.api.connection' => [
                'class'     => \Autoborna\MarketplaceBundle\Api\Connection::class,
                'arguments' => [
                    'autoborna.http.client',
                    'monolog.logger.autoborna',
                ],
            ],
        ],
        'models' => [
            'marketplace.model.package' => [
                'class'     => \Autoborna\MarketplaceBundle\Model\PackageModel::class,
                'arguments' => ['marketplace.api.connection', 'marketplace.service.allowlist'],
            ],
        ],
        'other' => [
            'marketplace.service.plugin_collector' => [
                'class'     => \Autoborna\MarketplaceBundle\Service\PluginCollector::class,
                'arguments' => [
                    'marketplace.api.connection',
                    'marketplace.service.allowlist',
                ],
            ],
            'marketplace.service.route_provider' => [
                'class'     => \Autoborna\MarketplaceBundle\Service\RouteProvider::class,
                'arguments' => ['router'],
            ],
            'marketplace.service.config' => [
                'class'     => \Autoborna\MarketplaceBundle\Service\Config::class,
                'arguments' => [
                    'autoborna.helper.core_parameters',
                ],
            ],
            'marketplace.service.allowlist' => [
                'class'     => \Autoborna\MarketplaceBundle\Service\Allowlist::class,
                'arguments' => [
                    'marketplace.service.config',
                    'autoborna.cache.provider',
                    'autoborna.http.client',
                ],
            ],
        ],
    ],
    // NOTE: when adding new parameters here, please add them to the developer documentation as well:
    'parameters' => [
        Config::MARKETPLACE_ENABLED                     => true,
        Config::MARKETPLACE_ALLOWLIST_URL               => 'https://raw.githubusercontent.com/autoborna/marketplace-allowlist/main/allowlist.json',
        Config::MARKETPLACE_ALLOWLIST_CACHE_TTL_SECONDS => 3600,
    ],
];
