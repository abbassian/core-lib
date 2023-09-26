<?php

return [
    'menu' => [
        'main' => [
            'items' => [
                'autoborna.dynamicContent.dynamicContent' => [
                    'route'    => 'autoborna_dynamicContent_index',
                    'access'   => ['dynamiccontent:dynamiccontents:viewown', 'dynamiccontent:dynamiccontents:viewother'],
                    'parent'   => 'autoborna.core.components',
                    'priority' => 90,
                ],
            ],
        ],
    ],
    'routes' => [
        'main' => [
            'autoborna_dynamicContent_index' => [
                'path'       => '/dwc/{page}',
                'controller' => 'AutobornaDynamicContentBundle:DynamicContent:index',
            ],
            'autoborna_dynamicContent_action' => [
                'path'       => '/dwc/{objectAction}/{objectId}',
                'controller' => 'AutobornaDynamicContentBundle:DynamicContent:execute',
            ],
        ],
        'public' => [
            'autoborna_api_dynamicContent_index' => [
                'path'       => '/dwc',
                'controller' => 'AutobornaDynamicContentBundle:DynamicContentApi:getEntities',
            ],
            'autoborna_api_dynamicContent_action' => [
                'path'       => '/dwc/{objectAlias}',
                'controller' => 'AutobornaDynamicContentBundle:DynamicContentApi:process',
            ],
        ],
        'api' => [
            'autoborna_api_dynamicContent_standard' => [
                'standard_entity' => true,
                'name'            => 'dynamicContents',
                'path'            => '/dynamiccontents',
                'controller'      => 'AutobornaDynamicContentBundle:Api\DynamicContentApi',
            ],
        ],
    ],
    'services' => [
        'events' => [
            'autoborna.dynamicContent.campaignbundle.subscriber' => [
                'class'     => \Autoborna\DynamicContentBundle\EventListener\CampaignSubscriber::class,
                'arguments' => [
                    'autoborna.dynamicContent.model.dynamicContent',
                    'session',
                    'event_dispatcher',
                ],
            ],
            'autoborna.dynamicContent.js.subscriber' => [
                'class'     => \Autoborna\DynamicContentBundle\EventListener\BuildJsSubscriber::class,
                'arguments' => [
                    'templating.helper.assets',
                    'translator',
                    'request_stack',
                    'router',
                ],
            ],
            'autoborna.dynamicContent.subscriber' => [
                'class'     => \Autoborna\DynamicContentBundle\EventListener\DynamicContentSubscriber::class,
                'arguments' => [
                    'autoborna.page.model.trackable',
                    'autoborna.page.helper.token',
                    'autoborna.asset.helper.token',
                    'autoborna.form.helper.token',
                    'autoborna.focus.helper.token',
                    'autoborna.core.model.auditlog',
                    'autoborna.helper.dynamicContent',
                    'autoborna.dynamicContent.model.dynamicContent',
                    'autoborna.security',
                    'autoborna.tracker.contact',
                ],
            ],
            'autoborna.dynamicContent.subscriber.channel' => [
                'class' => \Autoborna\DynamicContentBundle\EventListener\ChannelSubscriber::class,
            ],
            'autoborna.dynamicContent.stats.subscriber' => [
                'class'     => \Autoborna\DynamicContentBundle\EventListener\StatsSubscriber::class,
                'arguments' => [
                    'autoborna.security',
                    'doctrine.orm.entity_manager',
                ],
            ],
            'autoborna.dynamicContent.lead.subscriber' => [
                'class'     => \Autoborna\DynamicContentBundle\EventListener\LeadSubscriber::class,
                'arguments' => [
                    'translator',
                    'router',
                    'autoborna.dynamicContent.repository.stat',
                ],
            ],
        ],
        'forms' => [
            'autoborna.form.type.dwc' => [
                'class'     => 'Autoborna\DynamicContentBundle\Form\Type\DynamicContentType',
                'arguments' => [
                    'doctrine.orm.entity_manager',
                    'autoborna.lead.model.list',
                    'translator',
                    'autoborna.lead.model.lead',
                ],
            ],
            'autoborna.form.type.dwc_entry_filters' => [
                'class'     => 'Autoborna\DynamicContentBundle\Form\Type\DwcEntryFiltersType',
                'arguments' => [
                    'translator',
                ],
                'methodCalls' => [
                    'setConnection' => [
                        'database_connection',
                    ],
                ],
            ],
            'autoborna.form.type.dwcsend_list' => [
                'class'     => 'Autoborna\DynamicContentBundle\Form\Type\DynamicContentSendType',
                'arguments' => [
                    'router',
                ],
            ],
            'autoborna.form.type.dwcdecision_list' => [
                'class'     => 'Autoborna\DynamicContentBundle\Form\Type\DynamicContentDecisionType',
                'arguments' => [
                    'router',
                ],
            ],
            'autoborna.form.type.dwc_list' => [
                'class' => 'Autoborna\DynamicContentBundle\Form\Type\DynamicContentListType',
            ],
        ],
        'models' => [
            'autoborna.dynamicContent.model.dynamicContent' => [
                'class'     => 'Autoborna\DynamicContentBundle\Model\DynamicContentModel',
                'arguments' => [
                ],
            ],
        ],
        'repositories' => [
            'autoborna.dynamicContent.repository.stat' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => \Autoborna\DynamicContentBundle\Entity\Stat::class,
            ],
        ],
        'other' => [
            'autoborna.helper.dynamicContent' => [
                'class'     => \Autoborna\DynamicContentBundle\Helper\DynamicContentHelper::class,
                'arguments' => [
                    'autoborna.dynamicContent.model.dynamicContent',
                    'autoborna.campaign.executioner.realtime',
                    'event_dispatcher',
                    'autoborna.lead.model.lead',
                ],
            ],
        ],
    ],
];
