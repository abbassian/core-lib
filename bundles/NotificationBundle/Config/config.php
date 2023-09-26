<?php

return [
    'services' => [
        'events' => [
            'autoborna.notification.campaignbundle.subscriber' => [
                'class'     => \Autoborna\NotificationBundle\EventListener\CampaignSubscriber::class,
                'arguments' => [
                    'autoborna.helper.integration',
                    'autoborna.notification.model.notification',
                    'autoborna.notification.api',
                    'event_dispatcher',
                    'autoborna.lead.model.dnc',
                ],
            ],
            'autoborna.notification.campaignbundle.condition_subscriber' => [
                'class'     => \Autoborna\NotificationBundle\EventListener\CampaignConditionSubscriber::class,
            ],
            'autoborna.notification.pagebundle.subscriber' => [
                'class'     => \Autoborna\NotificationBundle\EventListener\PageSubscriber::class,
                'arguments' => [
                    'templating.helper.assets',
                    'autoborna.helper.integration',
                ],
            ],
            'autoborna.core.js.subscriber' => [
                'class'     => \Autoborna\NotificationBundle\EventListener\BuildJsSubscriber::class,
                'arguments' => [
                    'autoborna.helper.notification',
                    'autoborna.helper.integration',
                    'router',
                ],
            ],
            'autoborna.notification.notificationbundle.subscriber' => [
                'class'     => \Autoborna\NotificationBundle\EventListener\NotificationSubscriber::class,
                'arguments' => [
                    'autoborna.core.model.auditlog',
                    'autoborna.page.model.trackable',
                    'autoborna.page.helper.token',
                    'autoborna.asset.helper.token',
                ],
            ],
            'autoborna.notification.subscriber.channel' => [
                'class'     => \Autoborna\NotificationBundle\EventListener\ChannelSubscriber::class,
                'arguments' => [
                    'autoborna.helper.integration',
                ],
            ],
            'autoborna.notification.stats.subscriber' => [
                'class'     => \Autoborna\NotificationBundle\EventListener\StatsSubscriber::class,
                'arguments' => [
                    'autoborna.security',
                    'doctrine.orm.entity_manager',
                ],
            ],
            'autoborna.notification.mobile_notification.report.subscriber' => [
                'class'     => \Autoborna\NotificationBundle\EventListener\ReportSubscriber::class,
                'arguments' => [
                    'doctrine.dbal.default_connection',
                    'autoborna.lead.model.company_report_data',
                    'autoborna.notification.repository.stat',
                ],
            ],
            'autoborna.notification.configbundle.subscriber' => [
                'class' => Autoborna\NotificationBundle\EventListener\ConfigSubscriber::class,
            ],
        ],
        'forms' => [
            'autoborna.form.type.notification' => [
                'class' => 'Autoborna\NotificationBundle\Form\Type\NotificationType',
            ],
            'autoborna.form.type.mobile.notification' => [
                'class' => \Autoborna\NotificationBundle\Form\Type\MobileNotificationType::class,
            ],
            'autoborna.form.type.mobile.notification_details' => [
                'class'     => \Autoborna\NotificationBundle\Form\Type\MobileNotificationDetailsType::class,
                'arguments' => [
                    'autoborna.helper.integration',
                ],
            ],
            'autoborna.form.type.notificationconfig' => [
                'class' => 'Autoborna\NotificationBundle\Form\Type\ConfigType',
            ],
            'autoborna.notification.config' => [
                'class' => \Autoborna\NotificationBundle\Form\Type\NotificationConfigType::class,
            ],
            'autoborna.form.type.notificationsend_list' => [
                'class'     => 'Autoborna\NotificationBundle\Form\Type\NotificationSendType',
                'arguments' => 'router',
            ],
            'autoborna.form.type.notification_list' => [
                'class' => 'Autoborna\NotificationBundle\Form\Type\NotificationListType',
            ],
            'autoborna.form.type.mobilenotificationsend_list' => [
                'class'     => \Autoborna\NotificationBundle\Form\Type\MobileNotificationSendType::class,
                'arguments' => 'router',
            ],
            'autoborna.form.type.mobilenotification_list' => [
                'class' => \Autoborna\NotificationBundle\Form\Type\MobileNotificationListType::class,
            ],
        ],
        'helpers' => [
            'autoborna.helper.notification' => [
                'class'     => 'Autoborna\NotificationBundle\Helper\NotificationHelper',
                'alias'     => 'notification_helper',
                'arguments' => [
                    'doctrine.orm.entity_manager',
                    'templating.helper.assets',
                    'autoborna.helper.core_parameters',
                    'autoborna.helper.integration',
                    'router',
                    'request_stack',
                    'autoborna.lead.model.dnc',
                ],
            ],
        ],
        'other' => [
            'autoborna.notification.api' => [
                'class'     => \Autoborna\NotificationBundle\Api\OneSignalApi::class,
                'arguments' => [
                    'autoborna.http.client',
                    'autoborna.page.model.trackable',
                    'autoborna.helper.integration',
                ],
                'alias' => 'notification_api',
            ],
        ],
        'models' => [
            'autoborna.notification.model.notification' => [
                'class'     => 'Autoborna\NotificationBundle\Model\NotificationModel',
                'arguments' => [
                    'autoborna.page.model.trackable',
                ],
            ],
        ],
        'repositories' => [
            'autoborna.notification.repository.stat' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Autoborna\NotificationBundle\Entity\Stat::class,
                ],
            ],
        ],
        'integrations' => [
            'autoborna.integration.onesignal' => [
                'class'     => \Autoborna\NotificationBundle\Integration\OneSignalIntegration::class,
                'arguments' => [
                    'event_dispatcher',
                    'autoborna.helper.cache_storage',
                    'doctrine.orm.entity_manager',
                    'session',
                    'request_stack',
                    'router',
                    'translator',
                    'logger',
                    'autoborna.helper.encryption',
                    'autoborna.lead.model.lead',
                    'autoborna.lead.model.company',
                    'autoborna.helper.paths',
                    'autoborna.core.model.notification',
                    'autoborna.lead.model.field',
                    'autoborna.plugin.model.integration_entity',
                    'autoborna.lead.model.dnc',
                ],
            ],
        ],
    ],
    'routes' => [
        'main' => [
            'autoborna_notification_index' => [
                'path'       => '/notifications/{page}',
                'controller' => 'AutobornaNotificationBundle:Notification:index',
            ],
            'autoborna_notification_action' => [
                'path'       => '/notifications/{objectAction}/{objectId}',
                'controller' => 'AutobornaNotificationBundle:Notification:execute',
            ],
            'autoborna_notification_contacts' => [
                'path'       => '/notifications/view/{objectId}/contact/{page}',
                'controller' => 'AutobornaNotificationBundle:Notification:contacts',
            ],
            'autoborna_mobile_notification_index' => [
                'path'       => '/mobile_notifications/{page}',
                'controller' => 'AutobornaNotificationBundle:MobileNotification:index',
            ],
            'autoborna_mobile_notification_action' => [
                'path'       => '/mobile_notifications/{objectAction}/{objectId}',
                'controller' => 'AutobornaNotificationBundle:MobileNotification:execute',
            ],
            'autoborna_mobile_notification_contacts' => [
                'path'       => '/mobile_notifications/view/{objectId}/contact/{page}',
                'controller' => 'AutobornaNotificationBundle:MobileNotification:contacts',
            ],
        ],
        'public' => [
            'autoborna_receive_notification' => [
                'path'       => '/notification/receive',
                'controller' => 'AutobornaNotificationBundle:Api\NotificationApi:receive',
            ],
            'autoborna_subscribe_notification' => [
                'path'       => '/notification/subscribe',
                'controller' => 'AutobornaNotificationBundle:Api\NotificationApi:subscribe',
            ],
            'autoborna_notification_popup' => [
                'path'       => '/notification',
                'controller' => 'AutobornaNotificationBundle:Popup:index',
            ],

            // JS / Manifest URL's
            'autoborna_onesignal_worker' => [
                'path'       => '/OneSignalSDKWorker.js',
                'controller' => 'AutobornaNotificationBundle:Js:worker',
            ],
            'autoborna_onesignal_updater' => [
                'path'       => '/OneSignalSDKUpdaterWorker.js',
                'controller' => 'AutobornaNotificationBundle:Js:updater',
            ],
            'autoborna_onesignal_manifest' => [
                'path'       => '/manifest.json',
                'controller' => 'AutobornaNotificationBundle:Js:manifest',
            ],
            'autoborna_app_notification' => [
                'path'       => '/notification/appcallback',
                'controller' => 'AutobornaNotificationBundle:AppCallback:index',
            ],
        ],
        'api' => [
            'autoborna_api_notificationsstandard' => [
                'standard_entity' => true,
                'name'            => 'notifications',
                'path'            => '/notifications',
                'controller'      => 'AutobornaNotificationBundle:Api\NotificationApi',
            ],
        ],
    ],
    'menu' => [
        'main' => [
            'items' => [
                'autoborna.notification.notifications' => [
                    'route'  => 'autoborna_notification_index',
                    'access' => ['notification:notifications:viewown', 'notification:notifications:viewother'],
                    'checks' => [
                        'integration' => [
                            'OneSignal' => [
                                'enabled' => true,
                            ],
                        ],
                    ],
                    'parent'   => 'autoborna.core.channels',
                    'priority' => 80,
                ],
                'autoborna.notification.mobile_notifications' => [
                    'route'  => 'autoborna_mobile_notification_index',
                    'access' => ['notification:mobile_notifications:viewown', 'notification:mobile_notifications:viewother'],
                    'checks' => [
                        'integration' => [
                            'OneSignal' => [
                                'enabled'  => true,
                                'features' => [
                                    'mobile',
                                ],
                            ],
                        ],
                    ],
                    'parent'   => 'autoborna.core.channels',
                    'priority' => 65,
                ],
            ],
        ],
    ],
    //'categories' => [
    //    'notification' => null
    //],
    'parameters' => [
        'notification_enabled'                        => false,
        'notification_landing_page_enabled'           => true,
        'notification_tracking_page_enabled'          => false,
        'notification_app_id'                         => null,
        'notification_rest_api_key'                   => null,
        'notification_safari_web_id'                  => null,
        'gcm_sender_id'                               => '482941778795',
        'notification_subdomain_name'                 => null,
        'welcomenotification_enabled'                 => true,
        'campaign_send_notification_to_author'        => true,
        'campaign_notification_email_addresses'       => null,
        'webhook_send_notification_to_author'         => true,
        'webhook_notification_email_addresses'        => null,
    ],
];
