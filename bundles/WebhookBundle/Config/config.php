<?php

return [
    'routes' => [
        'main' => [
            'autoborna_webhook_index' => [
                'path'       => '/webhooks/{page}',
                'controller' => 'AutobornaWebhookBundle:Webhook:index',
            ],
            'autoborna_webhook_action' => [
                'path'       => '/webhooks/{objectAction}/{objectId}',
                'controller' => 'AutobornaWebhookBundle:Webhook:execute',
            ],
        ],
        'api' => [
            'autoborna_api_webhookstandard' => [
                'standard_entity' => true,
                'name'            => 'hooks',
                'path'            => '/hooks',
                'controller'      => 'AutobornaWebhookBundle:Api\WebhookApi',
            ],
            'autoborna_api_webhookevents' => [
                'path'       => '/hooks/triggers',
                'controller' => 'AutobornaWebhookBundle:Api\WebhookApi:getTriggers',
            ],
        ],
    ],

    'menu' => [
        'admin' => [
            'items' => [
                'autoborna.webhook.webhooks' => [
                    'id'        => 'autoborna_webhook_root',
                    'iconClass' => 'fa-exchange',
                    'access'    => ['webhook:webhooks:viewown', 'webhook:webhooks:viewother'],
                    'route'     => 'autoborna_webhook_index',
                ],
            ],
        ],
    ],

    'services' => [
        'forms' => [
            'autoborna.form.type.webhook' => [
                'class'     => \Autoborna\WebhookBundle\Form\Type\WebhookType::class,
            ],
            'autoborna.form.type.webhookconfig' => [
                'class' => \Autoborna\WebhookBundle\Form\Type\ConfigType::class,
            ],
            'autoborna.campaign.type.action.sendwebhook' => [
                'class'     => \Autoborna\WebhookBundle\Form\Type\CampaignEventSendWebhookType::class,
                'arguments' => [
                    'arguments' => 'translator',
                ],
            ],
            'autoborna.webhook.notificator.webhookkillnotificator' => [
                'class'     => \Autoborna\WebhookBundle\Notificator\WebhookKillNotificator::class,
                'arguments' => [
                    'translator',
                    'router',
                    'autoborna.core.model.notification',
                    'doctrine.orm.entity_manager',
                    'autoborna.helper.mailer',
                    'autoborna.helper.core_parameters',
                ],
            ],
        ],
        'events' => [
            'autoborna.webhook.config.subscriber' => [
                'class' => \Autoborna\WebhookBundle\EventListener\ConfigSubscriber::class,
            ],
            'autoborna.webhook.audit.subscriber' => [
                'class'     => \Autoborna\WebhookBundle\EventListener\WebhookSubscriber::class,
                'arguments' => [
                    'autoborna.helper.ip_lookup',
                    'autoborna.core.model.auditlog',
                    'autoborna.webhook.notificator.webhookkillnotificator',
                ],
            ],
            'autoborna.webhook.stats.subscriber' => [
                'class'     => \Autoborna\WebhookBundle\EventListener\StatsSubscriber::class,
                'arguments' => [
                    'autoborna.security',
                    'doctrine.orm.entity_manager',
                ],
            ],
            'autoborna.webhook.campaign.subscriber' => [
                'class'     => \Autoborna\WebhookBundle\EventListener\CampaignSubscriber::class,
                'arguments' => [
                    'autoborna.webhook.campaign.helper',
                ],
            ],
        ],
        'models' => [
            'autoborna.webhook.model.webhook' => [
                'class'     => \Autoborna\WebhookBundle\Model\WebhookModel::class,
                'arguments' => [
                    'autoborna.helper.core_parameters',
                    'jms_serializer',
                    'autoborna.webhook.http.client',
                    'event_dispatcher',
                ],
            ],
        ],
        'others' => [
            'autoborna.webhook.campaign.helper' => [
                'class'     => \Autoborna\WebhookBundle\Helper\CampaignHelper::class,
                'arguments' => [
                    'autoborna.http.client',
                    'autoborna.lead.model.company',
                    'event_dispatcher',
                ],
            ],
            'autoborna.webhook.http.client' => [
                'class'     => \Autoborna\WebhookBundle\Http\Client::class,
                'arguments' => [
                    'autoborna.helper.core_parameters',
                    'autoborna.guzzle.client',
                ],
            ],
        ],
        'commands' => [
            'autoborna.webhook.command.process.queues' => [
                'class'     => \Autoborna\WebhookBundle\Command\ProcessWebhookQueuesCommand::class,
                'tag'       => 'console.command',
            ],
            'autoborna.webhook.command.delete.logs' => [
                'class'     => \Autoborna\WebhookBundle\Command\DeleteWebhookLogsCommand::class,
                'arguments' => [
                    'autoborna.webhook.model.webhook',
                    'autoborna.helper.core_parameters',
                ],
                'tag' => 'console.command',
            ],
        ],
        'repositories' => [
            'autoborna.webhook.repository.queue' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Autoborna\WebhookBundle\Entity\WebhookQueue::class,
                ],
            ],
        ],
    ],

    'parameters' => [
        'webhook_limit'                        => 10, // How many entities can be sent in one webhook
        'webhook_time_limit'                   => 600, // How long the webhook processing can run in seconds
        'webhook_log_max'                      => 1000, // How many recent logs to keep
        'clean_webhook_logs_in_background'     => false,
        'webhook_disable_limit'                => 100, // How many times the webhook response can fail until the webhook will be unpublished
        'webhook_timeout'                      => 15, // How long the CURL request can wait for response before Autoborna hangs up. In seconds
        'queue_mode'                           => \Autoborna\WebhookBundle\Model\WebhookModel::IMMEDIATE_PROCESS, // Trigger the webhook immediately or queue it for faster response times
        'events_orderby_dir'                   => \Doctrine\Common\Collections\Criteria::ASC, // Order the queued events chronologically or the other way around
        'webhook_email_details'                => true, // If enabled, email related webhooks send detailed data
    ],
];
