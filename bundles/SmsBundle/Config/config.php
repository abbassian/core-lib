<?php

return [
    'services' => [
        'events' => [
            'autoborna.sms.lead.subscriber' => [
                'class'     => \Autoborna\SmsBundle\EventListener\LeadSubscriber::class,
                'arguments' => [
                    'translator',
                    'router',
                    'doctrine.orm.entity_manager',
                ],
            ],
            'autoborna.sms.broadcast.subscriber' => [
                'class'     => \Autoborna\SmsBundle\EventListener\BroadcastSubscriber::class,
                'arguments' => [
                    'autoborna.sms.broadcast.executioner',
                ],
            ],
            'autoborna.sms.campaignbundle.subscriber.send' => [
                'class'     => \Autoborna\SmsBundle\EventListener\CampaignSendSubscriber::class,
                'arguments' => [
                    'autoborna.sms.model.sms',
                    'autoborna.sms.transport_chain',
                ],
                'alias' => 'autoborna.sms.campaignbundle.subscriber',
            ],
            'autoborna.sms.campaignbundle.subscriber.reply' => [
                'class'     => \Autoborna\SmsBundle\EventListener\CampaignReplySubscriber::class,
                'arguments' => [
                    'autoborna.sms.transport_chain',
                    'autoborna.campaign.executioner.realtime',
                ],
            ],
            'autoborna.sms.smsbundle.subscriber' => [
                'class'     => \Autoborna\SmsBundle\EventListener\SmsSubscriber::class,
                'arguments' => [
                    'autoborna.core.model.auditlog',
                    'autoborna.page.model.trackable',
                    'autoborna.page.helper.token',
                    'autoborna.asset.helper.token',
                    'autoborna.helper.sms',
                ],
            ],
            'autoborna.sms.channel.subscriber' => [
                'class'     => \Autoborna\SmsBundle\EventListener\ChannelSubscriber::class,
                'arguments' => [
                    'autoborna.sms.transport_chain',
                ],
            ],
            'autoborna.sms.message_queue.subscriber' => [
                'class'     => \Autoborna\SmsBundle\EventListener\MessageQueueSubscriber::class,
                'arguments' => [
                    'autoborna.sms.model.sms',
                ],
            ],
            'autoborna.sms.stats.subscriber' => [
                'class'     => \Autoborna\SmsBundle\EventListener\StatsSubscriber::class,
                'arguments' => [
                    'autoborna.security',
                    'doctrine.orm.entity_manager',
                ],
            ],
            'autoborna.sms.configbundle.subscriber' => [
                'class' => Autoborna\SmsBundle\EventListener\ConfigSubscriber::class,
            ],
            'autoborna.sms.subscriber.contact_tracker' => [
                'class'     => \Autoborna\SmsBundle\EventListener\TrackingSubscriber::class,
                'arguments' => [
                    'autoborna.sms.repository.stat',
                ],
            ],
            'autoborna.sms.subscriber.stop' => [
                'class'     => \Autoborna\SmsBundle\EventListener\StopSubscriber::class,
                'arguments' => [
                    'autoborna.lead.model.dnc',
                ],
            ],
            'autoborna.sms.subscriber.reply' => [
                'class'     => \Autoborna\SmsBundle\EventListener\ReplySubscriber::class,
                'arguments' => [
                    'translator',
                    'autoborna.lead.repository.lead_event_log',
                ],
            ],
            'autoborna.sms.webhook.subscriber' => [
                'class'     => \Autoborna\SmsBundle\EventListener\WebhookSubscriber::class,
                'arguments' => [
                    'autoborna.webhook.model.webhook',
                ],
            ],
        ],
        'forms' => [
            'autoborna.form.type.sms' => [
                'class'     => \Autoborna\SmsBundle\Form\Type\SmsType::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                ],
            ],
            'autoborna.form.type.smsconfig' => [
                'class' => \Autoborna\SmsBundle\Form\Type\ConfigType::class,
            ],
            'autoborna.form.type.smssend_list' => [
                'class'     => \Autoborna\SmsBundle\Form\Type\SmsSendType::class,
                'arguments' => 'router',
            ],
            'autoborna.form.type.sms_list' => [
                'class' => \Autoborna\SmsBundle\Form\Type\SmsListType::class,
            ],
            'autoborna.form.type.sms.config.form' => [
                'class'     => \Autoborna\SmsBundle\Form\Type\ConfigType::class,
                'arguments' => ['autoborna.sms.transport_chain', 'translator'],
            ],
            'autoborna.form.type.sms.campaign_reply_type' => [
                'class' => \Autoborna\SmsBundle\Form\Type\CampaignReplyType::class,
            ],
        ],
        'helpers' => [
            'autoborna.helper.sms' => [
                'class'     => \Autoborna\SmsBundle\Helper\SmsHelper::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                    'autoborna.lead.model.lead',
                    'autoborna.helper.phone_number',
                    'autoborna.sms.model.sms',
                    'autoborna.helper.integration',
                    'autoborna.lead.model.dnc',
                ],
                'alias' => 'sms_helper',
            ],
        ],
        'other' => [
            'autoborna.sms.transport_chain' => [
                'class'     => \Autoborna\SmsBundle\Sms\TransportChain::class,
                'arguments' => [
                    '%autoborna.sms_transport%',
                    'autoborna.helper.integration',
                    'monolog.logger.autoborna',
                ],
            ],
            'autoborna.sms.callback_handler_container' => [
                'class' => \Autoborna\SmsBundle\Callback\HandlerContainer::class,
            ],
            'autoborna.sms.helper.contact' => [
                'class'     => \Autoborna\SmsBundle\Helper\ContactHelper::class,
                'arguments' => [
                    'autoborna.lead.repository.lead',
                    'doctrine.dbal.default_connection',
                    'autoborna.helper.phone_number',
                ],
            ],
            'autoborna.sms.helper.reply' => [
                'class'     => \Autoborna\SmsBundle\Helper\ReplyHelper::class,
                'arguments' => [
                    'event_dispatcher',
                    'monolog.logger.autoborna',
                    'autoborna.tracker.contact',
                ],
            ],
            'autoborna.sms.twilio.configuration' => [
                'class'        => \Autoborna\SmsBundle\Integration\Twilio\Configuration::class,
                'arguments'    => [
                    'autoborna.helper.integration',
                ],
            ],
            'autoborna.sms.twilio.transport' => [
                'class'        => \Autoborna\SmsBundle\Integration\Twilio\TwilioTransport::class,
                'arguments'    => [
                    'autoborna.sms.twilio.configuration',
                    'monolog.logger.autoborna',
                ],
                'tag'          => 'autoborna.sms_transport',
                'tagArguments' => [
                    'integrationAlias' => 'Twilio',
                ],
                'serviceAliases' => [
                    'sms_api',
                    'autoborna.sms.api',
                ],
            ],
            'autoborna.sms.twilio.callback' => [
                'class'     => \Autoborna\SmsBundle\Integration\Twilio\TwilioCallback::class,
                'arguments' => [
                    'autoborna.sms.helper.contact',
                    'autoborna.sms.twilio.configuration',
                ],
                'tag'   => 'autoborna.sms_callback_handler',
            ],

            // @deprecated - this should not be used; use `autoborna.sms.twilio.transport` instead.
            // Only kept as BC in case someone is passing the service by name in 3rd party
            'autoborna.sms.transport.twilio' => [
                'class'        => \Autoborna\SmsBundle\Api\TwilioApi::class,
                'arguments'    => [
                    'autoborna.sms.twilio.configuration',
                    'monolog.logger.autoborna',
                ],
            ],
            'autoborna.sms.broadcast.executioner' => [
                'class'        => \Autoborna\SmsBundle\Broadcast\BroadcastExecutioner::class,
                'arguments'    => [
                    'autoborna.sms.model.sms',
                    'autoborna.sms.broadcast.query',
                    'translator',
                ],
            ],
            'autoborna.sms.broadcast.query' => [
                'class'        => \Autoborna\SmsBundle\Broadcast\BroadcastQuery::class,
                'arguments'    => [
                    'doctrine.orm.entity_manager',
                    'autoborna.sms.model.sms',
                ],
            ],
        ],
        'models' => [
            'autoborna.sms.model.sms' => [
                'class'     => 'Autoborna\SmsBundle\Model\SmsModel',
                'arguments' => [
                    'autoborna.page.model.trackable',
                    'autoborna.lead.model.lead',
                    'autoborna.channel.model.queue',
                    'autoborna.sms.transport_chain',
                    'autoborna.helper.cache_storage',
                ],
            ],
        ],
        'integrations' => [
            'autoborna.integration.twilio' => [
                'class'     => \Autoborna\SmsBundle\Integration\TwilioIntegration::class,
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
        'repositories' => [
            'autoborna.sms.repository.stat' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Autoborna\SmsBundle\Entity\Stat::class,
                ],
            ],
        ],
        'controllers' => [
            'autoborna.sms.controller.reply' => [
                'class'     => \Autoborna\SmsBundle\Controller\ReplyController::class,
                'arguments' => [
                    'autoborna.sms.callback_handler_container',
                    'autoborna.sms.helper.reply',
                ],
                'methodCalls' => [
                    'setContainer' => [
                        '@service_container',
                    ],
                ],
            ],
        ],
    ],
    'routes' => [
        'main' => [
            'autoborna_sms_index' => [
                'path'       => '/sms/{page}',
                'controller' => 'AutobornaSmsBundle:Sms:index',
            ],
            'autoborna_sms_action' => [
                'path'       => '/sms/{objectAction}/{objectId}',
                'controller' => 'AutobornaSmsBundle:Sms:execute',
            ],
            'autoborna_sms_contacts' => [
                'path'       => '/sms/view/{objectId}/contact/{page}',
                'controller' => 'AutobornaSmsBundle:Sms:contacts',
            ],
        ],
        'public' => [
            'autoborna_sms_callback' => [
                'path'       => '/sms/{transport}/callback',
                'controller' => 'AutobornaSmsBundle:Reply:callback',
            ],
            /* @deprecated as this was Twilio specific */
            'autoborna_receive_sms' => [
                'path'       => '/sms/receive',
                'controller' => 'AutobornaSmsBundle:Reply:callback',
                'defaults'   => [
                    'transport' => 'twilio',
                ],
            ],
        ],
        'api' => [
            'autoborna_api_smsesstandard' => [
                'standard_entity' => true,
                'name'            => 'smses',
                'path'            => '/smses',
                'controller'      => 'AutobornaSmsBundle:Api\SmsApi',
            ],
            'autoborna_api_smses_send' => [
                'path'       => '/smses/{id}/contact/{contactId}/send',
                'controller' => 'AutobornaSmsBundle:Api\SmsApi:send',
            ],
        ],
    ],
    'menu' => [
        'main' => [
            'items' => [
                'autoborna.sms.smses' => [
                    'route'  => 'autoborna_sms_index',
                    'access' => ['sms:smses:viewown', 'sms:smses:viewother'],
                    'parent' => 'autoborna.core.channels',
                    'checks' => [
                        'integration' => [
                            'Twilio' => [
                                'enabled' => true,
                            ],
                        ],
                    ],
                    'priority' => 70,
                ],
            ],
        ],
    ],
    'parameters' => [
        'sms_enabled'              => false,
        'sms_username'             => null,
        'sms_password'             => null,
        'sms_sending_phone_number' => null,
        'sms_frequency_number'     => 0,
        'sms_frequency_time'       => 'DAY',
        'sms_transport'            => 'autoborna.sms.twilio.transport',
    ],
];
