<?php

return [
    'routes' => [
        'main' => [
            'autoborna_config_action' => [
                'path'       => '/config/{objectAction}/{objectId}',
                'controller' => 'AutobornaConfigBundle:Config:execute',
            ],
            'autoborna_sysinfo_index' => [
                'path'       => '/sysinfo',
                'controller' => 'AutobornaConfigBundle:Sysinfo:index',
            ],
        ],
    ],

    'menu' => [
        'admin' => [
            'autoborna.config.menu.index' => [
                'route'           => 'autoborna_config_action',
                'routeParameters' => ['objectAction' => 'edit'],
                'iconClass'       => 'fa-cogs',
                'id'              => 'autoborna_config_index',
                'access'          => 'admin',
            ],
            'autoborna.sysinfo.menu.index' => [
                'route'     => 'autoborna_sysinfo_index',
                'iconClass' => 'fa-life-ring',
                'id'        => 'autoborna_sysinfo_index',
                'access'    => 'admin',
                'checks'    => [
                    'parameters' => [
                        'sysinfo_disabled' => false,
                    ],
                ],
            ],
        ],
    ],

    'services' => [
        'events' => [
            'autoborna.config.subscriber' => [
                'class'     => \Autoborna\ConfigBundle\EventListener\ConfigSubscriber::class,
                'arguments' => [
                    'autoborna.config.config_change_logger',
                ],
            ],
        ],

        'forms' => [
            'autoborna.form.type.config' => [
                'class'     => \Autoborna\ConfigBundle\Form\Type\ConfigType::class,
                'arguments' => [
                    'autoborna.config.form.restriction_helper',
                    'autoborna.config.form.escape_transformer',
                ],
            ],
        ],
        'models' => [
            'autoborna.config.model.sysinfo' => [
                'class'     => \Autoborna\ConfigBundle\Model\SysinfoModel::class,
                'arguments' => [
                    'autoborna.helper.paths',
                    'autoborna.helper.core_parameters',
                    'translator',
                    'doctrine.dbal.default_connection',
                    'autoborna.install.service',
                    'autoborna.install.configurator.step.check',
                ],
            ],
        ],
        'others' => [
            'autoborna.config.mapper' => [
                'class'     => \Autoborna\ConfigBundle\Mapper\ConfigMapper::class,
                'arguments' => [
                    'autoborna.helper.core_parameters',
                ],
            ],
            'autoborna.config.form.restriction_helper' => [
                'class'     => \Autoborna\ConfigBundle\Form\Helper\RestrictionHelper::class,
                'arguments' => [
                    'translator',
                    '%autoborna.security.restrictedConfigFields%',
                    '%autoborna.security.restrictedConfigFields.displayMode%',
                ],
            ],
            'autoborna.config.config_change_logger' => [
                'class'     => \Autoborna\ConfigBundle\Service\ConfigChangeLogger::class,
                'arguments' => [
                    'autoborna.helper.ip_lookup',
                    'autoborna.core.model.auditlog',
                ],
            ],
            'autoborna.config.form.escape_transformer' => [
                'class'     => \Autoborna\ConfigBundle\Form\Type\EscapeTransformer::class,
                'arguments' => [
                    '%autoborna.config_allowed_parameters%',
                ],
            ],
        ],
    ],

    'parameters' => [
        'config_allowed_parameters' => [
            'kernel.root_dir',
            'kernel.project_dir',
            'kernel.logs_dir',
        ],
    ],
];
