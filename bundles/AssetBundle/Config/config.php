<?php

return [
    'routes' => [
        'main' => [
            'autoborna_asset_index' => [
                'path'       => '/assets/{page}',
                'controller' => 'AutobornaAssetBundle:Asset:index',
            ],
            'autoborna_asset_remote' => [
                'path'       => '/assets/remote',
                'controller' => 'AutobornaAssetBundle:Asset:remote',
            ],
            'autoborna_asset_action' => [
                'path'       => '/assets/{objectAction}/{objectId}',
                'controller' => 'AutobornaAssetBundle:Asset:execute',
            ],
        ],
        'api' => [
            'autoborna_api_assetsstandard' => [
                'standard_entity' => true,
                'name'            => 'assets',
                'path'            => '/assets',
                'controller'      => 'AutobornaAssetBundle:Api\AssetApi',
            ],
        ],
        'public' => [
            'autoborna_asset_download' => [
                'path'       => '/asset/{slug}',
                'controller' => 'AutobornaAssetBundle:Public:download',
                'defaults'   => [
                    'slug' => '',
                ],
            ],
        ],
    ],

    'menu' => [
        'main' => [
            'items' => [
                'autoborna.asset.assets' => [
                    'route'    => 'autoborna_asset_index',
                    'access'   => ['asset:assets:viewown', 'asset:assets:viewother'],
                    'parent'   => 'autoborna.core.components',
                    'priority' => 300,
                ],
            ],
        ],
    ],

    'categories' => [
        'asset' => null,
    ],

    'services' => [
        'permissions' => [
            'autoborna.asset.permissions' => [
                'class'     => \Autoborna\AssetBundle\Security\Permissions\AssetPermissions::class,
                'arguments' => [
                    'autoborna.helper.core_parameters',
                ],
            ],
        ],
        'events' => [
            'autoborna.asset.subscriber' => [
                'class'     => \Autoborna\AssetBundle\EventListener\AssetSubscriber::class,
                'arguments' => [
                    'autoborna.helper.ip_lookup',
                    'autoborna.core.model.auditlog',
                ],
            ],
            'autoborna.asset.pointbundle.subscriber' => [
                'class'     => \Autoborna\AssetBundle\EventListener\PointSubscriber::class,
                'arguments' => [
                    'autoborna.point.model.point',
                ],
            ],
            'autoborna.asset.formbundle.subscriber' => [
                'class'     => Autoborna\AssetBundle\EventListener\FormSubscriber::class,
                'arguments' => [
                    'autoborna.asset.model.asset',
                    'translator',
                    'autoborna.helper.template.analytics',
                    'templating.helper.assets',
                    'autoborna.helper.theme',
                    'autoborna.helper.templating',
                    'autoborna.helper.core_parameters',
                ],
            ],
            'autoborna.asset.campaignbundle.subscriber' => [
                'class'     => \Autoborna\AssetBundle\EventListener\CampaignSubscriber::class,
                'arguments' => [
                    'autoborna.campaign.executioner.realtime',
                ],
            ],
            'autoborna.asset.reportbundle.subscriber' => [
                'class'     => \Autoborna\AssetBundle\EventListener\ReportSubscriber::class,
                'arguments' => [
                    'autoborna.lead.model.company_report_data',
                    'autoborna.asset.repository.download',
                ],
            ],
            'autoborna.asset.builder.subscriber' => [
                'class'     => \Autoborna\AssetBundle\EventListener\BuilderSubscriber::class,
                'arguments' => [
                    'autoborna.security',
                    'autoborna.asset.helper.token',
                    'autoborna.tracker.contact',
                    'autoborna.helper.token_builder.factory',
                ],
            ],
            'autoborna.asset.leadbundle.subscriber' => [
                'class'     => \Autoborna\AssetBundle\EventListener\LeadSubscriber::class,
                'arguments' => [
                    'autoborna.asset.model.asset',
                    'translator',
                    'router',
                    'autoborna.asset.repository.download',
                ],
            ],
            'autoborna.asset.pagebundle.subscriber' => [
                'class' => \Autoborna\AssetBundle\EventListener\PageSubscriber::class,
            ],
            'autoborna.asset.emailbundle.subscriber' => [
                'class' => \Autoborna\AssetBundle\EventListener\EmailSubscriber::class,
            ],
            'autoborna.asset.configbundle.subscriber' => [
                'class' => \Autoborna\AssetBundle\EventListener\ConfigSubscriber::class,
            ],
            'autoborna.asset.search.subscriber' => [
                'class'     => \Autoborna\AssetBundle\EventListener\SearchSubscriber::class,
                'arguments' => [
                    'autoborna.asset.model.asset',
                    'autoborna.security',
                    'autoborna.helper.user',
                    'autoborna.helper.templating',
                ],
            ],
            'autoborna.asset.stats.subscriber' => [
                'class'     => \Autoborna\AssetBundle\EventListener\StatsSubscriber::class,
                'arguments' => [
                    'autoborna.security',
                    'doctrine.orm.entity_manager',
                ],
            ],
            'oneup_uploader.pre_upload' => [
                'class'     => \Autoborna\AssetBundle\EventListener\UploadSubscriber::class,
                'arguments' => [
                    'autoborna.helper.core_parameters',
                    'autoborna.asset.model.asset',
                    'autoborna.core.validator.file_upload',
                ],
            ],
            'autoborna.asset.dashboard.subscriber' => [
                'class'     => \Autoborna\AssetBundle\EventListener\DashboardSubscriber::class,
                'arguments' => [
                    'autoborna.asset.model.asset',
                    'router',
                ],
            ],
            'autoborna.asset.subscriber.determine_winner' => [
                'class'     => \Autoborna\AssetBundle\EventListener\DetermineWinnerSubscriber::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                    'translator',
                ],
            ],
        ],
        'forms' => [
            'autoborna.form.type.asset' => [
                'class'     => \Autoborna\AssetBundle\Form\Type\AssetType::class,
                'arguments' => [
                    'translator',
                    'autoborna.asset.model.asset',
                ],
            ],
            'autoborna.form.type.pointaction_assetdownload' => [
                'class' => \Autoborna\AssetBundle\Form\Type\PointActionAssetDownloadType::class,
            ],
            'autoborna.form.type.campaignevent_assetdownload' => [
                'class' => \Autoborna\AssetBundle\Form\Type\CampaignEventAssetDownloadType::class,
            ],
            'autoborna.form.type.formsubmit_assetdownload' => [
                'class' => \Autoborna\AssetBundle\Form\Type\FormSubmitActionDownloadFileType::class,
            ],
            'autoborna.form.type.assetlist' => [
                'class'     => \Autoborna\AssetBundle\Form\Type\AssetListType::class,
                'arguments' => [
                    'autoborna.security',
                    'autoborna.asset.model.asset',
                    'autoborna.helper.user',
                ],
            ],
            'autoborna.form.type.assetconfig' => [
                'class' => \Autoborna\AssetBundle\Form\Type\ConfigType::class,
            ],
        ],
        'others' => [
            'autoborna.asset.upload.error.handler' => [
                'class'     => \Autoborna\AssetBundle\ErrorHandler\DropzoneErrorHandler::class,
                'arguments' => 'autoborna.factory',
            ],
            // Override the DropzoneController
            'oneup_uploader.controller.dropzone.class' => \Autoborna\AssetBundle\Controller\UploadController::class,
            'autoborna.asset.helper.token'                => [
                'class'     => \Autoborna\AssetBundle\Helper\TokenHelper::class,
                'arguments' => 'autoborna.asset.model.asset',
            ],
        ],
        'models' => [
            'autoborna.asset.model.asset' => [
                'class'     => \Autoborna\AssetBundle\Model\AssetModel::class,
                'arguments' => [
                    'autoborna.lead.model.lead',
                    'autoborna.category.model.category',
                    'request_stack',
                    'autoborna.helper.ip_lookup',
                    'autoborna.helper.core_parameters',
                    'autoborna.lead.service.device_creator_service',
                    'autoborna.lead.factory.device_detector_factory',
                    'autoborna.lead.service.device_tracking_service',
                    'autoborna.tracker.contact',
                ],
            ],
        ],
        'fixtures' => [
            'autoborna.asset.fixture.asset' => [
                'class'     => \Autoborna\AssetBundle\DataFixtures\ORM\LoadAssetData::class,
                'tag'       => \Doctrine\Bundle\FixturesBundle\DependencyInjection\CompilerPass\FixturesCompilerPass::FIXTURE_TAG,
            ],
        ],
        'repositories' => [
            'autoborna.asset.repository.download' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => \Autoborna\AssetBundle\Entity\Download::class,
            ],
        ],
    ],

    'parameters' => [
        'upload_dir'         => '%kernel.root_dir%/../media/files',
        'max_size'           => '6',
        'allowed_extensions' => ['csv', 'doc', 'docx', 'epub', 'gif', 'jpg', 'jpeg', 'mpg', 'mpeg', 'mp3', 'odt', 'odp', 'ods', 'pdf', 'png', 'ppt', 'pptx', 'tif', 'tiff', 'txt', 'xls', 'xlsx', 'wav'],
    ],
];
