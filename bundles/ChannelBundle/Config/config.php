<?php

return [
    'routes' => [
        'main' => [
            'autoborna_message_index' => [
                'path'       => '/messages/{page}',
                'controller' => 'AutobornaChannelBundle:Message:index',
            ],
            'autoborna_message_contacts' => [
                'path'       => '/messages/contacts/{objectId}/{channel}/{page}',
                'controller' => 'AutobornaChannelBundle:Message:contacts',
            ],
            'autoborna_message_action' => [
                'path'       => '/messages/{objectAction}/{objectId}',
                'controller' => 'AutobornaChannelBundle:Message:execute',
            ],
            'autoborna_channel_batch_contact_set' => [
                'path'       => '/channels/batch/contact/set',
                'controller' => 'AutobornaChannelBundle:BatchContact:set',
            ],
            'autoborna_channel_batch_contact_view' => [
                'path'       => '/channels/batch/contact/view',
                'controller' => 'AutobornaChannelBundle:BatchContact:index',
            ],
        ],
        'api' => [
            'autoborna_api_messagetandard' => [
                'standard_entity' => true,
                'name'            => 'messages',
                'path'            => '/messages',
                'controller'      => 'AutobornaChannelBundle:Api\MessageApi',
            ],
        ],
        'public' => [
        ],
    ],

    'menu' => [
        'main' => [
            'autoborna.channel.messages' => [
                'route'    => 'autoborna_message_index',
                'access'   => ['channel:messages:viewown', 'channel:messages:viewother'],
                'parent'   => 'autoborna.core.channels',
                'priority' => 110,
            ],
        ],
        'admin' => [
        ],
        'profile' => [
        ],
        'extra' => [
        ],
    ],

    'categories' => [
        'messages' => null,
    ],

    'services' => [
        'events' => [
            'autoborna.channel.campaignbundle.subscriber' => [
                'class'     => Autoborna\ChannelBundle\EventListener\CampaignSubscriber::class,
                'arguments' => [
                    'autoborna.channel.model.message',
                    'autoborna.campaign.dispatcher.action',
                    'autoborna.campaign.event_collector',
                    'monolog.logger.autoborna',
                    'translator',
                ],
            ],
            'autoborna.channel.channelbundle.subscriber' => [
                'class'     => \Autoborna\ChannelBundle\EventListener\MessageSubscriber::class,
                'arguments' => [
                    'autoborna.core.model.auditlog',
                ],
            ],
            'autoborna.channel.channelbundle.lead.subscriber' => [
                'class'     => Autoborna\ChannelBundle\EventListener\LeadSubscriber::class,
                'arguments' => [
                    'translator',
                    'router',
                    'autoborna.channel.repository.message_queue',
                ],
            ],
            'autoborna.channel.reportbundle.subscriber' => [
                'class'     => Autoborna\ChannelBundle\EventListener\ReportSubscriber::class,
                'arguments' => [
                    'autoborna.lead.model.company_report_data',
                    'router',
                ],
            ],
            'autoborna.channel.button.subscriber' => [
                'class'     => \Autoborna\ChannelBundle\EventListener\ButtonSubscriber::class,
                'arguments' => [
                    'router',
                    'translator',
                ],
            ],
        ],
        'forms' => [
            \Autoborna\ChannelBundle\Form\Type\MessageType::class => [
                'class'       => \Autoborna\ChannelBundle\Form\Type\MessageType::class,
                'methodCalls' => [
                    'setSecurity' => ['autoborna.security'],
                ],
                'arguments' => [
                    'autoborna.channel.model.message',
                ],
            ],
            'autoborna.form.type.message_list' => [
                'class' => \Autoborna\ChannelBundle\Form\Type\MessageListType::class,
            ],
            'autoborna.form.type.message_send' => [
                'class'     => \Autoborna\ChannelBundle\Form\Type\MessageSendType::class,
                'arguments' => ['router', 'autoborna.channel.model.message'],
            ],
        ],
        'helpers' => [
            'autoborna.channel.helper.channel_list' => [
                'class'     => \Autoborna\ChannelBundle\Helper\ChannelListHelper::class,
                'arguments' => [
                    'event_dispatcher',
                    'translator',
                ],
                'alias' => 'channel',
            ],
        ],
        'models' => [
            'autoborna.channel.model.message' => [
                'class'     => \Autoborna\ChannelBundle\Model\MessageModel::class,
                'arguments' => [
                    'autoborna.channel.helper.channel_list',
                    'autoborna.campaign.model.campaign',
                ],
            ],
            'autoborna.channel.model.queue' => [
                'class'     => 'Autoborna\ChannelBundle\Model\MessageQueueModel',
                'arguments' => [
                    'autoborna.lead.model.lead',
                    'autoborna.lead.model.company',
                    'autoborna.helper.core_parameters',
                ],
            ],
            'autoborna.channel.model.channel.action' => [
                'class'     => \Autoborna\ChannelBundle\Model\ChannelActionModel::class,
                'arguments' => [
                    'autoborna.lead.model.lead',
                    'autoborna.lead.model.dnc',
                    'translator',
                ],
            ],
            'autoborna.channel.model.frequency.action' => [
                'class'     => \Autoborna\ChannelBundle\Model\FrequencyActionModel::class,
                'arguments' => [
                    'autoborna.lead.model.lead',
                    'autoborna.lead.repository.frequency_rule',
                ],
            ],
        ],
        'repositories' => [
            'autoborna.channel.repository.message_queue' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => \Autoborna\ChannelBundle\Entity\MessageQueue::class,
            ],
        ],
    ],

    'parameters' => [
    ],
];
