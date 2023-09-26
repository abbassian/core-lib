<?php

return [
    'routes' => [
        'main' => [
            'autoborna_pointtriggerevent_action' => [
                'path'       => '/points/triggers/events/{objectAction}/{objectId}',
                'controller' => 'AutobornaPointBundle:TriggerEvent:execute',
            ],
            'autoborna_pointtrigger_index' => [
                'path'       => '/points/triggers/{page}',
                'controller' => 'AutobornaPointBundle:Trigger:index',
            ],
            'autoborna_pointtrigger_action' => [
                'path'       => '/points/triggers/{objectAction}/{objectId}',
                'controller' => 'AutobornaPointBundle:Trigger:execute',
            ],
            'autoborna_point_index' => [
                'path'       => '/points/{page}',
                'controller' => 'AutobornaPointBundle:Point:index',
            ],
            'autoborna_point_action' => [
                'path'       => '/points/{objectAction}/{objectId}',
                'controller' => 'AutobornaPointBundle:Point:execute',
            ],
        ],
        'api' => [
            'autoborna_api_pointactionsstandard' => [
                'standard_entity' => true,
                'name'            => 'points',
                'path'            => '/points',
                'controller'      => 'AutobornaPointBundle:Api\PointApi',
            ],
            'autoborna_api_getpointactiontypes' => [
                'path'       => '/points/actions/types',
                'controller' => 'AutobornaPointBundle:Api\PointApi:getPointActionTypes',
            ],
            'autoborna_api_pointtriggersstandard' => [
                'standard_entity' => true,
                'name'            => 'triggers',
                'path'            => '/points/triggers',
                'controller'      => 'AutobornaPointBundle:Api\TriggerApi',
            ],
            'autoborna_api_getpointtriggereventtypes' => [
                'path'       => '/points/triggers/events/types',
                'controller' => 'AutobornaPointBundle:Api\TriggerApi:getPointTriggerEventTypes',
            ],
            'autoborna_api_pointtriggerdeleteevents' => [
                'path'       => '/points/triggers/{triggerId}/events/delete',
                'controller' => 'AutobornaPointBundle:Api\TriggerApi:deletePointTriggerEvents',
                'method'     => 'DELETE',
            ],
            'autoborna_api_adjustcontactpoints' => [
                'path'       => '/contacts/{leadId}/points/{operator}/{delta}',
                'controller' => 'AutobornaPointBundle:Api\PointApi:adjustPoints',
                'method'     => 'POST',
            ],
        ],
    ],

    'menu' => [
        'main' => [
            'autoborna.points.menu.root' => [
                'id'        => 'autoborna_points_root',
                'iconClass' => 'fa-calculator',
                'access'    => ['point:points:view', 'point:triggers:view'],
                'priority'  => 30,
                'children'  => [
                    'autoborna.point.menu.index' => [
                        'route'  => 'autoborna_point_index',
                        'access' => 'point:points:view',
                    ],
                    'autoborna.point.trigger.menu.index' => [
                        'route'  => 'autoborna_pointtrigger_index',
                        'access' => 'point:triggers:view',
                    ],
                ],
            ],
        ],
    ],

    'categories' => [
        'point' => null,
    ],

    'services' => [
        'events' => [
            'autoborna.point.subscriber' => [
                'class'     => \Autoborna\PointBundle\EventListener\PointSubscriber::class,
                'arguments' => [
                    'autoborna.helper.ip_lookup',
                    'autoborna.core.model.auditlog',
                ],
            ],
            'autoborna.point.leadbundle.subscriber' => [
                'class'     => \Autoborna\PointBundle\EventListener\LeadSubscriber::class,
                'arguments' => [
                    'autoborna.point.model.trigger',
                    'translator',
                    'autoborna.lead.repository.points_change_log',
                    'autoborna.point.repository.lead_point_log',
                    'autoborna.point.repository.lead_trigger_log',
                ],
            ],
            'autoborna.point.search.subscriber' => [
                'class'     => \Autoborna\PointBundle\EventListener\SearchSubscriber::class,
                'arguments' => [
                    'autoborna.point.model.point',
                    'autoborna.point.model.trigger',
                    'autoborna.security',
                    'autoborna.helper.templating',
                ],
            ],
            'autoborna.point.dashboard.subscriber' => [
                'class'     => \Autoborna\PointBundle\EventListener\DashboardSubscriber::class,
                'arguments' => [
                    'autoborna.point.model.point',
                ],
            ],
            'autoborna.point.stats.subscriber' => [
                'class'     => \Autoborna\PointBundle\EventListener\StatsSubscriber::class,
                'arguments' => [
                    'autoborna.security',
                    'doctrine.orm.entity_manager',
                ],
            ],
        ],
        'forms' => [
            'autoborna.point.type.form' => [
                'class'     => \Autoborna\PointBundle\Form\Type\PointType::class,
                'arguments' => ['autoborna.security'],
            ],
            'autoborna.point.type.action' => [
                'class' => \Autoborna\PointBundle\Form\Type\PointActionType::class,
            ],
            'autoborna.pointtrigger.type.form' => [
                'class'     => \Autoborna\PointBundle\Form\Type\TriggerType::class,
                'arguments' => [
                  'autoborna.security',
                ],
            ],
            'autoborna.pointtrigger.type.action' => [
                'class' => \Autoborna\PointBundle\Form\Type\TriggerEventType::class,
            ],
            'autoborna.point.type.genericpoint_settings' => [
                'class' => \Autoborna\PointBundle\Form\Type\GenericPointSettingsType::class,
            ],
        ],
        'models' => [
            'autoborna.point.model.point' => [
                'class'     => \Autoborna\PointBundle\Model\PointModel::class,
                'arguments' => [
                    'session',
                    'autoborna.helper.ip_lookup',
                    'autoborna.lead.model.lead',
                    'autoborna.factory',
                    'autoborna.tracker.contact',
                ],
            ],
            'autoborna.point.model.triggerevent' => [
                'class' => \Autoborna\PointBundle\Model\TriggerEventModel::class,
            ],
            'autoborna.point.model.trigger' => [
                'class'     => \Autoborna\PointBundle\Model\TriggerModel::class,
                'arguments' => [
                    'autoborna.helper.ip_lookup',
                    'autoborna.lead.model.lead',
                    'autoborna.point.model.triggerevent',
                    'autoborna.factory',
                    'autoborna.tracker.contact',
                ],
            ],
        ],
        'repositories' => [
            'autoborna.point.repository.lead_point_log' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Autoborna\PointBundle\Entity\LeadPointLog::class,
                ],
            ],
            'autoborna.point.repository.lead_trigger_log' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Autoborna\PointBundle\Entity\LeadTriggerLog::class,
                ],
            ],
        ],
    ],
];
