<?php

return [
    'routes' => [
        'main' => [
            'autoborna_integration_auth_callback_secure' => [
                'path'       => '/plugins/integrations/authcallback/{integration}',
                'controller' => 'AutobornaPluginBundle:Auth:authCallback',
            ],
            'autoborna_integration_auth_postauth_secure' => [
                'path'       => '/plugins/integrations/authstatus/{integration}',
                'controller' => 'AutobornaPluginBundle:Auth:authStatus',
            ],
            'autoborna_plugin_index' => [
                'path'       => '/plugins',
                'controller' => 'AutobornaPluginBundle:Plugin:index',
            ],
            'autoborna_plugin_config' => [
                'path'       => '/plugins/config/{name}/{page}',
                'controller' => 'AutobornaPluginBundle:Plugin:config',
            ],
            'autoborna_plugin_info' => [
                'path'       => '/plugins/info/{name}',
                'controller' => 'AutobornaPluginBundle:Plugin:info',
            ],
            'autoborna_plugin_reload' => [
                'path'       => '/plugins/reload',
                'controller' => 'AutobornaPluginBundle:Plugin:reload',
            ],
        ],
        'public' => [
            'autoborna_integration_auth_user' => [
                'path'       => '/plugins/integrations/authuser/{integration}',
                'controller' => 'AutobornaPluginBundle:Auth:authUser',
            ],
            'autoborna_integration_auth_callback' => [
                'path'       => '/plugins/integrations/authcallback/{integration}',
                'controller' => 'AutobornaPluginBundle:Auth:authCallback',
            ],
            'autoborna_integration_auth_postauth' => [
                'path'       => '/plugins/integrations/authstatus/{integration}',
                'controller' => 'AutobornaPluginBundle:Auth:authStatus',
            ],
        ],
    ],
    'menu' => [
        'admin' => [
            'priority' => 50,
            'items'    => [
                'autoborna.plugin.plugins' => [
                    'id'        => 'autoborna_plugin_root',
                    'iconClass' => 'fa-plus-circle',
                    'access'    => 'plugin:plugins:manage',
                    'route'     => 'autoborna_plugin_index',
                ],
            ],
        ],
    ],

    'services' => [
        'events' => [
            'autoborna.plugin.pointbundle.subscriber' => [
                'class' => \Autoborna\PluginBundle\EventListener\PointSubscriber::class,
            ],
            'autoborna.plugin.formbundle.subscriber' => [
                'class'       => \Autoborna\PluginBundle\EventListener\FormSubscriber::class,
                'methodCalls' => [
                    'setIntegrationHelper' => [
                        'autoborna.helper.integration',
                    ],
                ],
            ],
            'autoborna.plugin.campaignbundle.subscriber' => [
                'class'       => \Autoborna\PluginBundle\EventListener\CampaignSubscriber::class,
                'methodCalls' => [
                    'setIntegrationHelper' => [
                        'autoborna.helper.integration',
                    ],
                ],
            ],
            'autoborna.plugin.leadbundle.subscriber' => [
                'class'     => \Autoborna\PluginBundle\EventListener\LeadSubscriber::class,
                'arguments' => [
                    'autoborna.plugin.model.plugin',
                ],
            ],
            'autoborna.plugin.integration.subscriber' => [
                'class'     => \Autoborna\PluginBundle\EventListener\IntegrationSubscriber::class,
                'arguments' => [
                    'monolog.logger.autoborna',
                ],
            ],
        ],
        'forms' => [
            'autoborna.form.type.integration.details' => [
                'class' => \Autoborna\PluginBundle\Form\Type\DetailsType::class,
            ],
            'autoborna.form.type.integration.settings' => [
                'class'     => \Autoborna\PluginBundle\Form\Type\FeatureSettingsType::class,
                'arguments' => [
                    'session',
                    'autoborna.helper.core_parameters',
                    'monolog.logger.autoborna',
                ],
            ],
            'autoborna.form.type.integration.fields' => [
                'class'     => \Autoborna\PluginBundle\Form\Type\FieldsType::class,
            ],
            'autoborna.form.type.integration.company.fields' => [
                'class'     => \Autoborna\PluginBundle\Form\Type\CompanyFieldsType::class,
            ],
            'autoborna.form.type.integration.keys' => [
                'class' => \Autoborna\PluginBundle\Form\Type\KeysType::class,
            ],
            'autoborna.form.type.integration.list' => [
                'class'     => \Autoborna\PluginBundle\Form\Type\IntegrationsListType::class,
                'arguments' => [
                    'autoborna.helper.integration',
                ],
            ],
            'autoborna.form.type.integration.config' => [
                'class' => \Autoborna\PluginBundle\Form\Type\IntegrationConfigType::class,
            ],
            'autoborna.form.type.integration.campaign' => [
                'class' => \Autoborna\PluginBundle\Form\Type\IntegrationCampaignsType::class,
            ],
        ],
        'other' => [
            'autoborna.helper.integration' => [
                'class'     => \Autoborna\PluginBundle\Helper\IntegrationHelper::class,
                'arguments' => [
                    'service_container',
                    'doctrine.orm.entity_manager',
                    'autoborna.helper.paths',
                    'autoborna.helper.bundle',
                    'autoborna.helper.core_parameters',
                    'autoborna.helper.templating',
                    'autoborna.plugin.model.plugin',
                ],
            ],
            'autoborna.plugin.helper.reload' => [
                'class'     => \Autoborna\PluginBundle\Helper\ReloadHelper::class,
                'arguments' => [
                    'event_dispatcher',
                    'autoborna.factory',
                ],
            ],
        ],
        'facades' => [
            'autoborna.plugin.facade.reload' => [
                'class'     => \Autoborna\PluginBundle\Facade\ReloadFacade::class,
                'arguments' => [
                    'autoborna.plugin.model.plugin',
                    'autoborna.plugin.helper.reload',
                    'translator',
                ],
            ],
        ],
        'models' => [
            'autoborna.plugin.model.plugin' => [
                'class'     => \Autoborna\PluginBundle\Model\PluginModel::class,
                'arguments' => [
                    'autoborna.lead.model.field',
                    'autoborna.helper.core_parameters',
                    'autoborna.helper.bundle',
                ],
            ],

            'autoborna.plugin.model.integration_entity' => [
                'class' => Autoborna\PluginBundle\Model\IntegrationEntityModel::class,
            ],
        ],
    ],
];
