<?php

return [
    'routes' => [
        'main' => [
            'autoborna_stage_index' => [
                'path'       => '/stages/{page}',
                'controller' => 'AutobornaStageBundle:Stage:index',
            ],
            'autoborna_stage_action' => [
                'path'       => '/stages/{objectAction}/{objectId}',
                'controller' => 'AutobornaStageBundle:Stage:execute',
            ],
        ],
        'api' => [
            'autoborna_api_stagesstandard' => [
                'standard_entity' => true,
                'name'            => 'stages',
                'path'            => '/stages',
                'controller'      => 'AutobornaStageBundle:Api\StageApi',
            ],
            'autoborna_api_stageddcontact' => [
                'path'       => '/stages/{id}/contact/{contactId}/add',
                'controller' => 'AutobornaStageBundle:Api\StageApi:addContact',
                'method'     => 'POST',
            ],
            'autoborna_api_stageremovecontact' => [
                'path'       => '/stages/{id}/contact/{contactId}/remove',
                'controller' => 'AutobornaStageBundle:Api\StageApi:removeContact',
                'method'     => 'POST',
            ],
        ],
    ],

    'menu' => [
        'main' => [
            'autoborna.stages.menu.index' => [
                'route'     => 'autoborna_stage_index',
                'iconClass' => 'fa-tachometer',
                'access'    => ['stage:stages:view'],
                'priority'  => 25,
            ],
        ],
    ],

    'categories' => [
        'stage' => null,
    ],

    'services' => [
        'events' => [
            'autoborna.stage.campaignbundle.subscriber' => [
                'class'     => \Autoborna\StageBundle\EventListener\CampaignSubscriber::class,
                'arguments' => [
                    'autoborna.lead.model.lead',
                    'autoborna.stage.model.stage',
                    'translator',
                ],
            ],
            'autoborna.stage.subscriber' => [
                'class'     => \Autoborna\StageBundle\EventListener\StageSubscriber::class,
                'arguments' => [
                    'autoborna.helper.ip_lookup',
                    'autoborna.core.model.auditlog',
                ],
            ],
            'autoborna.stage.leadbundle.subscriber' => [
                'class'     => \Autoborna\StageBundle\EventListener\LeadSubscriber::class,
                'arguments' => [
                    'autoborna.lead.repository.stages_lead_log',
                    'autoborna.stage.repository.lead_stage_log',
                    'translator',
                    'router',
                ],
            ],
            'autoborna.stage.search.subscriber' => [
                'class'     => \Autoborna\StageBundle\EventListener\SearchSubscriber::class,
                'arguments' => [
                    'autoborna.stage.model.stage',
                    'autoborna.security',
                    'autoborna.helper.templating',
                ],
            ],
            'autoborna.stage.dashboard.subscriber' => [
                'class'     => \Autoborna\StageBundle\EventListener\DashboardSubscriber::class,
                'arguments' => [
                    'autoborna.stage.model.stage',
                ],
            ],
            'autoborna.stage.stats.subscriber' => [
                'class'     => \Autoborna\StageBundle\EventListener\StatsSubscriber::class,
                'arguments' => [
                    'autoborna.security',
                    'doctrine.orm.entity_manager',
                ],
            ],
        ],
        'forms' => [
            'autoborna.stage.type.form' => [
                'class'     => \Autoborna\StageBundle\Form\Type\StageType::class,
                'arguments' => [
                    'autoborna.security',
                ],
            ],
            'autoborna.stage.type.action' => [
                'class' => 'Autoborna\StageBundle\Form\Type\StageActionType',
            ],
            'autoborna.stage.type.action_list' => [
                'class'     => 'Autoborna\StageBundle\Form\Type\StageActionListType',
                'arguments' => [
                    'autoborna.stage.model.stage',
                ],
            ],
            'autoborna.stage.type.action_change' => [
                'class' => 'Autoborna\StageBundle\Form\Type\StageActionChangeType',
            ],
            'autoborna.stage.type.stage_list' => [
                'class'     => 'Autoborna\StageBundle\Form\Type\StageListType',
                'arguments' => [
                    'autoborna.stage.model.stage',
                ],
            ],
            'autoborna.point.type.genericstage_settings' => [
                'class' => 'Autoborna\StageBundle\Form\Type\GenericStageSettingsType',
            ],
        ],
        'models' => [
            'autoborna.stage.model.stage' => [
                'class'     => 'Autoborna\StageBundle\Model\StageModel',
                'arguments' => [
                    'autoborna.lead.model.lead',
                    'session',
                    'autoborna.helper.user',
                ],
            ],
        ],
        'repositories' => [
            'autoborna.stage.repository.lead_stage_log' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Autoborna\StageBundle\Entity\LeadStageLog::class,
                ],
            ],
        ],
    ],
];
