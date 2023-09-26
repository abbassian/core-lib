<?php

declare(strict_types=1);

return [
    'name'        => 'Integrations',
    'description' => 'Adds support for plugin integrations',
    'author'      => 'Autoborna, Inc.',
    'routes'      => [
        'main' => [
            'autoborna_integration_config' => [
                'path'       => '/integration/{integration}/config',
                'controller' => 'IntegrationsBundle:Config:edit',
            ],
            'autoborna_integration_config_field_pagination' => [
                'path'       => '/integration/{integration}/config/{object}/{page}',
                'controller' => 'IntegrationsBundle:FieldPagination:paginate',
                'defaults'   => [
                    'page' => 1,
                ],
            ],
            'autoborna_integration_config_field_update' => [
                'path'       => '/integration/{integration}/config/{object}/field/{field}',
                'controller' => 'IntegrationsBundle:UpdateField:update',
            ],
        ],
        'public' => [
            'autoborna_integration_public_callback' => [
                'path'       => '/integration/{integration}/callback',
                'controller' => 'IntegrationsBundle:Auth:callback',
            ],
        ],
    ],
    'services' => [
        'commands' => [
            'autoborna.integrations.command.sync' => [
                'class'     => \Autoborna\IntegrationsBundle\Command\SyncCommand::class,
                'arguments' => [
                    'autoborna.integrations.sync.service',
                    'autoborna.helper.core_parameters',
                ],
                'tag' => 'console.command',
            ],
        ],
        'events' => [
            'autoborna.integrations.subscriber.lead' => [
                'class'     => \Autoborna\IntegrationsBundle\EventListener\LeadSubscriber::class,
                'arguments' => [
                    'autoborna.integrations.repository.field_change',
                    'autoborna.integrations.repository.object_mapping',
                    'autoborna.integrations.helper.variable_expresser',
                    'autoborna.integrations.helper.sync_integrations',
                ],
            ],
            'autoborna.integrations.subscriber.contact_object' => [
                'class'     => \Autoborna\IntegrationsBundle\EventListener\ContactObjectSubscriber::class,
                'arguments' => [
                    'autoborna.integrations.helper.contact_object',
                    'router',
                ],
            ],
            'autoborna.integrations.subscriber.company_object' => [
                'class'     => \Autoborna\IntegrationsBundle\EventListener\CompanyObjectSubscriber::class,
                'arguments' => [
                    'autoborna.integrations.helper.company_object',
                    'router',
                ],
            ],
            'autoborna.integrations.subscriber.controller' => [
                'class'     => \Autoborna\IntegrationsBundle\EventListener\ControllerSubscriber::class,
                'arguments' => [
                    'autoborna.integrations.helper',
                    'controller_resolver',
                ],
            ],
            'autoborna.integrations.subscriber.ui_contact_integrations_tab' => [
                'class'     => \Autoborna\IntegrationsBundle\EventListener\UIContactIntegrationsTabSubscriber::class,
                'arguments' => [
                    'autoborna.integrations.repository.object_mapping',
                ],
            ],
            'autoborna.integrations.subscriber.contact_timeline_events' => [
                'class'     => \Autoborna\IntegrationsBundle\EventListener\TimelineSubscriber::class,
                'arguments' => [
                    'autoborna.lead.repository.lead_event_log',
                    'translator',
                ],
            ],
            'autoborna.integrations.subscriber.email_subscriber' => [
                'class'     => \Autoborna\IntegrationsBundle\EventListener\EmailSubscriber::class,
                'arguments' => [
                    'translator',
                    'event_dispatcher',
                    'autoborna.integrations.token.parser',
                    'autoborna.integrations.repository.object_mapping',
                    'autoborna.helper.integration',
                ],
            ],
        ],
        'forms' => [
            'autoborna.integrations.form.config.integration' => [
                'class'     => \Autoborna\IntegrationsBundle\Form\Type\IntegrationConfigType::class,
                'arguments' => [
                    'autoborna.integrations.helper.config_integrations',
                ],
            ],
            'autoborna.integrations.form.config.feature_settings' => [
                'class' => \Autoborna\IntegrationsBundle\Form\Type\IntegrationFeatureSettingsType::class,
            ],
            'autoborna.integrations.form.config.sync_settings' => [
                'class' => \Autoborna\IntegrationsBundle\Form\Type\IntegrationSyncSettingsType::class,
            ],
            'autoborna.integrations.form.config.sync_settings_field_mappings' => [
                'class'     => \Autoborna\IntegrationsBundle\Form\Type\IntegrationSyncSettingsFieldMappingsType::class,
                'arguments' => [
                    'monolog.logger.autoborna',
                    'translator',
                ],
            ],
            'autoborna.integrations.form.config.sync_settings_object_field_directions' => [
                'class' => \Autoborna\IntegrationsBundle\Form\Type\IntegrationSyncSettingsObjectFieldType::class,
            ],
            'autoborna.integrations.form.config.sync_settings_object_field_mapping' => [
                'class'     => \Autoborna\IntegrationsBundle\Form\Type\IntegrationSyncSettingsObjectFieldMappingType::class,
                'arguments' => [
                    'translator',
                    'autoborna.integrations.sync.data_exchange.autoborna.field_helper',
                ],
            ],
            'autoborna.integrations.form.config.sync_settings_object_field' => [
                'class' => \Autoborna\IntegrationsBundle\Form\Type\IntegrationSyncSettingsObjectFieldType::class,
            ],
            'autoborna.integrations.form.config.feature_settings.activity_list' => [
                'class'     => \Autoborna\IntegrationsBundle\Form\Type\ActivityListType::class,
                'arguments' => [
                    'autoborna.lead.model.lead',
                ],
            ],
        ],
        'helpers' => [
            'autoborna.integrations.helper.variable_expresser' => [
                'class' => \Autoborna\IntegrationsBundle\Sync\VariableExpresser\VariableExpresserHelper::class,
            ],
            'autoborna.integrations.helper' => [
                'class'     => \Autoborna\IntegrationsBundle\Helper\IntegrationsHelper::class,
                'arguments' => [
                    'autoborna.plugin.integrations.repository.integration',
                    'autoborna.integrations.service.encryption',
                    'event_dispatcher',
                ],
            ],
            'autoborna.integrations.helper.auth_integrations' => [
                'class'     => \Autoborna\IntegrationsBundle\Helper\AuthIntegrationsHelper::class,
                'arguments' => [
                    'autoborna.integrations.helper',
                ],
            ],
            'autoborna.integrations.helper.sync_integrations' => [
                'class'     => \Autoborna\IntegrationsBundle\Helper\SyncIntegrationsHelper::class,
                'arguments' => [
                    'autoborna.integrations.helper',
                    'autoborna.integrations.internal.object_provider',
                ],
            ],
            'autoborna.integrations.helper.config_integrations' => [
                'class'     => \Autoborna\IntegrationsBundle\Helper\ConfigIntegrationsHelper::class,
                'arguments' => [
                    'autoborna.integrations.helper',
                ],
            ],
            'autoborna.integrations.helper.builder_integrations' => [
                'class'     => \Autoborna\IntegrationsBundle\Helper\BuilderIntegrationsHelper::class,
                'arguments' => [
                    'autoborna.integrations.helper',
                ],
            ],
            'autoborna.integrations.helper.field_validator' => [
                'class'     => \Autoborna\IntegrationsBundle\Helper\FieldValidationHelper::class,
                'arguments' => [
                    'autoborna.integrations.sync.data_exchange.autoborna.field_helper',
                    'translator',
                ],
            ],
        ],
        'other' => [
            'autoborna.integrations.service.encryption' => [
                'class'     => \Autoborna\IntegrationsBundle\Facade\EncryptionService::class,
                'arguments' => [
                    'autoborna.helper.encryption',
                ],
            ],
            'autoborna.integrations.internal.object_provider' => [
                'class'     => \Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Internal\ObjectProvider::class,
                'arguments' => [
                    'event_dispatcher',
                ],
            ],
            'autoborna.integrations.sync.notification.helper.owner_provider' => [
                'class'     => \Autoborna\IntegrationsBundle\Sync\Notification\Helper\OwnerProvider::class,
                'arguments' => [
                    'event_dispatcher',
                    'autoborna.integrations.internal.object_provider',
                ],
            ],
            'autoborna.integrations.auth_provider.api_key' => [
                'class' => \Autoborna\IntegrationsBundle\Auth\Provider\ApiKey\HttpFactory::class,
            ],
            'autoborna.integrations.auth_provider.basic_auth' => [
                'class' => \Autoborna\IntegrationsBundle\Auth\Provider\BasicAuth\HttpFactory::class,
            ],
            'autoborna.integrations.auth_provider.oauth1atwolegged' => [
                'class' => \Autoborna\IntegrationsBundle\Auth\Provider\Oauth1aTwoLegged\HttpFactory::class,
            ],
            'autoborna.integrations.auth_provider.oauth2twolegged' => [
                'class' => \Autoborna\IntegrationsBundle\Auth\Provider\Oauth2TwoLegged\HttpFactory::class,
            ],
            'autoborna.integrations.auth_provider.oauth2threelegged' => [
                'class' => \Autoborna\IntegrationsBundle\Auth\Provider\Oauth2ThreeLegged\HttpFactory::class,
            ],
            'autoborna.integrations.auth_provider.token_persistence_factory' => [
                'class'     => \Autoborna\IntegrationsBundle\Auth\Support\Oauth2\Token\TokenPersistenceFactory::class,
                'arguments' => ['autoborna.integrations.helper'],
            ],
            'autoborna.integrations.token.parser' => [
                'class' => \Autoborna\IntegrationsBundle\Helper\TokenParser::class,
            ],
        ],
        'repositories' => [
            'autoborna.integrations.repository.field_change' => [
                'class'     => \Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Autoborna\IntegrationsBundle\Entity\FieldChange::class,
                ],
            ],
            'autoborna.integrations.repository.object_mapping' => [
                'class'     => \Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Autoborna\IntegrationsBundle\Entity\ObjectMapping::class,
                ],
            ],
            // Placeholder till the plugin bundle implements this
            'autoborna.plugin.integrations.repository.integration' => [
                'class'     => \Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Autoborna\PluginBundle\Entity\Integration::class,
                ],
            ],
        ],
        'sync' => [
            'autoborna.sync.logger' => [
                'class'     => \Autoborna\IntegrationsBundle\Sync\Logger\DebugLogger::class,
                'arguments' => [
                    'monolog.logger.autoborna',
                ],
            ],
            'autoborna.integrations.helper.sync_judge' => [
                'class' => \Autoborna\IntegrationsBundle\Sync\SyncJudge\SyncJudge::class,
            ],
            'autoborna.integrations.helper.contact_object' => [
                'class'     => \Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Internal\ObjectHelper\ContactObjectHelper::class,
                'arguments' => [
                    'autoborna.lead.model.lead',
                    'autoborna.lead.repository.lead',
                    'doctrine.dbal.default_connection',
                    'autoborna.lead.model.field',
                    'autoborna.lead.model.dnc',
                    'autoborna.lead.model.company',
                ],
            ],
            'autoborna.integrations.helper.company_object' => [
                'class'     => \Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Internal\ObjectHelper\CompanyObjectHelper::class,
                'arguments' => [
                    'autoborna.lead.model.company',
                    'autoborna.lead.repository.company',
                    'doctrine.dbal.default_connection',
                ],
            ],
            'autoborna.integrations.sync.data_exchange.autoborna.order_executioner' => [
                'class'     => \Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Internal\Executioner\OrderExecutioner::class,
                'arguments' => [
                    'autoborna.integrations.helper.sync_mapping',
                    'event_dispatcher',
                    'autoborna.integrations.internal.object_provider',
                ],
            ],
            'autoborna.integrations.sync.data_exchange.autoborna.field_helper' => [
                'class'     => \Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Helper\FieldHelper::class,
                'arguments' => [
                    'autoborna.lead.model.field',
                    'autoborna.integrations.helper.variable_expresser',
                    'autoborna.channel.helper.channel_list',
                    'translator',
                    'event_dispatcher',
                    'autoborna.integrations.internal.object_provider',
                ],
            ],
            'autoborna.integrations.sync.sync_process.value_helper' => [
                'class'     => \Autoborna\IntegrationsBundle\Sync\SyncProcess\Direction\Helper\ValueHelper::class,
                'arguments' => [],
            ],
            'autoborna.integrations.sync.data_exchange.autoborna.field_builder' => [
                'class'     => \Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Internal\ReportBuilder\FieldBuilder::class,
                'arguments' => [
                    'router',
                    'autoborna.integrations.sync.data_exchange.autoborna.field_helper',
                    'autoborna.integrations.helper.contact_object',
                ],
            ],
            'autoborna.integrations.sync.data_exchange.autoborna.full_object_report_builder' => [
                'class'     => \Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Internal\ReportBuilder\FullObjectReportBuilder::class,
                'arguments' => [
                    'autoborna.integrations.sync.data_exchange.autoborna.field_builder',
                    'autoborna.integrations.internal.object_provider',
                    'event_dispatcher',
                ],
            ],
            'autoborna.integrations.sync.data_exchange.autoborna.partial_object_report_builder' => [
                'class'     => \Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Internal\ReportBuilder\PartialObjectReportBuilder::class,
                'arguments' => [
                    'autoborna.integrations.repository.field_change',
                    'autoborna.integrations.sync.data_exchange.autoborna.field_helper',
                    'autoborna.integrations.sync.data_exchange.autoborna.field_builder',
                    'autoborna.integrations.internal.object_provider',
                    'event_dispatcher',
                ],
            ],
            'autoborna.integrations.sync.data_exchange.autoborna' => [
                'class'     => \Autoborna\IntegrationsBundle\Sync\SyncDataExchange\AutobornaSyncDataExchange::class,
                'arguments' => [
                    'autoborna.integrations.repository.field_change',
                    'autoborna.integrations.sync.data_exchange.autoborna.field_helper',
                    'autoborna.integrations.helper.sync_mapping',
                    'autoborna.integrations.sync.data_exchange.autoborna.full_object_report_builder',
                    'autoborna.integrations.sync.data_exchange.autoborna.partial_object_report_builder',
                    'autoborna.integrations.sync.data_exchange.autoborna.order_executioner',
                ],
            ],
            'autoborna.integrations.sync.integration_process.object_change_generator' => [
                'class'     => \Autoborna\IntegrationsBundle\Sync\SyncProcess\Direction\Integration\ObjectChangeGenerator::class,
                'arguments' => [
                    'autoborna.integrations.sync.sync_process.value_helper',
                ],
            ],
            'autoborna.integrations.sync.integration_process' => [
                'class'     => \Autoborna\IntegrationsBundle\Sync\SyncProcess\Direction\Integration\IntegrationSyncProcess::class,
                'arguments' => [
                    'autoborna.integrations.helper.sync_date',
                    'autoborna.integrations.helper.sync_mapping',
                    'autoborna.integrations.sync.integration_process.object_change_generator',
                ],
            ],
            'autoborna.integrations.sync.internal_process.object_change_generator' => [
                'class'     => \Autoborna\IntegrationsBundle\Sync\SyncProcess\Direction\Internal\ObjectChangeGenerator::class,
                'arguments' => [
                    'autoborna.integrations.helper.sync_judge',
                    'autoborna.integrations.sync.sync_process.value_helper',
                    'autoborna.integrations.sync.data_exchange.autoborna.field_helper',
                ],
            ],
            'autoborna.integrations.sync.internal_process' => [
                'class'     => \Autoborna\IntegrationsBundle\Sync\SyncProcess\Direction\Internal\AutobornaSyncProcess::class,
                'arguments' => [
                    'autoborna.integrations.helper.sync_date',
                    'autoborna.integrations.sync.internal_process.object_change_generator',
                ],
            ],
            'autoborna.integrations.sync.service' => [
                'class'     => \Autoborna\IntegrationsBundle\Sync\SyncService\SyncService::class,
                'arguments' => [
                    'autoborna.integrations.sync.data_exchange.autoborna',
                    'autoborna.integrations.helper.sync_date',
                    'autoborna.integrations.helper.sync_mapping',
                    'autoborna.integrations.sync.helper.relations',
                    'autoborna.integrations.helper.sync_integrations',
                    'event_dispatcher',
                    'autoborna.integrations.sync.notifier',
                    'autoborna.integrations.sync.integration_process',
                    'autoborna.integrations.sync.internal_process',
                ],
                'methodCalls' => [
                    'initiateDebugLogger' => ['autoborna.sync.logger'],
                ],
            ],
            'autoborna.integrations.helper.sync_date' => [
                'class'     => \Autoborna\IntegrationsBundle\Sync\Helper\SyncDateHelper::class,
                'arguments' => [
                    'doctrine.dbal.default_connection',
                ],
            ],
            'autoborna.integrations.helper.sync_mapping' => [
                'class'     => \Autoborna\IntegrationsBundle\Sync\Helper\MappingHelper::class,
                'arguments' => [
                    'autoborna.lead.model.field',
                    'autoborna.integrations.repository.object_mapping',
                    'autoborna.integrations.internal.object_provider',
                    'event_dispatcher',
                ],
            ],
            'autoborna.integrations.sync.helper.relations' => [
                'class'     => \Autoborna\IntegrationsBundle\Sync\Helper\RelationsHelper::class,
                'arguments' => [
                    'autoborna.integrations.helper.sync_mapping',
                ],
            ],
            'autoborna.integrations.sync.notifier' => [
                'class'     => \Autoborna\IntegrationsBundle\Sync\Notification\Notifier::class,
                'arguments' => [
                    'autoborna.integrations.sync.notification.handler_container',
                    'autoborna.integrations.helper.sync_integrations',
                    'autoborna.integrations.helper.config_integrations',
                ],
            ],
            'autoborna.integrations.sync.notification.writer' => [
                'class'     => \Autoborna\IntegrationsBundle\Sync\Notification\Writer::class,
                'arguments' => [
                    'autoborna.core.model.notification',
                    'autoborna.core.model.auditlog',
                    'doctrine.orm.entity_manager',
                ],
            ],
            'autoborna.integrations.sync.notification.handler_container' => [
                'class' => \Autoborna\IntegrationsBundle\Sync\Notification\Handler\HandlerContainer::class,
            ],
            'autoborna.integrations.sync.notification.handler_company' => [
                'class'     => \Autoborna\IntegrationsBundle\Sync\Notification\Handler\CompanyNotificationHandler::class,
                'arguments' => [
                    'autoborna.integrations.sync.notification.writer',
                    'autoborna.integrations.sync.notification.helper_user_notification',
                    'autoborna.integrations.sync.notification.helper_company',
                ],
                'tag' => 'autoborna.sync.notification_handler',
            ],
            'autoborna.integrations.sync.notification.handler_contact' => [
                'class'     => \Autoborna\IntegrationsBundle\Sync\Notification\Handler\ContactNotificationHandler::class,
                'arguments' => [
                    'autoborna.integrations.sync.notification.writer',
                    'autoborna.lead.repository.lead_event_log',
                    'doctrine.orm.entity_manager',
                    'autoborna.integrations.sync.notification.helper_user_summary_notification',
                ],
                'tag' => 'autoborna.sync.notification_handler',
            ],
            'autoborna.integrations.sync.notification.helper_company' => [
                'class'     => \Autoborna\IntegrationsBundle\Sync\Notification\Helper\CompanyHelper::class,
                'arguments' => [
                    'doctrine.dbal.default_connection',
                ],
            ],
            'autoborna.integrations.sync.notification.helper_user' => [
                'class'     => \Autoborna\IntegrationsBundle\Sync\Notification\Helper\UserHelper::class,
                'arguments' => [
                    'doctrine.dbal.default_connection',
                ],
            ],
            'autoborna.integrations.sync.notification.helper_route' => [
                'class'     => \Autoborna\IntegrationsBundle\Sync\Notification\Helper\RouteHelper::class,
                'arguments' => [
                    'autoborna.integrations.internal.object_provider',
                    'event_dispatcher',
                ],
            ],
            'autoborna.integrations.sync.notification.helper_user_notification' => [
                'class'     => \Autoborna\IntegrationsBundle\Sync\Notification\Helper\UserNotificationHelper::class,
                'arguments' => [
                    'autoborna.integrations.sync.notification.writer',
                    'autoborna.integrations.sync.notification.helper_user',
                    'autoborna.integrations.sync.notification.helper.owner_provider',
                    'autoborna.integrations.sync.notification.helper_route',
                    'translator',
                ],
            ],
            'autoborna.integrations.sync.notification.helper_user_summary_notification' => [
                'class'     => \Autoborna\IntegrationsBundle\Sync\Notification\Helper\UserSummaryNotificationHelper::class,
                'arguments' => [
                    'autoborna.integrations.sync.notification.writer',
                    'autoborna.integrations.sync.notification.helper_user',
                    'autoborna.integrations.sync.notification.helper.owner_provider',
                    'autoborna.integrations.sync.notification.helper_route',
                    'translator',
                ],
            ],
        ],
    ],
];
