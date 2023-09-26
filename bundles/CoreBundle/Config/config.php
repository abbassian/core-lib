<?php

return [
    'routes' => [
        'main' => [
            'autoborna_core_ajax' => [
                'path'       => '/ajax',
                'controller' => 'AutobornaCoreBundle:Ajax:delegateAjax',
            ],
            'autoborna_core_update' => [
                'path'       => '/update',
                'controller' => 'AutobornaCoreBundle:Update:index',
            ],
            'autoborna_core_update_schema' => [
                'path'       => '/update/schema',
                'controller' => 'AutobornaCoreBundle:Update:schema',
            ],
            'autoborna_core_form_action' => [
                'path'       => '/action/{objectAction}/{objectModel}/{objectId}',
                'controller' => 'AutobornaCoreBundle:Form:execute',
                'defaults'   => [
                    'objectModel' => '',
                ],
            ],
            'autoborna_core_file_action' => [
                'path'       => '/file/{objectAction}/{objectId}',
                'controller' => 'AutobornaCoreBundle:File:execute',
            ],
            'autoborna_themes_index' => [
                'path'       => '/themes',
                'controller' => 'AutobornaCoreBundle:Theme:index',
            ],
            'autoborna_themes_action' => [
                'path'       => '/themes/{objectAction}/{objectId}',
                'controller' => 'AutobornaCoreBundle:Theme:execute',
            ],
        ],
        'public' => [
            'autoborna_js' => [
                'path'       => '/mtc.js',
                'controller' => 'AutobornaCoreBundle:Js:index',
            ],
            'autoborna_base_index' => [
                'path'       => '/',
                'controller' => 'AutobornaCoreBundle:Default:index',
            ],
            'autoborna_secure_root' => [
                'path'       => '/s',
                'controller' => 'AutobornaCoreBundle:Default:redirectSecureRoot',
            ],
            'autoborna_secure_root_slash' => [
                'path'       => '/s/',
                'controller' => 'AutobornaCoreBundle:Default:redirectSecureRoot',
            ],
            'autoborna_remove_trailing_slash' => [
                'path'         => '/{url}',
                'controller'   => 'AutobornaCoreBundle:Common:removeTrailingSlash',
                'method'       => 'GET',
                'requirements' => [
                    'url' => '.*/$',
                ],
            ],
        ],
        'api' => [
            'autoborna_core_api_file_list' => [
                'path'       => '/files/{dir}',
                'controller' => 'AutobornaCoreBundle:Api\FileApi:list',
            ],
            'autoborna_core_api_file_create' => [
                'path'       => '/files/{dir}/new',
                'controller' => 'AutobornaCoreBundle:Api\FileApi:create',
                'method'     => 'POST',
            ],
            'autoborna_core_api_file_delete' => [
                'path'       => '/files/{dir}/{file}/delete',
                'controller' => 'AutobornaCoreBundle:Api\FileApi:delete',
                'method'     => 'DELETE',
            ],
            'autoborna_core_api_theme_list' => [
                'path'       => '/themes',
                'controller' => 'AutobornaCoreBundle:Api\ThemeApi:list',
            ],
            'autoborna_core_api_theme_get' => [
                'path'       => '/themes/{theme}',
                'controller' => 'AutobornaCoreBundle:Api\ThemeApi:get',
            ],
            'autoborna_core_api_theme_create' => [
                'path'       => '/themes/new',
                'controller' => 'AutobornaCoreBundle:Api\ThemeApi:new',
                'method'     => 'POST',
            ],
            'autoborna_core_api_theme_delete' => [
                'path'       => '/themes/{theme}/delete',
                'controller' => 'AutobornaCoreBundle:Api\ThemeApi:delete',
                'method'     => 'DELETE',
            ],
            'autoborna_core_api_stats' => [
                'path'       => '/stats/{table}',
                'controller' => 'AutobornaCoreBundle:Api\StatsApi:list',
                'defaults'   => [
                    'table' => '',
                ],
            ],
        ],
    ],
    'menu' => [
        'main' => [
            'autoborna.core.components' => [
                'id'        => 'autoborna_components_root',
                'iconClass' => 'fa-puzzle-piece',
                'priority'  => 60,
            ],
            'autoborna.core.channels' => [
                'id'        => 'autoborna_channels_root',
                'iconClass' => 'fa-rss',
                'priority'  => 40,
            ],
        ],
        'admin' => [
            'autoborna.theme.menu.index' => [
                'route'     => 'autoborna_themes_index',
                'iconClass' => 'fa-newspaper-o',
                'id'        => 'autoborna_themes_index',
                'access'    => 'core:themes:view',
            ],
        ],
        'extra' => [
            'priority' => -1000,
            'items'    => [
                'name'     => 'extra',
                'children' => [],
            ],
        ],
        'profile' => [
            'priority' => -1000,
            'items'    => [
                'name'     => 'profile',
                'children' => [],
            ],
        ],
    ],
    'services' => [
        'main' => [
            'autoborna.core.service.flashbag' => [
                'class'     => \Autoborna\CoreBundle\Service\FlashBag::class,
                'arguments' => [
                    '@session',
                    'translator',
                    'request_stack',
                    'autoborna.core.model.notification',
                ],
            ],
            'autoborna.core.service.local_file_adapter' => [
                'class'     => \Autoborna\CoreBundle\Service\LocalFileAdapterService::class,
                'arguments' => [
                    '%env(resolve:MAUTIC_EL_FINDER_PATH)%',
                ],
            ],
            'autoborna.core.service.log_processor' => [
                'class'     => \Autoborna\CoreBundle\Monolog\LogProcessor::class,
                'tags'      => ['monolog.processor'],
            ],
        ],
        'events' => [
            'autoborna.core.subscriber' => [
                'class'     => Autoborna\CoreBundle\EventListener\CoreSubscriber::class,
                'arguments' => [
                    'autoborna.helper.bundle',
                    'autoborna.helper.menu',
                    'autoborna.helper.user',
                    'templating.helper.assets',
                    'autoborna.helper.core_parameters',
                    'security.authorization_checker',
                    'autoborna.user.model.user',
                    'event_dispatcher',
                    'translator',
                    'request_stack',
                    'autoborna.form.repository.form',
                    'autoborna.factory',
                    'autoborna.core.service.flashbag',
                ],
            ],
            'autoborna.core.environment.subscriber' => [
                'class'     => \Autoborna\CoreBundle\EventListener\EnvironmentSubscriber::class,
                'arguments' => [
                    'autoborna.helper.core_parameters',
                ],
            ],
            'autoborna.core.migration.command.subscriber' => [
                'class'     => \Autoborna\CoreBundle\EventListener\MigrationCommandSubscriber::class,
                'arguments' => [
                    'autoborna.database.version.provider',
                    'autoborna.generated.columns.provider',
                    'database_connection',
                ],
            ],
            'autoborna.core.configbundle.subscriber' => [
                'class'     => \Autoborna\CoreBundle\EventListener\ConfigSubscriber::class,
                'arguments' => [
                    'autoborna.helper.language',
                ],
            ],
            'autoborna.core.configbundle.subscriber.theme' => [
                'class'     => \Autoborna\CoreBundle\EventListener\ConfigThemeSubscriber::class,
            ],
            'autoborna.webpush.js.subscriber' => [
                'class' => \Autoborna\CoreBundle\EventListener\BuildJsSubscriber::class,
            ],
            'autoborna.core.dashboard.subscriber' => [
                'class'     => \Autoborna\CoreBundle\EventListener\DashboardSubscriber::class,
                'arguments' => [
                    'autoborna.core.model.auditlog',
                    'translator',
                    'router',
                    'autoborna.security',
                    'event_dispatcher',
                    'autoborna.model.factory',
                ],
            ],

            'autoborna.core.maintenance.subscriber' => [
                'class'     => Autoborna\CoreBundle\EventListener\MaintenanceSubscriber::class,
                'arguments' => [
                    'doctrine.dbal.default_connection',
                    'autoborna.user.token.repository',
                    'translator',
                ],
            ],
            'autoborna.core.request.subscriber' => [
                'class'     => \Autoborna\CoreBundle\EventListener\RequestSubscriber::class,
                'arguments' => [
                    'security.csrf.token_manager',
                    'translator',
                    'autoborna.helper.templating',
                ],
            ],
            'autoborna.core.stats.subscriber' => [
                'class'     => \Autoborna\CoreBundle\EventListener\StatsSubscriber::class,
                'arguments' => [
                    'autoborna.security',
                    'doctrine.orm.entity_manager',
                ],
            ],
            'autoborna.core.assets.subscriber' => [
                'class'     => \Autoborna\CoreBundle\EventListener\AssetsSubscriber::class,
                'arguments' => [
                    'templating.helper.assets',
                    'event_dispatcher',
                ],
            ],
            'autoborna.core.subscriber.router' => [
                'class'     => \Autoborna\CoreBundle\EventListener\RouterSubscriber::class,
                'arguments' => [
                    'router',
                    '%router.request_context.scheme%',
                    '%router.request_context.host%',
                    '%request_listener.https_port%',
                    '%request_listener.http_port%',
                    '%router.request_context.base_url%',
                ],
            ],
            'autoborna.core.subscriber.editor_assets' => [
                'class'       => \Autoborna\CoreBundle\EventListener\EditorFontsSubscriber::class,
                'arguments'   => [
                    'autoborna.helper.core_parameters',
                ],
            ],
        ],
        'forms' => [
            'autoborna.form.type.button_group' => [
                'class' => 'Autoborna\CoreBundle\Form\Type\ButtonGroupType',
            ],
            'autoborna.form.type.standalone_button' => [
                'class' => 'Autoborna\CoreBundle\Form\Type\StandAloneButtonType',
            ],
            'autoborna.form.type.form_buttons' => [
                'class' => 'Autoborna\CoreBundle\Form\Type\FormButtonsType',
            ],
            'autoborna.form.type.sortablelist' => [
                'class' => 'Autoborna\CoreBundle\Form\Type\SortableListType',
            ],
            'autoborna.form.type.coreconfig' => [
                'class'     => \Autoborna\CoreBundle\Form\Type\ConfigType::class,
                'arguments' => [
                    'translator',
                    'autoborna.helper.language',
                    'autoborna.ip_lookup.factory',
                    '%autoborna.ip_lookup_services%',
                    'autoborna.ip_lookup',
                ],
            ],
            'autoborna.form.type.coreconfig.iplookup_download_data_store_button' => [
                'class'     => \Autoborna\CoreBundle\Form\Type\IpLookupDownloadDataStoreButtonType::class,
                'arguments' => [
                    'autoborna.helper.template.date',
                    'translator',
                ],
            ],
            'autoborna.form.type.theme_list' => [
                'class'     => \Autoborna\CoreBundle\Form\Type\ThemeListType::class,
                'arguments' => ['autoborna.helper.theme'],
            ],
            'autoborna.form.type.daterange' => [
                'class'     => \Autoborna\CoreBundle\Form\Type\DateRangeType::class,
                'arguments' => [
                    'session',
                    'autoborna.helper.core_parameters',
                ],
            ],
            'autoborna.form.type.timeformat' => [
                'class'     => \Autoborna\CoreBundle\Form\Type\TimeFormatType::class,
                'arguments' => ['translator'],
            ],
            'autoborna.form.type.slot.saveprefsbutton' => [
                'class'     => 'Autoborna\CoreBundle\Form\Type\SlotSavePrefsButtonType',
                'arguments' => [
                    'translator',
                ],
            ],
            'autoborna.form.type.slot.successmessage' => [
                'class'     => Autoborna\CoreBundle\Form\Type\SlotSuccessMessageType::class,
                'arguments' => [
                    'translator',
                ],
            ],
            'autoborna.form.type.slot.gatedvideo' => [
                'class'     => Autoborna\CoreBundle\Form\Type\GatedVideoType::class,
                'arguments' => [
                    'autoborna.form.repository.form',
                ],
            ],
            'autoborna.form.type.slot.segmentlist' => [
                'class'     => 'Autoborna\CoreBundle\Form\Type\SlotSegmentListType',
                'arguments' => [
                    'translator',
                ],
            ],
            'autoborna.form.type.slot.categorylist' => [
                'class'     => \Autoborna\CoreBundle\Form\Type\SlotCategoryListType::class,
                'arguments' => [
                    'translator',
                ],
            ],
            'autoborna.form.type.slot.preferredchannel' => [
                'class'     => \Autoborna\CoreBundle\Form\Type\SlotPreferredChannelType::class,
                'arguments' => [
                    'translator',
                ],
            ],
            'autoborna.form.type.slot.channelfrequency' => [
                'class'     => \Autoborna\CoreBundle\Form\Type\SlotChannelFrequencyType::class,
                'arguments' => [
                    'translator',
                ],
            ],
            'autoborna.form.type.dynamic_content_filter_entry' => [
                'class'     => \Autoborna\CoreBundle\Form\Type\DynamicContentFilterEntryType::class,
                'arguments' => [
                    'autoborna.lead.model.list',
                    'autoborna.stage.model.stage',
                    'autoborna.integrations.helper.builder_integrations',
                ],
            ],
            'autoborna.form.type.dynamic_content_filter_entry_filters' => [
                'class'     => \Autoborna\CoreBundle\Form\Type\DynamicContentFilterEntryFiltersType::class,
                'arguments' => [
                    'translator',
                ],
                'methodCalls' => [
                    'setConnection' => [
                        'database_connection',
                    ],
                ],
            ],
            'autoborna.form.type.entity_lookup' => [
                'class'     => \Autoborna\CoreBundle\Form\Type\EntityLookupType::class,
                'arguments' => [
                    'autoborna.model.factory',
                    'translator',
                    'database_connection',
                    'router',
                ],
            ],
            'autoborna.form.type.dynamic_content_filter' => [
                'class'     => \Autoborna\CoreBundle\Form\Type\DynamicContentFilterType::class,
                'arguments' => [
                    'autoborna.integrations.helper.builder_integrations',
                ],
            ],
        ],
        'helpers' => [
            'autoborna.helper.app_version' => [
                'class' => \Autoborna\CoreBundle\Helper\AppVersion::class,
            ],
            'autoborna.helper.template.menu' => [
                'class'     => \Autoborna\CoreBundle\Templating\Helper\MenuHelper::class,
                'arguments' => ['knp_menu.helper'],
                'alias'     => 'menu',
            ],
            'autoborna.helper.template.date' => [
                'class'     => \Autoborna\CoreBundle\Templating\Helper\DateHelper::class,
                'arguments' => [
                    '%autoborna.date_format_full%',
                    '%autoborna.date_format_short%',
                    '%autoborna.date_format_dateonly%',
                    '%autoborna.date_format_timeonly%',
                    'translator',
                    'autoborna.helper.core_parameters',
                ],
                'alias' => 'date',
            ],
            'autoborna.helper.template.exception' => [
                'class'     => 'Autoborna\CoreBundle\Templating\Helper\ExceptionHelper',
                'arguments' => '%kernel.root_dir%',
                'alias'     => 'exception',
            ],
            'autoborna.helper.template.gravatar' => [
                'class'     => \Autoborna\CoreBundle\Templating\Helper\GravatarHelper::class,
                'arguments' => [
                    'autoborna.helper.template.default_avatar',
                    'autoborna.helper.core_parameters',
                    'request_stack',
                ],
                'alias'     => 'gravatar',
            ],
            'autoborna.helper.template.analytics' => [
                'class'     => \Autoborna\CoreBundle\Templating\Helper\AnalyticsHelper::class,
                'alias'     => 'analytics',
                'arguments' => [
                    'autoborna.helper.core_parameters',
                ],
            ],
            'autoborna.helper.template.config' => [
                'class'     => \Autoborna\CoreBundle\Templating\Helper\ConfigHelper::class,
                'alias'     => 'config',
                'arguments' => [
                    'autoborna.helper.core_parameters',
                ],
            ],
            'autoborna.helper.template.mautibot' => [
                'class' => 'Autoborna\CoreBundle\Templating\Helper\MautibotHelper',
                'alias' => 'mautibot',
            ],
            'autoborna.helper.template.canvas' => [
                'class'     => 'Autoborna\CoreBundle\Templating\Helper\SidebarCanvasHelper',
                'arguments' => [
                    'event_dispatcher',
                ],
                'alias' => 'canvas',
            ],
            'autoborna.helper.template.button' => [
                'class'     => 'Autoborna\CoreBundle\Templating\Helper\ButtonHelper',
                'arguments' => [
                    'templating',
                    'translator',
                    'event_dispatcher',
                ],
                'alias' => 'buttons',
            ],
            'autoborna.helper.template.content' => [
                'class'     => 'Autoborna\CoreBundle\Templating\Helper\ContentHelper',
                'arguments' => [
                    'templating',
                    'event_dispatcher',
                ],
                'alias' => 'content',
            ],
            'autoborna.helper.template.formatter' => [
                'class'     => \Autoborna\CoreBundle\Templating\Helper\FormatterHelper::class,
                'arguments' => [
                    'autoborna.helper.template.date',
                    'translator',
                ],
                'alias' => 'formatter',
            ],
            'autoborna.helper.template.version' => [
                'class'     => \Autoborna\CoreBundle\Templating\Helper\VersionHelper::class,
                'arguments' => [
                    'autoborna.helper.app_version',
                ],
                'alias' => 'version',
            ],
            'autoborna.helper.template.security' => [
                'class'     => \Autoborna\CoreBundle\Templating\Helper\SecurityHelper::class,
                'arguments' => [
                    'autoborna.security',
                    'request_stack',
                    'event_dispatcher',
                    'security.csrf.token_manager',
                ],
                'alias' => 'security',
            ],
            'autoborna.helper.template.translator' => [
                'class'     => \Autoborna\CoreBundle\Templating\Helper\TranslatorHelper::class,
                'arguments' => [
                    'translator',
                ],
                'alias' => 'translator',
            ],
            'autoborna.helper.paths' => [
                'class'     => 'Autoborna\CoreBundle\Helper\PathsHelper',
                'arguments' => [
                    'autoborna.helper.user',
                    'autoborna.helper.core_parameters',
                    '%kernel.cache_dir%',
                    '%kernel.logs_dir%',
                    '%kernel.root_dir%',
                ],
            ],
            'autoborna.helper.ip_lookup' => [
                'class'     => 'Autoborna\CoreBundle\Helper\IpLookupHelper',
                'arguments' => [
                    'request_stack',
                    'doctrine.orm.entity_manager',
                    'autoborna.helper.core_parameters',
                    'autoborna.ip_lookup',
                ],
            ],
            'autoborna.helper.user' => [
                'class'     => 'Autoborna\CoreBundle\Helper\UserHelper',
                'arguments' => [
                    'security.token_storage',
                ],
            ],
            'autoborna.helper.core_parameters' => [
                'class'     => \Autoborna\CoreBundle\Helper\CoreParametersHelper::class,
                'arguments' => [
                    'service_container',
                ],
                'serviceAlias' => 'autoborna.config',
            ],
            'autoborna.helper.bundle' => [
                'class'     => 'Autoborna\CoreBundle\Helper\BundleHelper',
                'arguments' => [
                    '%autoborna.bundles%',
                    '%autoborna.plugin.bundles%',
                ],
            ],
            'autoborna.helper.phone_number' => [
                'class' => 'Autoborna\CoreBundle\Helper\PhoneNumberHelper',
            ],
            'autoborna.helper.input_helper' => [
                'class' => \Autoborna\CoreBundle\Helper\InputHelper::class,
            ],
            'autoborna.helper.file_uploader' => [
                'class'     => \Autoborna\CoreBundle\Helper\FileUploader::class,
                'arguments' => [
                    'autoborna.helper.file_path_resolver',
                ],
            ],
            'autoborna.helper.file_path_resolver' => [
                'class'     => \Autoborna\CoreBundle\Helper\FilePathResolver::class,
                'arguments' => [
                    'symfony.filesystem',
                    'autoborna.helper.input_helper',
                ],
            ],
            'autoborna.helper.file_properties' => [
                'class' => \Autoborna\CoreBundle\Helper\FileProperties::class,
            ],
            'autoborna.helper.trailing_slash' => [
                'class'     => \Autoborna\CoreBundle\Helper\TrailingSlashHelper::class,
                'arguments' => [
                    'autoborna.helper.core_parameters',
                ],
            ],
            'autoborna.helper.token_builder' => [
                'class'     => \Autoborna\CoreBundle\Helper\BuilderTokenHelper::class,
                'arguments' => [
                    'autoborna.security',
                    'autoborna.model.factory',
                    'database_connection',
                    'autoborna.helper.user',
                ],
            ],
            'autoborna.helper.token_builder.factory' => [
                'class'     => \Autoborna\CoreBundle\Helper\BuilderTokenHelperFactory::class,
                'arguments' => [
                    'autoborna.security',
                    'autoborna.model.factory',
                    'database_connection',
                    'autoborna.helper.user',
                ],
            ],
            'autoborna.helper.maxmind_do_not_sell_download' => [
                'class'     => \Autoborna\CoreBundle\Helper\MaxMindDoNotSellDownloadHelper::class,
                'arguments' => [
                    '%autoborna.ip_lookup_auth%',
                    'monolog.logger.autoborna',
                    'autoborna.native.connector',
                    'autoborna.helper.core_parameters',
                ],
            ],
            'autoborna.helper.update_checks' => [
                'class' => \Autoborna\CoreBundle\Helper\PreUpdateCheckHelper::class,
            ],
        ],
        'menus' => [
            'autoborna.menu.main' => [
                'alias' => 'main',
            ],
            'autoborna.menu.admin' => [
                'alias'   => 'admin',
                'options' => [
                    'template' => 'AutobornaCoreBundle:Menu:admin.html.php',
                ],
            ],
            'autoborna.menu.extra' => [
                'alias'   => 'extra',
                'options' => [
                    'template' => 'AutobornaCoreBundle:Menu:extra.html.php',
                ],
            ],
            'autoborna.menu.profile' => [
                'alias'   => 'profile',
                'options' => [
                    'template' => 'AutobornaCoreBundle:Menu:profile_inline.html.php',
                ],
            ],
        ],
        'commands' => [
            'autoborna.core.command.transifex_pull' => [
                'tag'       => 'console.command',
                'class'     => \Autoborna\CoreBundle\Command\PullTransifexCommand::class,
                'arguments' => [
                    'transifex.factory',
                    'translator',
                    'autoborna.helper.core_parameters',
                ],
            ],
            'autoborna.core.command.transifex_push' => [
                'tag'       => 'console.command',
                'class'     => \Autoborna\CoreBundle\Command\PushTransifexCommand::class,
                'arguments' => [
                    'transifex.factory',
                    'translator',
                ],
            ],
            'autoborna.core.command.do_not_sell' => [
                'class'     => \Autoborna\CoreBundle\Command\UpdateDoNotSellListCommand::class,
                'arguments' => [
                    'autoborna.helper.maxmind_do_not_sell_download',
                    'translator',
                ],
                'tag' => 'console.command',
            ],
            'autoborna.core.command.apply_update' => [
                'tag'       => 'console.command',
                'class'     => \Autoborna\CoreBundle\Command\ApplyUpdatesCommand::class,
                'arguments' => [
                    'translator',
                    'autoborna.helper.core_parameters',
                    'autoborna.update.step_provider',
                ],
            ],
            'autoborna.core.command.maxmind.purge' => [
                'tag'       => 'console.command',
                'class'     => \Autoborna\CoreBundle\Command\MaxMindDoNotSellPurgeCommand::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                    'autoborna.maxmind.doNotSellList',
                ],
            ],
        ],
        'other' => [
            'autoborna.cache.warmer.middleware' => [
                'class'     => \Autoborna\CoreBundle\Cache\MiddlewareCacheWarmer::class,
                'tag'       => 'kernel.cache_warmer',
                'arguments' => [
                    '%kernel.environment%',
                ],
            ],
            'autoborna.http.client' => [
                'class' => GuzzleHttp\Client::class,
            ],
            /* @deprecated to be removed in Autoborna 4. Use 'autoborna.filesystem' instead. */
            'symfony.filesystem' => [
                'class' => \Symfony\Component\Filesystem\Filesystem::class,
            ],
            'autoborna.filesystem' => [
                'class' => \Autoborna\CoreBundle\Helper\Filesystem::class,
            ],
            'symfony.finder' => [
                'class' => \Symfony\Component\Finder\Finder::class,
            ],
            // Error handler
            'autoborna.core.errorhandler.subscriber' => [
                'class'     => 'Autoborna\CoreBundle\EventListener\ErrorHandlingListener',
                'arguments' => [
                    'monolog.logger.autoborna',
                    'monolog.logger',
                    "@=container.has('monolog.logger.chrome') ? container.get('monolog.logger.chrome') : null",
                ],
                'tag' => 'kernel.event_subscriber',
            ],

            // Configurator (used in installer and managing global config]
            'autoborna.configurator' => [
                'class'     => 'Autoborna\CoreBundle\Configurator\Configurator',
                'arguments' => [
                    'autoborna.helper.paths',
                ],
            ],

            // System uses
            'autoborna.di.env_processor.nullable' => [
                'class' => \Autoborna\CoreBundle\DependencyInjection\EnvProcessor\NullableProcessor::class,
                'tag'   => 'container.env_var_processor',
            ],
            'autoborna.di.env_processor.int_nullable' => [
                'class' => \Autoborna\CoreBundle\DependencyInjection\EnvProcessor\IntNullableProcessor::class,
                'tag'   => 'container.env_var_processor',
            ],
            'autoborna.di.env_processor.autobornaconst' => [
                'class' => \Autoborna\CoreBundle\DependencyInjection\EnvProcessor\AutobornaConstProcessor::class,
                'tag'   => 'container.env_var_processor',
            ],
            'autoborna.cipher.openssl' => [
                'class'     => \Autoborna\CoreBundle\Security\Cryptography\Cipher\Symmetric\OpenSSLCipher::class,
                'arguments' => ['%kernel.environment%'],
            ],
            'autoborna.factory' => [
                'class'     => 'Autoborna\CoreBundle\Factory\AutobornaFactory',
                'arguments' => 'service_container',
            ],
            'autoborna.model.factory' => [
                'class'     => 'Autoborna\CoreBundle\Factory\ModelFactory',
                'arguments' => 'service_container',
            ],
            'autoborna.templating.name_parser' => [
                'class'     => 'Autoborna\CoreBundle\Templating\TemplateNameParser',
                'arguments' => 'kernel',
            ],
            'autoborna.route_loader' => [
                'class'     => 'Autoborna\CoreBundle\Loader\RouteLoader',
                'arguments' => [
                    'event_dispatcher',
                    'autoborna.helper.core_parameters',
                ],
                'tag' => 'routing.loader',
            ],
            'autoborna.security' => [
                'class'     => 'Autoborna\CoreBundle\Security\Permissions\CorePermissions',
                'arguments' => [
                    'autoborna.helper.user',
                    'translator',
                    'autoborna.helper.core_parameters',
                    '%autoborna.bundles%',
                    '%autoborna.plugin.bundles%',
                ],
            ],
            'autoborna.page.helper.factory' => [
                'class'     => \Autoborna\CoreBundle\Factory\PageHelperFactory::class,
                'arguments' => [
                    'session',
                    'autoborna.helper.core_parameters',
                ],
            ],
            'autoborna.translation.loader' => [
                'class'     => \Autoborna\CoreBundle\Loader\TranslationLoader::class,
                'arguments' => [
                    'autoborna.helper.bundle',
                    'autoborna.helper.paths',
                ],
                'tag'       => 'translation.loader',
                'alias'     => 'autoborna',
            ],
            'autoborna.tblprefix_subscriber' => [
                'class'     => 'Autoborna\CoreBundle\EventListener\DoctrineEventsSubscriber',
                'tag'       => 'doctrine.event_subscriber',
                'arguments' => '%autoborna.db_table_prefix%',
            ],
            'autoborna.database.version.provider' => [
                'class'     => \Autoborna\CoreBundle\Doctrine\Provider\VersionProvider::class,
                'arguments' => ['database_connection', 'autoborna.helper.core_parameters'],
            ],
            'autoborna.generated.columns.provider' => [
                'class'     => \Autoborna\CoreBundle\Doctrine\Provider\GeneratedColumnsProvider::class,
                'arguments' => ['autoborna.database.version.provider', 'event_dispatcher'],
            ],
            'autoborna.generated.columns.doctrine.listener' => [
                'class'        => \Autoborna\CoreBundle\EventListener\DoctrineGeneratedColumnsListener::class,
                'tag'          => 'doctrine.event_listener',
                'tagArguments' => [
                    'event' => 'postGenerateSchema',
                    'lazy'  => true,
                ],
                'arguments' => [
                    'autoborna.generated.columns.provider',
                    'monolog.logger.autoborna',
                ],
            ],
            'autoborna.exception.listener' => [
                'class'     => 'Autoborna\CoreBundle\EventListener\ExceptionListener',
                'arguments' => [
                    'router',
                    '"AutobornaCoreBundle:Exception:show"',
                    'monolog.logger.autoborna',
                ],
                'tag'          => 'kernel.event_listener',
                'tagArguments' => [
                    'event'    => 'kernel.exception',
                    'method'   => 'onKernelException',
                    'priority' => 255,
                ],
            ],
            'transifex.factory' => [
                'class'     => \Autoborna\CoreBundle\Factory\TransifexFactory::class,
                'arguments' => [
                    'autoborna.http.client',
                    'autoborna.helper.core_parameters',
                ],
            ],
            // Helpers
            'autoborna.helper.assetgeneration' => [
                'class'     => \Autoborna\CoreBundle\Helper\AssetGenerationHelper::class,
                'arguments' => [
                    'autoborna.helper.core_parameters',
                    'autoborna.helper.bundle',
                    'autoborna.helper.paths',
                    'autoborna.helper.app_version',
                ],
            ],
            'autoborna.helper.cookie' => [
                'class'     => 'Autoborna\CoreBundle\Helper\CookieHelper',
                'arguments' => [
                    '%autoborna.cookie_path%',
                    '%autoborna.cookie_domain%',
                    '%autoborna.cookie_secure%',
                    '%autoborna.cookie_httponly%',
                    'request_stack',
                ],
            ],
            'autoborna.helper.cache_storage' => [
                'class'     => Autoborna\CoreBundle\Helper\CacheStorageHelper::class,
                'arguments' => [
                    '"db"',
                    '%autoborna.db_table_prefix%',
                    'doctrine.dbal.default_connection',
                    '%kernel.cache_dir%',
                ],
            ],
            'autoborna.helper.update' => [
                'class'     => \Autoborna\CoreBundle\Helper\UpdateHelper::class,
                'arguments' => [
                    'autoborna.helper.paths',
                    'monolog.logger.autoborna',
                    'autoborna.helper.core_parameters',
                    'autoborna.http.client',
                    'autoborna.helper.update.release_parser',
                    'autoborna.helper.update_checks',
                ],
            ],
            'autoborna.helper.update.release_parser' => [
                'class'     => \Autoborna\CoreBundle\Helper\Update\Github\ReleaseParser::class,
                'arguments' => [
                    'autoborna.http.client',
                ],
            ],
            'autoborna.helper.cache' => [
                'class'     => \Autoborna\CoreBundle\Helper\CacheHelper::class,
                'arguments' => [
                    '%kernel.cache_dir%',
                    'session',
                    'autoborna.helper.paths',
                    'kernel',
                ],
            ],
            'autoborna.helper.templating' => [
                'class'     => 'Autoborna\CoreBundle\Helper\TemplatingHelper',
                'arguments' => [
                    'kernel',
                ],
            ],
            'autoborna.helper.theme' => [
                'class'     => \Autoborna\CoreBundle\Helper\ThemeHelper::class,
                'arguments' => [
                    'autoborna.helper.paths',
                    'autoborna.helper.templating',
                    'translator',
                    'autoborna.helper.core_parameters',
                    'autoborna.filesystem',
                    'symfony.finder',
                    'autoborna.integrations.helper.builder_integrations',
                ],
                'methodCalls' => [
                    'setDefaultTheme' => [
                        '%autoborna.theme%',
                    ],
                ],
            ],
            'autoborna.helper.encryption' => [
                'class'     => \Autoborna\CoreBundle\Helper\EncryptionHelper::class,
                'arguments' => [
                    'autoborna.helper.core_parameters',
                    'autoborna.cipher.openssl',
                ],
            ],
            'autoborna.helper.language' => [
                'class'     => \Autoborna\CoreBundle\Helper\LanguageHelper::class,
                'arguments' => [
                    'autoborna.helper.paths',
                    'monolog.logger.autoborna',
                    'autoborna.helper.core_parameters',
                    'autoborna.http.client',
                ],
            ],
            'autoborna.helper.url' => [
                'class'     => \Autoborna\CoreBundle\Helper\UrlHelper::class,
                'arguments' => [
                    'autoborna.http.client',
                    '%autoborna.link_shortener_url%',
                    'monolog.logger.autoborna',
                ],
            ],
            'autoborna.helper.export' => [
                'class'     => \Autoborna\CoreBundle\Helper\ExportHelper::class,
                'arguments' => [
                    'translator',
                ],
            ],
            'autoborna.helper.composer' => [
                'class'     => \Autoborna\CoreBundle\Helper\ComposerHelper::class,
                'arguments' => [
                    'kernel',
                    'monolog.logger.autoborna',
                ],
            ],
            // Menu
            'autoborna.helper.menu' => [
                'class'     => 'Autoborna\CoreBundle\Menu\MenuHelper',
                'arguments' => [
                    'autoborna.security',
                    'request_stack',
                    'autoborna.helper.core_parameters',
                    'autoborna.helper.integration',
                ],
            ],
            'autoborna.helper.hash' => [
                'class' => \Autoborna\CoreBundle\Helper\HashHelper\HashHelper::class,
            ],
            'autoborna.helper.random' => [
                'class' => \Autoborna\CoreBundle\Helper\RandomHelper\RandomHelper::class,
            ],
            'autoborna.helper.command' => [
                'class'     => \Autoborna\CoreBundle\Helper\CommandHelper::class,
                'arguments' => 'kernel',
            ],
            'autoborna.menu_renderer' => [
                'class'     => \Autoborna\CoreBundle\Menu\MenuRenderer::class,
                'arguments' => [
                    'knp_menu.matcher',
                    'autoborna.helper.templating',
                ],
                'tag'   => 'knp_menu.renderer',
                'alias' => 'autoborna',
            ],
            'autoborna.menu.builder' => [
                'class'     => \Autoborna\CoreBundle\Menu\MenuBuilder::class,
                'arguments' => [
                    'knp_menu.factory',
                    'knp_menu.matcher',
                    'event_dispatcher',
                    'autoborna.helper.menu',
                ],
            ],
            // IP Lookup
            'autoborna.ip_lookup.factory' => [
                'class'     => \Autoborna\CoreBundle\Factory\IpLookupFactory::class,
                'arguments' => [
                    '%autoborna.ip_lookup_services%',
                    'monolog.logger.autoborna',
                    'autoborna.http.client',
                    '%kernel.cache_dir%',
                ],
            ],
            'autoborna.ip_lookup' => [
                'class'     => \Autoborna\CoreBundle\IpLookup\AbstractLookup::class, // bogus just to make cache compilation happy
                'factory'   => ['@autoborna.ip_lookup.factory', 'getService'],
                'arguments' => [
                    '%autoborna.ip_lookup_service%',
                    '%autoborna.ip_lookup_auth%',
                    '%autoborna.ip_lookup_config%',
                    'autoborna.http.client',
                ],
            ],
            'autoborna.native.connector' => [
                'class'     => \Symfony\Contracts\HttpClient\HttpClientInterface::class,
                'factory'   => [Symfony\Component\HttpClient\HttpClient::class, 'create'],
            ],

            'twig.controller.exception.class' => 'Autoborna\CoreBundle\Controller\ExceptionController',

            // Form extensions
            'autoborna.form.extension.custom' => [
                'class'        => \Autoborna\CoreBundle\Form\Extension\CustomFormExtension::class,
                'arguments'    => [
                    'event_dispatcher',
                ],
                'tag'          => 'form.type_extension',
                'tagArguments' => [
                    'extended_type' => Symfony\Component\Form\Extension\Core\Type\FormType::class,
                ],
            ],

            // Twig
            'templating.twig.extension.slot' => [
                'class'     => \Autoborna\CoreBundle\Templating\Twig\Extension\SlotExtension::class,
                'arguments' => [
                    'templating.helper.slots',
                ],
                'tag' => 'twig.extension',
            ],
            'templating.twig.extension.asset' => [
                'class'     => 'Autoborna\CoreBundle\Templating\Twig\Extension\AssetExtension',
                'arguments' => [
                    'templating.helper.assets',
                ],
                'tag' => 'twig.extension',
            ],
            'templating.twig.extension.menu' => [
                'class'     => \Autoborna\CoreBundle\Templating\Twig\Extension\MenuExtension::class,
                'arguments' => [
                    'autoborna.helper.template.menu',
                ],
                'tag' => 'twig.extension',
            ],
            'templating.twig.extension.gravatar' => [
                'class'     => \Autoborna\CoreBundle\Templating\Twig\Extension\GravatarExtension::class,
                'arguments' => [
                    'autoborna.helper.template.gravatar',
                ],
                'tag' => 'twig.extension',
            ],
            'templating.twig.extension.version' => [
                'class'     => \Autoborna\CoreBundle\Templating\Twig\Extension\VersionExtension::class,
                'arguments' => [
                    'autoborna.helper.app_version',
                ],
                'tag' => 'twig.extension',
            ],
            'templating.twig.extension.mautibot' => [
                'class'     => \Autoborna\CoreBundle\Templating\Twig\Extension\MautibotExtension::class,
                'arguments' => [
                    'autoborna.helper.template.mautibot',
                ],
                'tag' => 'twig.extension',
            ],
            'templating.twig.extension.formatter' => [
                'class'     => \Autoborna\CoreBundle\Templating\Twig\Extension\FormatterExtension::class,
                'arguments' => [
                    'autoborna.helper.template.formatter',
                ],
                'tag' => 'twig.extension',
            ],
            'templating.twig.extension.date' => [
                'class'     => \Autoborna\CoreBundle\Templating\Twig\Extension\DateExtension::class,
                'arguments' => [
                    'autoborna.helper.template.date',
                ],
                'tag' => 'twig.extension',
            ],
            'templating.twig.extension.button' => [
                'class'     => \Autoborna\CoreBundle\Templating\Twig\Extension\ButtonExtension::class,
                'arguments' => [
                    'autoborna.helper.template.button',
                    'request_stack',
                    'router',
                    'translator',
                ],
                'tag' => 'twig.extension',
            ],
            'templating.twig.extension.content' => [
                'class'     => \Autoborna\CoreBundle\Templating\Twig\Extension\ContentExtension::class,
                'arguments' => [
                    'autoborna.helper.template.content',
                ],
                'tag' => 'twig.extension',
            ],
            'templating.twig.extension.numeric' => [
                'class'     => \Autoborna\CoreBundle\Templating\Twig\Extension\NumericExtension::class,
                'tag'       => 'twig.extension',
            ],
            'templating.twig.extension.form' => [
                'class'     => \Autoborna\CoreBundle\Templating\Twig\Extension\FormExtension::class,
                'tag'       => 'twig.extension',
            ],
            'templating.twig.extension.class' => [
                'class'     => \Autoborna\CoreBundle\Templating\Twig\Extension\ClassExtension::class,
                'tag'       => 'twig.extension',
            ],
            'templating.twig.extension.security' => [
                'class'     => \Autoborna\CoreBundle\Templating\Twig\Extension\SecurityExtension::class,
                'arguments' => [
                    'autoborna.helper.template.security',
                ],
                'tag'       => 'twig.extension',
            ],
            'templating.twig.extension.translator' => [
                'class'     => \Autoborna\CoreBundle\Templating\Twig\Extension\TranslatorExtension::class,
                'arguments' => [
                    'autoborna.helper.template.translator',
                ],
                'tag'       => 'twig.extension',
            ],
            'templating.twig.extension.config' => [
                'class'     => \Autoborna\CoreBundle\Templating\Twig\Extension\ConfigExtension::class,
                'arguments' => [
                    'autoborna.helper.template.config',
                ],
                'tag'       => 'twig.extension',
            ],
            'templating.twig.extension.storage' => [
                'class'     => \Autoborna\CoreBundle\Templating\Twig\Extension\StorageExtension::class,
                'tag'       => 'twig.extension',
            ],
            'templating.twig.extension.publish_status' => [
                'class'     => \Autoborna\CoreBundle\Templating\Twig\Extension\CoreHelpersExtension::class,
                'arguments' => [
                    'translator',
                    'autoborna.helper.template.date',
                ],
                'tag'       => 'twig.extension',
            ],
            // Schema
            'autoborna.schema.helper.column' => [
                'class'     => 'Autoborna\CoreBundle\Doctrine\Helper\ColumnSchemaHelper',
                'arguments' => [
                    'database_connection',
                    '%autoborna.db_table_prefix%',
                ],
            ],
            'autoborna.schema.helper.index' => [
                'class'     => 'Autoborna\CoreBundle\Doctrine\Helper\IndexSchemaHelper',
                'arguments' => [
                    'database_connection',
                    '%autoborna.db_table_prefix%',
                ],
            ],
            'autoborna.schema.helper.table' => [
                'class'     => 'Autoborna\CoreBundle\Doctrine\Helper\TableSchemaHelper',
                'arguments' => [
                    'database_connection',
                    '%autoborna.db_table_prefix%',
                    'autoborna.schema.helper.column',
                ],
            ],
            'autoborna.form.list.validator.circular' => [
                'class'     => Autoborna\CoreBundle\Form\Validator\Constraints\CircularDependencyValidator::class,
                'arguments' => [
                    'autoborna.lead.model.list',
                    'request_stack',
                ],
                'tag' => 'validator.constraint_validator',
            ],
            'autoborna.maxmind.doNotSellList' => [
                'class'     => Autoborna\CoreBundle\IpLookup\DoNotSellList\MaxMindDoNotSellList::class,
                'arguments' => [
                    'autoborna.helper.core_parameters',
                ],
            ],
            // Logger
            'autoborna.monolog.handler' => [
                'class'     => \Autoborna\CoreBundle\Monolog\Handler\FileLogHandler::class,
                'arguments' => [
                    'autoborna.helper.core_parameters',
                    'autoborna.monolog.fulltrace.formatter',
                ],
            ],

            // Update steps
            'autoborna.update.step_provider' => [
                'class' => \Autoborna\CoreBundle\Update\StepProvider::class,
            ],
            'autoborna.update.step.delete_cache' => [
                'class'     => \Autoborna\CoreBundle\Update\Step\DeleteCacheStep::class,
                'arguments' => [
                    'autoborna.helper.cache',
                    'translator',
                ],
                'tag' => 'autoborna.update_step',
            ],
            'autoborna.update.step.finalize' => [
                'class'     => \Autoborna\CoreBundle\Update\Step\FinalizeUpdateStep::class,
                'arguments' => [
                    'translator',
                    'autoborna.helper.paths',
                    'session',
                    'autoborna.helper.app_version',
                ],
                'tag' => 'autoborna.update_step',
            ],
            'autoborna.update.step.install_new_files' => [
                'class'     => \Autoborna\CoreBundle\Update\Step\InstallNewFilesStep::class,
                'arguments' => [
                    'translator',
                    'autoborna.helper.update',
                    'autoborna.helper.paths',
                ],
                'tag' => 'autoborna.update_step',
            ],
            'autoborna.update.step.remove_deleted_files' => [
                'class'     => \Autoborna\CoreBundle\Update\Step\RemoveDeletedFilesStep::class,
                'arguments' => [
                    'translator',
                    'autoborna.helper.paths',
                    'monolog.logger.autoborna',
                ],
                'tag' => 'autoborna.update_step',
            ],
            'autoborna.update.step.update_schema' => [
                'class'     => \Autoborna\CoreBundle\Update\Step\UpdateSchemaStep::class,
                'arguments' => [
                    'translator',
                    'service_container',
                ],
                'tag' => 'autoborna.update_step',
            ],
            'autoborna.update.step.update_translations' => [
                'class'     => \Autoborna\CoreBundle\Update\Step\UpdateTranslationsStep::class,
                'arguments' => [
                    'translator',
                    'autoborna.helper.language',
                    'monolog.logger.autoborna',
                ],
                'tag' => 'autoborna.update_step',
            ],
            'autoborna.update.step.checks' => [
                'class'     => \Autoborna\CoreBundle\Update\Step\PreUpdateChecksStep::class,
                'arguments' => [
                    'translator',
                    'autoborna.helper.update',
                ],
                'tag' => 'autoborna.update_step',
            ],
            'autoborna.update.checks.php' => [
                'class' => \Autoborna\CoreBundle\Helper\Update\PreUpdateChecks\CheckPhpVersion::class,
                'tag'   => 'autoborna.update_check',
            ],
            'autoborna.update.checks.database' => [
                'class'     => \Autoborna\CoreBundle\Helper\Update\PreUpdateChecks\CheckDatabaseDriverAndVersion::class,
                'arguments' => [
                    'doctrine.orm.default_entity_manager',
                ],
                'tag' => 'autoborna.update_check',
            ],
        ],
        'models' => [
            'autoborna.core.model.auditlog' => [
                'class' => 'Autoborna\CoreBundle\Model\AuditLogModel',
            ],
            'autoborna.core.model.notification' => [
                'class'     => 'Autoborna\CoreBundle\Model\NotificationModel',
                'arguments' => [
                    'autoborna.helper.paths',
                    'autoborna.helper.update',
                    'autoborna.helper.core_parameters',
                ],
                'methodCalls' => [
                    'setDisableUpdates' => [
                        '%autoborna.security.disableUpdates%',
                    ],
                ],
            ],
            'autoborna.core.model.form' => [
                'class' => 'Autoborna\CoreBundle\Model\FormModel',
            ],
        ],
        'validator' => [
            'autoborna.core.validator.file_upload' => [
                'class'     => \Autoborna\CoreBundle\Validator\FileUploadValidator::class,
                'arguments' => [
                    'translator',
                ],
            ],
        ],
    ],

    'ip_lookup_services' => [
        'extreme-ip' => [
            'display_name' => 'Extreme-IP',
            'class'        => 'Autoborna\CoreBundle\IpLookup\ExtremeIpLookup',
        ],
        'freegeoip' => [
            'display_name' => 'Ipstack.com',
            'class'        => 'Autoborna\CoreBundle\IpLookup\IpstackLookup',
        ],
        'geobytes' => [
            'display_name' => 'Geobytes',
            'class'        => 'Autoborna\CoreBundle\IpLookup\GeobytesLookup',
        ],
        'geoips' => [
            'display_name' => 'GeoIPs',
            'class'        => 'Autoborna\CoreBundle\IpLookup\GeoipsLookup',
        ],
        'ipinfodb' => [
            'display_name' => 'IPInfoDB',
            'class'        => 'Autoborna\CoreBundle\IpLookup\IpinfodbLookup',
        ],
        'maxmind_country' => [
            'display_name' => 'MaxMind - Country Geolocation',
            'class'        => 'Autoborna\CoreBundle\IpLookup\MaxmindCountryLookup',
        ],
        'maxmind_omni' => [
            'display_name' => 'MaxMind - Insights (formerly Omni]',
            'class'        => 'Autoborna\CoreBundle\IpLookup\MaxmindOmniLookup',
        ],
        'maxmind_precision' => [
            'display_name' => 'MaxMind - GeoIP2 Precision',
            'class'        => 'Autoborna\CoreBundle\IpLookup\MaxmindPrecisionLookup',
        ],
        'maxmind_download' => [
            'display_name' => 'MaxMind - GeoLite2 City Download',
            'class'        => 'Autoborna\CoreBundle\IpLookup\MaxmindDownloadLookup',
        ],
        'telize' => [
            'display_name' => 'Telize',
            'class'        => 'Autoborna\CoreBundle\IpLookup\TelizeLookup',
        ],
        'ip2loctionlocal' => [
            'display_name' => 'IP2Location Local Bin File',
            'class'        => 'Autoborna\CoreBundle\IpLookup\IP2LocationBinLookup',
        ],
        'ip2loctionapi' => [
            'display_name' => 'IP2Location Web Service',
            'class'        => 'Autoborna\CoreBundle\IpLookup\IP2LocationAPILookup',
        ],
    ],

    'parameters' => [
        'site_url'                        => '',
        'webroot'                         => '',
        '404_page'                        => '',
        'cache_path'                      => '%kernel.root_dir%/../var/cache',
        'log_path'                        => '%kernel.root_dir%/../var/logs',
        'max_log_files'                   => 7,
        'log_file_name'                   => 'autoborna_%kernel.environment%.php',
        'image_path'                      => 'media/images',
        'tmp_path'                        => '%kernel.root_dir%/../var/tmp',
        'theme'                           => 'blank',
        'theme_import_allowed_extensions' => ['json', 'twig', 'css', 'js', 'htm', 'html', 'txt', 'jpg', 'jpeg', 'png', 'gif'],
        'db_driver'                       => 'pdo_mysql',
        'db_host'                         => '127.0.0.1',
        'db_port'                         => 3306,
        'db_name'                         => '',
        'db_user'                         => '',
        'db_password'                     => '',
        'db_table_prefix'                 => '',
        'locale'                          => 'en_US',
        'secret_key'                      => 'temp',
        'dev_hosts'                       => [],
        'trusted_hosts'                   => [],
        'trusted_proxies'                 => [],
        'rememberme_key'                  => hash('sha1', uniqid(mt_rand())),
        'rememberme_lifetime'             => 31536000, //365 days in seconds
        'rememberme_path'                 => '/',
        'rememberme_domain'               => '',
        'default_pagelimit'               => 30,
        'default_timezone'                => 'UTC',
        'date_format_full'                => 'F j, Y g:i a T',
        'date_format_short'               => 'D, M d',
        'date_format_dateonly'            => 'F j, Y',
        'date_format_timeonly'            => 'g:i a',
        'ip_lookup_service'               => 'maxmind_download',
        'ip_lookup_auth'                  => '',
        'ip_lookup_config'                => [],
        'ip_lookup_create_organization'   => false,
        'transifex_username'              => '',
        'transifex_password'              => '',
        'update_stability'                => 'stable',
        'cookie_path'                     => '/',
        'cookie_domain'                   => '',
        'cookie_secure'                   => true,
        'cookie_httponly'                 => false,
        'do_not_track_ips'                => [],
        'do_not_track_bots'               => [
            'MSNBOT',
            'msnbot-media',
            'bingbot',
            'Googlebot',
            'Google Web Preview',
            'Mediapartners-Google',
            'Baiduspider',
            'Ezooms',
            'YahooSeeker',
            'Slurp',
            'AltaVista',
            'AVSearch',
            'Mercator',
            'Scooter',
            'InfoSeek',
            'Ultraseek',
            'Lycos',
            'Wget',
            'YandexBot',
            'Java/1.4.1_04',
            'SiteBot',
            'Exabot',
            'AhrefsBot',
            'MJ12bot',
            'NetSeer crawler',
            'TurnitinBot',
            'magpie-crawler',
            'Nutch Crawler',
            'CMS Crawler',
            'rogerbot',
            'Domnutch',
            'ssearch_bot',
            'XoviBot',
            'digincore',
            'fr-crawler',
            'SeznamBot',
            'Seznam screenshot-generator',
            'Facebot',
            'facebookexternalhit',
            'SimplePie',
            'Riddler',
            '007ac9 Crawler',
            '360Spider',
            'A6-Indexer',
            'ADmantX',
            'AHC',
            'AISearchBot',
            'APIs-Google',
            'Aboundex',
            'AddThis',
            'Adidxbot',
            'AdsBot-Google',
            'AdsTxtCrawler',
            'AdvBot',
            'Ahrefs',
            'AlphaBot',
            'Amazon CloudFront',
            'AndersPinkBot',
            'Apache-HttpClient',
            'Apercite',
            'AppEngine-Google',
            'Applebot',
            'ArchiveBot',
            'BDCbot',
            'BIGLOTRON',
            'BLEXBot',
            'BLP_bbot',
            'BTWebClient',
            'BUbiNG',
            'Baidu-YunGuanCe',
            'Barkrowler',
            'BehloolBot',
            'BingPreview',
            'BomboraBot',
            'Bot.AraTurka.com',
            'BoxcarBot',
            'BrandVerity',
            'Buck',
            'CC Metadata Scaper',
            'CCBot',
            'CapsuleChecker',
            'Cliqzbot',
            'CloudFlare-AlwaysOnline',
            'Companybook-Crawler',
            'ContextAd Bot',
            'CrunchBot',
            'CrystalSemanticsBot',
            'CyberPatrol',
            'DareBoost',
            'Datafeedwatch',
            'Daum',
            'DeuSu',
            'developers.google.com',
            'Diffbot',
            'Digg Deeper',
            'Digincore bot',
            'Discordbot',
            'Disqus',
            'DnyzBot',
            'Domain Re-Animator Bot',
            'DomainStatsBot',
            'DuckDuckBot',
            'DuckDuckGo-Favicons-Bot',
            'EZID',
            'Embedly',
            'EveryoneSocialBot',
            'ExtLinksBot',
            'FAST Enterprise Crawler',
            'FAST-WebCrawler',
            'Feedfetcher-Google',
            'Feedly',
            'Feedspotbot',
            'FemtosearchBot',
            'Fetch',
            'Fever',
            'Flamingo_SearchEngine',
            'FlipboardProxy',
            'Fyrebot',
            'GarlikCrawler',
            'Genieo',
            'Gigablast',
            'Gigabot',
            'GingerCrawler',
            'Gluten Free Crawler',
            'GnowitNewsbot',
            'Go-http-client',
            'Google-Adwords-Instant',
            'Gowikibot',
            'GrapeshotCrawler',
            'Grobbot',
            'HTTrack',
            'Hatena',
            'IAS crawler',
            'ICC-Crawler',
            'IndeedBot',
            'InterfaxScanBot',
            'IstellaBot',
            'James BOT',
            'Jamie\'s Spider',
            'Jetslide',
            'Jetty',
            'Jugendschutzprogramm-Crawler',
            'K7MLWCBot',
            'Kemvibot',
            'KosmioBot',
            'Landau-Media-Spider',
            'Laserlikebot',
            'Leikibot',
            'Linguee Bot',
            'LinkArchiver',
            'LinkedInBot',
            'LivelapBot',
            'Luminator-robots',
            'Mail.RU_Bot',
            'Mastodon',
            'MauiBot',
            'Mediatoolkitbot',
            'MegaIndex',
            'MeltwaterNews',
            'MetaJobBot',
            'MetaURI',
            'Miniflux',
            'MojeekBot',
            'Moreover',
            'MuckRack',
            'Multiviewbot',
            'NING',
            'NerdByNature.Bot',
            'NetcraftSurveyAgent',
            'Netvibes',
            'Nimbostratus-Bot',
            'Nuzzel',
            'Ocarinabot',
            'OpenHoseBot',
            'OrangeBot',
            'OutclicksBot',
            'PR-CY.RU',
            'PaperLiBot',
            'Pcore-HTTP',
            'PhantomJS',
            'PiplBot',
            'PocketParser',
            'Primalbot',
            'PrivacyAwareBot',
            'Pulsepoint',
            'Python-urllib',
            'Qwantify',
            'RankActiveLinkBot',
            'RetrevoPageAnalyzer',
            'SBL-BOT',
            'SEMrushBot',
            'SEOkicks',
            'SWIMGBot',
            'SafeDNSBot',
            'SafeSearch microdata crawler',
            'ScoutJet',
            'Scrapy',
            'Screaming Frog SEO Spider',
            'SemanticScholarBot',
            'SimpleCrawler',
            'Siteimprove.com',
            'SkypeUriPreview',
            'Slack-ImgProxy',
            'Slackbot',
            'Snacktory',
            'SocialRankIOBot',
            'Sogou',
            'Sonic',
            'StorygizeBot',
            'SurveyBot',
            'Sysomos',
            'TangibleeBot',
            'TelegramBot',
            'Teoma',
            'Thinklab',
            'TinEye',
            'ToutiaoSpider',
            'Traackr.com',
            'Trove',
            'TweetmemeBot',
            'Twitterbot',
            'Twurly',
            'Upflow',
            'UptimeRobot',
            'UsineNouvelleCrawler',
            'Veoozbot',
            'WeSEE:Search',
            'WhatsApp',
            'Xenu Link Sleuth',
            'Y!J',
            'YaK',
            'Yahoo Link Preview',
            'Yeti',
            'YisouSpider',
            'Zabbix',
            'ZoominfoBot',
            'ZumBot',
            'ZuperlistBot',
            '^LCC ',
            'acapbot',
            'acoonbot',
            'adbeat_bot',
            'adscanner',
            'aiHitBot',
            'antibot',
            'arabot',
            'archive.org_bot',
            'axios',
            'backlinkcrawler',
            'betaBot',
            'bibnum.bnf',
            'binlar',
            'bitlybot',
            'blekkobot',
            'blogmuraBot',
            'bnf.fr_bot',
            'bot-pge.chlooe.com',
            'botify',
            'brainobot',
            'buzzbot',
            'cXensebot',
            'careerbot',
            'centurybot9',
            'changedetection',
            'check_http',
            'citeseerxbot',
            'coccoc',
            'collection@infegy.com',
            'content crawler spider',
            'contxbot',
            'convera',
            'crawler4j',
            'curl',
            'datagnionbot',
            'dcrawl',
            'deadlinkchecker',
            'discobot',
            'domaincrawler',
            'dotbot',
            'drupact',
            'ec2linkfinder',
            'edisterbot',
            'electricmonk',
            'elisabot',
            'epicbot',
            'eright',
            'europarchive.org',
            'exabot',
            'ezooms',
            'filterdb.iss.net',
            'findlink',
            'findthatfile',
            'findxbot',
            'fluffy',
            'fuelbot',
            'g00g1e.net',
            'g2reader-bot',
            'gnam gnam spider',
            'google-xrawler',
            'grub.org',
            'gslfbot',
            'heritrix',
            'http_get',
            'httpunit',
            'ia_archiver',
            'ichiro',
            'imrbot',
            'integromedb',
            'intelium_bot',
            'ip-web-crawler.com',
            'ips-agent',
            'iskanie',
            'it2media-domain-crawler',
            'jyxobot',
            'lb-spider',
            'libwww',
            'linkapediabot',
            'linkdex',
            'lipperhey',
            'lssbot',
            'lssrocketcrawler',
            'ltx71',
            'mappydata',
            'memorybot',
            'mindUpBot',
            'mlbot',
            'moatbot',
            'msnbot',
            'msrbot',
            'nerdybot',
            'netEstate NE Crawler',
            'netresearchserver',
            'newsharecounts',
            'newspaper',
            'niki-bot',
            'nutch',
            'okhttp',
            'omgili',
            'openindexspider',
            'page2rss',
            'panscient',
            'phpcrawl',
            'pingdom',
            'pinterest',
            'postrank',
            'proximic',
            'psbot',
            'purebot',
            'python-requests',
            'redditbot',
            'scribdbot',
            'seekbot',
            'semanticbot',
            'sentry',
            'seoscanners',
            'seznambot',
            'sistrix crawler',
            'sitebot',
            'siteexplorer.info',
            'smtbot',
            'spbot',
            'speedy',
            'summify',
            'tagoobot',
            'toplistbot',
            'tracemyfile',
            'trendictionbot',
            'turnitinbot',
            'twengabot',
            'um-LN',
            'urlappendbot',
            'vebidoobot',
            'vkShare',
            'voilabot',
            'wbsearchbot',
            'web-archive-net.com.bot',
            'webcompanycrawler',
            'webmon',
            'wget',
            'wocbot',
            'woobot',
            'woriobot',
            'wotbox',
            'xovibot',
            'yacybot',
            'yandex.com',
            'yanga',
            'yoozBot',
            'zgrab',
        ],
        'do_not_track_internal_ips' => [],
        'track_private_ip_ranges'   => false,
        'link_shortener_url'        => null,
        'cached_data_timeout'       => 10,
        'batch_sleep_time'          => 1,
        'batch_campaign_sleep_time' => false,
        'transliterate_page_title'  => false,
        'cors_restrict_domains'     => true,
        'cors_valid_domains'        => [],
        'max_entity_lock_time'      => 0,
        'default_daterange_filter'  => '-1 month',
        'debug'                     => false,
        'rss_notification_url'      => '',
        'translations_list_url'     => 'https://language-packs.autoborna.com/manifest.json',
        'translations_fetch_url'    => 'https://language-packs.autoborna.com/',
        'stats_update_url'          => 'https://updates.autoborna.org/stats/send', // set to empty in config file to disable
        'install_source'            => 'Autoborna',
        'system_update_url'         => 'https://api.github.com/repos/autoborna/autoborna/releases',
        'editor_fonts'              => [
            [
                'name' => 'Arial',
                'font' => 'Arial, Helvetica Neue, Helvetica, sans-serif',
            ],
            [
                'name' => 'Bitter',
                'font' => 'Bitter, Georgia, Times, Times New Roman, serif',
                'url'  => 'https://fonts.googleapis.com/css?family=Bitter',
            ],
            [
                'name' => 'Courier New',
                'font' => 'Courier New, Courier, Lucida Sans Typewriter, Lucida Typewriter, monospace',
            ],
            [
                'name' => 'Droid Serif',
                'font' => 'Droid Serif, Georgia, Times, Times New Roman, serif',
                'url'  => 'https://fonts.googleapis.com/css?family=Droid+Serif',
            ],
            [
                'name' => 'Georgia',
                'font' => 'Georgia, Times, Times New Roman, serif',
            ],
            [
                'name' => 'Helvetica',
                'font' => 'Helvetica Neue, Helvetica, Arial, sans-serif',
            ],
            [
                'name' => 'Lato',
                'font' => 'Lato, Tahoma, Verdana, Segoe, sans-serif',
                'url'  => 'https://fonts.googleapis.com/css?family=Lato',
            ],
            [
                'name' => 'Lucida Sans Unicode',
                'font' => 'Lucida Sans Unicode, Lucida Grande, Lucida Sans, Geneva, Verdana, sans-serif',
            ],
            [
                'name' => 'Montserrat',
                'font' => 'Montserrat, Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif',
                'url'  => 'https://fonts.googleapis.com/css?family=Montserrat',
            ],
            [
                'name' => 'Open Sans',
                'font' => 'Open Sans, Helvetica Neue, Helvetica, Arial, sans-serif',
                'url'  => 'https://fonts.googleapis.com/css?family=Open+Sans',
            ],
            [
                'name' => 'Roboto',
                'font' => 'Roboto, Tahoma, Verdana, Segoe, sans-serif',
                'url'  => 'https://fonts.googleapis.com/css?family=Roboto',
            ],
            [
                'name' => 'Source Sans Pro',
                'font' => 'Source Sans Pro, Tahoma, Verdana, Segoe, sans-serif',
                'url'  => 'https://fonts.googleapis.com/css?family=Source+Sans+Pro',
            ],
            [
                'name' => 'Tahoma',
                'font' => 'Tahoma, Geneva, Segoe, sans-serif',
            ],
            [
                'name' => 'Times New Roman',
                'font' => 'TimesNewRoman, Times New Roman, Times, Beskerville, Georgia, serif',
            ],
            [
                'name' => 'Trebuchet MS',
                'font' => 'Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif',
            ],
            [
                'name' => 'Ubuntu',
                'font' => 'Ubuntu, Tahoma, Verdana, Segoe, sans-serif',
                'url'  => 'https://fonts.googleapis.com/css?family=Ubuntu',
            ],
            [
                'name' => 'Verdana',
                'font' => 'Verdana, Geneva, sans-serif',
            ],
            [
                'name' => 'ヒラギノ角ゴ Pro W3',
                'font' => 'ヒラギノ角ゴ Pro W3, Hiragino Kaku Gothic Pro,Osaka, メイリオ, Meiryo, ＭＳ Ｐゴシック, MS PGothic, sans-serif',
            ],
            [
                'name' => 'メイリオ',
                'font' => 'メイリオ, Meiryo, ＭＳ Ｐゴシック, MS PGothic, ヒラギノ角ゴ Pro W3, Hiragino Kaku Gothic Pro,Osaka, sans-serif',
            ],
        ],
        'composer_updates' => false,
    ],
];
