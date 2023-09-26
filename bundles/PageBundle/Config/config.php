<?php

return [
    'routes' => [
        'main' => [
            'autoborna_page_index' => [
                'path'       => '/pages/{page}',
                'controller' => 'AutobornaPageBundle:Page:index',
            ],
            'autoborna_page_action' => [
                'path'       => '/pages/{objectAction}/{objectId}',
                'controller' => 'AutobornaPageBundle:Page:execute',
            ],
            'autoborna_page_results' => [
                'path'       => '/pages/results/{objectId}/{page}',
                'controller' => 'AutobornaPageBundle:Page:results',
            ],
            'autoborna_page_export' => [
                'path'       => '/pages/results/{objectId}/export/{format}',
                'controller' => 'AutobornaPageBundle:Page:export',
                'defaults'   => [
                    'format' => 'csv',
                ],
            ],
        ],
        'public' => [
            'autoborna_page_tracker' => [
                'path'       => '/mtracking.gif',
                'controller' => 'AutobornaPageBundle:Public:trackingImage',
            ],
            'autoborna_page_tracker_cors' => [
                'path'       => '/mtc/event',
                'controller' => 'AutobornaPageBundle:Public:tracking',
            ],
            'autoborna_page_tracker_getcontact' => [
                'path'       => '/mtc',
                'controller' => 'AutobornaPageBundle:Public:getContactId',
            ],
            'autoborna_url_redirect' => [
                'path'       => '/r/{redirectId}',
                'controller' => 'AutobornaPageBundle:Public:redirect',
            ],
            'autoborna_page_redirect' => [
                'path'       => '/redirect/{redirectId}',
                'controller' => 'AutobornaPageBundle:Public:redirect',
            ],
            'autoborna_page_preview' => [
                'path'       => '/page/preview/{id}',
                'controller' => 'AutobornaPageBundle:Public:preview',
            ],
            'autoborna_gated_video_hit' => [
                'path'       => '/video/hit',
                'controller' => 'AutobornaPageBundle:Public:hitVideo',
            ],
        ],
        'api' => [
            'autoborna_api_pagesstandard' => [
                'standard_entity' => true,
                'name'            => 'pages',
                'path'            => '/pages',
                'controller'      => 'AutobornaPageBundle:Api\PageApi',
            ],
        ],
        'catchall' => [
            'autoborna_page_public' => [
                'path'         => '/{slug}',
                'controller'   => 'AutobornaPageBundle:Public:index',
                'requirements' => [
                    'slug' => '^(?!(_(profiler|wdt)|css|images|js|favicon.ico|apps/bundles/|plugins/)).+',
                ],
            ],
        ],
    ],

    'menu' => [
        'main' => [
            'items' => [
                'autoborna.page.pages' => [
                    'route'    => 'autoborna_page_index',
                    'access'   => ['page:pages:viewown', 'page:pages:viewother'],
                    'parent'   => 'autoborna.core.components',
                    'priority' => 100,
                ],
            ],
        ],
    ],

    'categories' => [
        'page' => null,
    ],

    'services' => [
        'events' => [
            'autoborna.page.subscriber' => [
                'class'     => \Autoborna\PageBundle\EventListener\PageSubscriber::class,
                'arguments' => [
                    'templating.helper.assets',
                    'autoborna.helper.ip_lookup',
                    'autoborna.core.model.auditlog',
                    'autoborna.page.model.page',
                    'monolog.logger.autoborna',
                    'autoborna.page.repository.hit',
                    'autoborna.page.repository.page',
                    'autoborna.page.repository.redirect',
                    'autoborna.lead.repository.lead',
                ],
            ],
            'autoborna.pagebuilder.subscriber' => [
                'class'     => \Autoborna\PageBundle\EventListener\BuilderSubscriber::class,
                'arguments' => [
                    'autoborna.security',
                    'autoborna.page.helper.token',
                    'autoborna.helper.integration',
                    'autoborna.page.model.page',
                    'autoborna.helper.token_builder.factory',
                    'translator',
                    'doctrine.dbal.default_connection',
                    'autoborna.helper.templating',
                ],
            ],
            'autoborna.pagetoken.subscriber' => [
                'class' => \Autoborna\PageBundle\EventListener\TokenSubscriber::class,
            ],
            'autoborna.page.pointbundle.subscriber' => [
                'class'     => \Autoborna\PageBundle\EventListener\PointSubscriber::class,
                'arguments' => [
                    'autoborna.point.model.point',
                ],
            ],
            'autoborna.page.reportbundle.subscriber' => [
                'class'     => \Autoborna\PageBundle\EventListener\ReportSubscriber::class,
                'arguments' => [
                    'autoborna.lead.model.company_report_data',
                    'autoborna.page.repository.hit',
                    'translator',
                ],
            ],
            'autoborna.page.campaignbundle.subscriber' => [
                'class'     => \Autoborna\PageBundle\EventListener\CampaignSubscriber::class,
                'arguments' => [
                    'autoborna.lead.model.lead',
                    'autoborna.page.helper.tracking',
                    'autoborna.campaign.executioner.realtime',
                ],
            ],
            'autoborna.page.leadbundle.subscriber' => [
                'class'     => \Autoborna\PageBundle\EventListener\LeadSubscriber::class,
                'arguments' => [
                    'autoborna.page.model.page',
                    'autoborna.page.model.video',
                    'translator',
                    'router',
                ],
                'methodCalls' => [
                    'setModelFactory' => ['autoborna.model.factory'],
                ],
            ],
            'autoborna.page.calendarbundle.subscriber' => [
                'class'     => \Autoborna\PageBundle\EventListener\CalendarSubscriber::class,
                'arguments' => [
                    'autoborna.page.model.page',
                    'doctrine.dbal.default_connection',
                    'autoborna.security',
                    'translator',
                    'router',
                ],
            ],
            'autoborna.page.configbundle.subscriber' => [
                'class' => \Autoborna\PageBundle\EventListener\ConfigSubscriber::class,
            ],
            'autoborna.page.search.subscriber' => [
                'class'     => \Autoborna\PageBundle\EventListener\SearchSubscriber::class,
                'arguments' => [
                    'autoborna.helper.user',
                    'autoborna.page.model.page',
                    'autoborna.security',
                    'autoborna.helper.templating',
                ],
            ],
            'autoborna.page.webhook.subscriber' => [
                'class'     => \Autoborna\PageBundle\EventListener\WebhookSubscriber::class,
                'arguments' => [
                    'autoborna.webhook.model.webhook',
                ],
            ],
            'autoborna.page.dashboard.subscriber' => [
                'class'     => \Autoborna\PageBundle\EventListener\DashboardSubscriber::class,
                'arguments' => [
                    'autoborna.page.model.page',
                    'router',
                ],
            ],
            'autoborna.page.js.subscriber' => [
                'class'     => \Autoborna\PageBundle\EventListener\BuildJsSubscriber::class,
                'arguments' => [
                    'templating.helper.assets',
                    'autoborna.page.helper.tracking',
                    'router',
                ],
            ],
            'autoborna.page.maintenance.subscriber' => [
                'class'     => \Autoborna\PageBundle\EventListener\MaintenanceSubscriber::class,
                'arguments' => [
                    'doctrine.dbal.default_connection',
                    'translator',
                ],
            ],
            'autoborna.page.stats.subscriber' => [
                'class'     => \Autoborna\PageBundle\EventListener\StatsSubscriber::class,
                'arguments' => [
                    'autoborna.security',
                    'doctrine.orm.entity_manager',
                ],
            ],
            'autoborna.page.subscriber.determine_winner' => [
                'class'     => \Autoborna\PageBundle\EventListener\DetermineWinnerSubscriber::class,
                'arguments' => [
                    'autoborna.page.repository.hit',
                    'translator',
                ],
            ],
        ],
        'forms' => [
            'autoborna.form.type.page' => [
                'class'     => \Autoborna\PageBundle\Form\Type\PageType::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                    'autoborna.page.model.page',
                    'autoborna.security',
                    'autoborna.helper.user',
                    'autoborna.helper.theme',
                ],
            ],
            'autoborna.form.type.pagevariant' => [
                'class'     => \Autoborna\PageBundle\Form\Type\VariantType::class,
                'arguments' => ['autoborna.page.model.page'],
            ],
            'autoborna.form.type.pointaction_pointhit' => [
                'class' => \Autoborna\PageBundle\Form\Type\PointActionPageHitType::class,
            ],
            'autoborna.form.type.pointaction_urlhit' => [
                'class' => \Autoborna\PageBundle\Form\Type\PointActionUrlHitType::class,
            ],
            'autoborna.form.type.pagehit.campaign_trigger' => [
                'class' => \Autoborna\PageBundle\Form\Type\CampaignEventPageHitType::class,
            ],
            'autoborna.form.type.pagelist' => [
                'class'     => \Autoborna\PageBundle\Form\Type\PageListType::class,
                'arguments' => [
                    'autoborna.page.model.page',
                    'autoborna.security',
                ],
            ],
            'autoborna.form.type.preferencecenterlist' => [
                'class'     => \Autoborna\PageBundle\Form\Type\PreferenceCenterListType::class,
                'arguments' => [
                    'autoborna.page.model.page',
                    'autoborna.security',
                ],
            ],
            'autoborna.form.type.page_abtest_settings' => [
                'class' => \Autoborna\PageBundle\Form\Type\AbTestPropertiesType::class,
            ],
            'autoborna.form.type.page_publish_dates' => [
                'class' => \Autoborna\PageBundle\Form\Type\PagePublishDatesType::class,
            ],
            'autoborna.form.type.pageconfig' => [
                'class' => \Autoborna\PageBundle\Form\Type\ConfigType::class,
            ],
            'autoborna.form.type.trackingconfig' => [
                'class' => \Autoborna\PageBundle\Form\Type\ConfigTrackingPageType::class,
            ],
            'autoborna.form.type.redirect_list' => [
                'class'     => \Autoborna\PageBundle\Form\Type\RedirectListType::class,
                'arguments' => ['autoborna.helper.core_parameters'],
            ],
            'autoborna.form.type.page_dashboard_hits_in_time_widget' => [
                'class' => \Autoborna\PageBundle\Form\Type\DashboardHitsInTimeWidgetType::class,
            ],
            'autoborna.page.tracking.pixel.send' => [
                'class'     => \Autoborna\PageBundle\Form\Type\TrackingPixelSendType::class,
                'arguments' => [
                    'autoborna.page.helper.tracking',
                ],
            ],
        ],
        'models' => [
            'autoborna.page.model.page' => [
                'class'     => \Autoborna\PageBundle\Model\PageModel::class,
                'arguments' => [
                    'autoborna.helper.cookie',
                    'autoborna.helper.ip_lookup',
                    'autoborna.lead.model.lead',
                    'autoborna.lead.model.field',
                    'autoborna.page.model.redirect',
                    'autoborna.page.model.trackable',
                    'autoborna.queue.service',
                    'autoborna.lead.model.company',
                    'autoborna.tracker.device',
                    'autoborna.tracker.contact',
                    'autoborna.helper.core_parameters',
                ],
                'methodCalls' => [
                    'setCatInUrl' => [
                        '%autoborna.cat_in_page_url%',
                    ],
                ],
            ],
            'autoborna.page.model.redirect' => [
                'class'     => 'Autoborna\PageBundle\Model\RedirectModel',
                'arguments' => [
                    'autoborna.helper.url',
                ],
            ],
            'autoborna.page.model.trackable' => [
                'class'     => \Autoborna\PageBundle\Model\TrackableModel::class,
                'arguments' => [
                    'autoborna.page.model.redirect',
                    'autoborna.lead.repository.field',
                ],
            ],
            'autoborna.page.model.video' => [
                'class'     => 'Autoborna\PageBundle\Model\VideoModel',
                'arguments' => [
                    'autoborna.helper.ip_lookup',
                    'autoborna.tracker.contact',
                ],
            ],
            'autoborna.page.model.tracking.404' => [
                'class'     => \Autoborna\PageBundle\Model\Tracking404Model::class,
                'arguments' => [
                    'autoborna.helper.core_parameters',
                    'autoborna.tracker.contact',
                    'autoborna.page.model.page',
                ],
            ],
        ],
        'repositories' => [
            'autoborna.page.repository.hit' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Autoborna\PageBundle\Entity\Hit::class,
                ],
            ],
            'autoborna.page.repository.page' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Autoborna\PageBundle\Entity\Page::class,
                ],
            ],
            'autoborna.page.repository.redirect' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Autoborna\PageBundle\Entity\Redirect::class,
                ],
            ],
        ],
        'fixtures' => [
            'autoborna.page.fixture.page_category' => [
                'class'     => \Autoborna\PageBundle\DataFixtures\ORM\LoadPageCategoryData::class,
                'tag'       => \Doctrine\Bundle\FixturesBundle\DependencyInjection\CompilerPass\FixturesCompilerPass::FIXTURE_TAG,
                'arguments' => ['autoborna.category.model.category'],
            ],
            'autoborna.page.fixture.page' => [
                'class'     => \Autoborna\PageBundle\DataFixtures\ORM\LoadPageData::class,
                'tag'       => \Doctrine\Bundle\FixturesBundle\DependencyInjection\CompilerPass\FixturesCompilerPass::FIXTURE_TAG,
                'arguments' => ['autoborna.page.model.page'],
            ],
            'autoborna.page.fixture.page_hit' => [
                'class'     => \Autoborna\PageBundle\DataFixtures\ORM\LoadPageHitData::class,
                'tag'       => \Doctrine\Bundle\FixturesBundle\DependencyInjection\CompilerPass\FixturesCompilerPass::FIXTURE_TAG,
                'arguments' => ['autoborna.page.model.page'],
            ],
        ],
        'other' => [
            'autoborna.page.helper.token' => [
                'class'     => 'Autoborna\PageBundle\Helper\TokenHelper',
                'arguments' => 'autoborna.page.model.page',
            ],
            'autoborna.page.helper.tracking' => [
                'class'     => 'Autoborna\PageBundle\Helper\TrackingHelper',
                'arguments' => [
                    'session',
                    'autoborna.helper.core_parameters',
                    'request_stack',
                    'autoborna.tracker.contact',
                ],
            ],
        ],
    ],

    'parameters' => [
        'cat_in_page_url'       => false,
        'google_analytics'      => null,
        'track_contact_by_ip'   => false,
        'track_by_tracking_url' => false,
        'redirect_list_types'   => [
            '301' => 'autoborna.page.form.redirecttype.permanent',
            '302' => 'autoborna.page.form.redirecttype.temporary',
        ],
        'google_analytics_id'                   => null,
        'google_analytics_trackingpage_enabled' => false,
        'google_analytics_landingpage_enabled'  => false,
        'google_analytics_anonymize_ip'         => false,
        'facebook_pixel_id'                     => null,
        'facebook_pixel_trackingpage_enabled'   => false,
        'facebook_pixel_landingpage_enabled'    => false,
        'do_not_track_404_anonymous'            => false,
    ],
];
