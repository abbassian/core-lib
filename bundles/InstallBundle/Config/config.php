<?php

return [
    'routes' => [
        'public' => [
            'autoborna_installer_home' => [
                'path'       => '/installer',
                'controller' => 'AutobornaInstallBundle:Install:step',
            ],
            'autoborna_installer_remove_slash' => [
                'path'       => '/installer/',
                'controller' => 'AutobornaCoreBundle:Common:removeTrailingSlash',
            ],
            'autoborna_installer_step' => [
                'path'       => '/installer/step/{index}',
                'controller' => 'AutobornaInstallBundle:Install:step',
            ],
            'autoborna_installer_final' => [
                'path'       => '/installer/final',
                'controller' => 'AutobornaInstallBundle:Install:final',
            ],
            'autoborna_installer_catchcall' => [
                'path'         => '/installer/{noerror}',
                'controller'   => 'AutobornaInstallBundle:Install:step',
                'requirements' => [
                    'noerror' => '^(?).+',
                ],
            ],
        ],
    ],

    'services' => [
        'fixtures' => [
            'autoborna.install.fixture.lead_field' => [
                'class'     => \Autoborna\InstallBundle\InstallFixtures\ORM\LeadFieldData::class,
                'tag'       => \Doctrine\Bundle\FixturesBundle\DependencyInjection\CompilerPass\FixturesCompilerPass::FIXTURE_TAG,
                'arguments' => [],
            ],
            'autoborna.install.fixture.role' => [
                'class'     => \Autoborna\InstallBundle\InstallFixtures\ORM\RoleData::class,
                'tag'       => \Doctrine\Bundle\FixturesBundle\DependencyInjection\CompilerPass\FixturesCompilerPass::FIXTURE_TAG,
                'arguments' => [],
            ],
            'autoborna.install.fixture.report_data' => [
                'class'     => \Autoborna\InstallBundle\InstallFixtures\ORM\LoadReportData::class,
                'tag'       => \Doctrine\Bundle\FixturesBundle\DependencyInjection\CompilerPass\FixturesCompilerPass::FIXTURE_TAG,
                'arguments' => [],
            ],
            'autoborna.install.fixture.grape_js' => [
                'class'     => \Autoborna\InstallBundle\InstallFixtures\ORM\GrapesJsData::class,
                'tag'       => \Doctrine\Bundle\FixturesBundle\DependencyInjection\CompilerPass\FixturesCompilerPass::FIXTURE_TAG,
                'arguments' => [],
            ],
        ],
        'forms' => [
            \Autoborna\InstallBundle\Configurator\Form\CheckStepType::class => [
                'class' => \Autoborna\InstallBundle\Configurator\Form\CheckStepType::class,
            ],
            \Autoborna\InstallBundle\Configurator\Form\DoctrineStepType::class => [
                'class' => \Autoborna\InstallBundle\Configurator\Form\DoctrineStepType::class,
            ],
            \Autoborna\InstallBundle\Configurator\Form\EmailStepType::class => [
                'class'     => \Autoborna\InstallBundle\Configurator\Form\EmailStepType::class,
                'arguments' => [
                    'translator',
                    'autoborna.email.transport_type',
                ],
            ],
            \Autoborna\InstallBundle\Configurator\Form\UserStepType::class => [
                'class'     => \Autoborna\InstallBundle\Configurator\Form\UserStepType::class,
                'arguments' => ['session'],
            ],
        ],
        'other' => [
            'autoborna.install.configurator.step.check' => [
                'class'     => \Autoborna\InstallBundle\Configurator\Step\CheckStep::class,
                'arguments' => [
                    'autoborna.configurator',
                    '%kernel.root_dir%',
                    'request_stack',
                    'autoborna.cipher.openssl',
                ],
                'tag'          => 'autoborna.configurator.step',
                'tagArguments' => [
                    'priority' => 0,
                ],
            ],
            'autoborna.install.configurator.step.doctrine' => [
                'class'     => \Autoborna\InstallBundle\Configurator\Step\DoctrineStep::class,
                'arguments' => [
                    'autoborna.configurator',
                ],
                'tag'          => 'autoborna.configurator.step',
                'tagArguments' => [
                    'priority' => 1,
                ],
            ],
            'autoborna.install.configurator.step.email' => [
                'class'     => \Autoborna\InstallBundle\Configurator\Step\EmailStep::class,
                'arguments' => [
                    'session',
                ],
                'tag'          => 'autoborna.configurator.step',
                'tagArguments' => [
                    'priority' => 3,
                ],
            ],
            'autoborna.install.configurator.step.user' => [
                'class'        => \Autoborna\InstallBundle\Configurator\Step\UserStep::class,
                'tag'          => 'autoborna.configurator.step',
                'tagArguments' => [
                    'priority' => 2,
                ],
            ],
            'autoborna.install.service' => [
                'class'     => 'Autoborna\InstallBundle\Install\InstallService',
                'arguments' => [
                    'autoborna.configurator',
                    'autoborna.helper.cache',
                    'autoborna.helper.paths',
                    'doctrine.orm.entity_manager',
                    'translator',
                    'kernel',
                    'validator',
                    'security.password_encoder',
                ],
            ],
            'autoborna.install.leadcolumns' => [
                'class'     => \Autoborna\InstallBundle\EventListener\DoctrineEventSubscriber::class,
                'tag'       => 'doctrine.event_subscriber',
                'arguments' => [],
            ],
        ],
    ],
];
