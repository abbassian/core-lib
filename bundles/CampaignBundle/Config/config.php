<?php

return [
    'routes' => [
        'main' => [
            'autoborna_campaignevent_action'  => [
                'path'       => '/campaigns/events/{objectAction}/{objectId}',
                'controller' => 'AutobornaCampaignBundle:Event:execute',
            ],
            'autoborna_campaignsource_action' => [
                'path'       => '/campaigns/sources/{objectAction}/{objectId}',
                'controller' => 'AutobornaCampaignBundle:Source:execute',
            ],
            'autoborna_campaign_index'        => [
                'path'       => '/campaigns/{page}',
                'controller' => 'AutobornaCampaignBundle:Campaign:index',
            ],
            'autoborna_campaign_action'       => [
                'path'       => '/campaigns/{objectAction}/{objectId}',
                'controller' => 'AutobornaCampaignBundle:Campaign:execute',
            ],
            'autoborna_campaign_contacts'     => [
                'path'       => '/campaigns/view/{objectId}/contact/{page}',
                'controller' => 'AutobornaCampaignBundle:Campaign:contacts',
            ],
            'autoborna_campaign_preview'      => [
                'path'       => '/campaign/preview/{objectId}',
                'controller' => 'AutobornaEmailBundle:Public:preview',
            ],
        ],
        'api'  => [
            'autoborna_api_campaignsstandard'            => [
                'standard_entity' => true,
                'name'            => 'campaigns',
                'path'            => '/campaigns',
                'controller'      => 'AutobornaCampaignBundle:Api\CampaignApi',
            ],
            'autoborna_api_campaigneventsstandard'       => [
                'standard_entity'     => true,
                'supported_endpoints' => [
                    'getone',
                    'getall',
                ],
                'name'                => 'events',
                'path'                => '/campaigns/events',
                'controller'          => 'AutobornaCampaignBundle:Api\EventApi',
            ],
            'autoborna_api_campaigns_events_contact'     => [
                'path'       => '/campaigns/events/contact/{contactId}',
                'controller' => 'AutobornaCampaignBundle:Api\EventLogApi:getContactEvents',
                'method'     => 'GET',
            ],
            'autoborna_api_campaigns_edit_contact_event' => [
                'path'       => '/campaigns/events/{eventId}/contact/{contactId}/edit',
                'controller' => 'AutobornaCampaignBundle:Api\EventLogApi:editContactEvent',
                'method'     => 'PUT',
            ],
            'autoborna_api_campaigns_batchedit_events'   => [
                'path'       => '/campaigns/events/batch/edit',
                'controller' => 'AutobornaCampaignBundle:Api\EventLogApi:editEvents',
                'method'     => 'PUT',
            ],
            'autoborna_api_campaign_contact_events'      => [
                'path'       => '/campaigns/{campaignId}/events/contact/{contactId}',
                'controller' => 'AutobornaCampaignBundle:Api\EventLogApi:getContactEvents',
                'method'     => 'GET',
            ],
            'autoborna_api_campaigngetcontacts'          => [
                'path'       => '/campaigns/{id}/contacts',
                'controller' => 'AutobornaCampaignBundle:Api\CampaignApi:getContacts',
            ],
            'autoborna_api_campaignaddcontact'           => [
                'path'       => '/campaigns/{id}/contact/{leadId}/add',
                'controller' => 'AutobornaCampaignBundle:Api\CampaignApi:addLead',
                'method'     => 'POST',
            ],
            'autoborna_api_campaignremovecontact'        => [
                'path'       => '/campaigns/{id}/contact/{leadId}/remove',
                'controller' => 'AutobornaCampaignBundle:Api\CampaignApi:removeLead',
                'method'     => 'POST',
            ],
            'autoborna_api_contact_clone_campaign' => [
                'path'       => '/campaigns/clone/{campaignId}',
                'controller' => 'AutobornaCampaignBundle:Api\CampaignApi:cloneCampaign',
                'method'     => 'POST',
            ],
        ],
    ],

    'menu' => [
        'main' => [
            'autoborna.campaign.menu.index' => [
                'iconClass' => 'fa-clock-o',
                'route'     => 'autoborna_campaign_index',
                'access'    => 'campaign:campaigns:view',
                'priority'  => 50,
            ],
        ],
    ],

    'categories' => [
        'campaign' => null,
    ],

    'services' => [
        'events' => [
            'autoborna.campaign.subscriber'                => [
                'class'     => \Autoborna\CampaignBundle\EventListener\CampaignSubscriber::class,
                'arguments' => [
                    'autoborna.helper.ip_lookup',
                    'autoborna.core.model.auditlog',
                    'autoborna.campaign.service.campaign',
                    'autoborna.core.service.flashbag',
                ],
            ],
            'autoborna.campaign.leadbundle.subscriber'     => [
                'class'     => \Autoborna\CampaignBundle\EventListener\LeadSubscriber::class,
                'arguments' => [
                    'autoborna.campaign.event_collector',
                    'translator',
                    'doctrine.orm.entity_manager',
                    'router',
                ],
            ],
            'autoborna.campaign.calendarbundle.subscriber' => [
                'class'     => \Autoborna\CampaignBundle\EventListener\CalendarSubscriber::class,
                'arguments' => [
                    'doctrine.dbal.default_connection',
                    'translator',
                    'router',
                ],
            ],
            'autoborna.campaign.pointbundle.subscriber'    => [
                'class' => \Autoborna\CampaignBundle\EventListener\PointSubscriber::class,
            ],
            'autoborna.campaign.search.subscriber'         => [
                'class'     => \Autoborna\CampaignBundle\EventListener\SearchSubscriber::class,
                'arguments' => [
                    'autoborna.campaign.model.campaign',
                    'autoborna.security',
                    'autoborna.helper.templating',
                ],
            ],
            'autoborna.campaign.dashboard.subscriber'      => [
                'class'     => \Autoborna\CampaignBundle\EventListener\DashboardSubscriber::class,
                'arguments' => [
                    'autoborna.campaign.model.campaign',
                    'autoborna.campaign.model.event',
                ],
            ],
            'autoborna.campaignconfigbundle.subscriber'    => [
                'class' => \Autoborna\CampaignBundle\EventListener\ConfigSubscriber::class,
            ],
            'autoborna.campaign.stats.subscriber'          => [
                'class'     => \Autoborna\CampaignBundle\EventListener\StatsSubscriber::class,
                'arguments' => [
                    'autoborna.security',
                    'doctrine.orm.entity_manager',
                ],
            ],
            'autoborna.campaign.report.subscriber'         => [
                'class'     => \Autoborna\CampaignBundle\EventListener\ReportSubscriber::class,
                'arguments' => [
                    'autoborna.lead.model.company_report_data',
                ],
            ],
            'autoborna.campaign.action.change_membership.subscriber' => [
                'class'     => \Autoborna\CampaignBundle\EventListener\CampaignActionChangeMembershipSubscriber::class,
                'arguments' => [
                    'autoborna.campaign.membership.manager',
                    'autoborna.campaign.model.campaign',
                ],
            ],
            'autoborna.campaign.action.jump_to_event.subscriber' => [
                'class'     => \Autoborna\CampaignBundle\EventListener\CampaignActionJumpToEventSubscriber::class,
                'arguments' => [
                    'autoborna.campaign.repository.event',
                    'autoborna.campaign.event_executioner',
                    'translator',
                    'autoborna.campaign.repository.lead',
                ],
            ],
        ],
        'forms'        => [
            'autoborna.campaign.type.form'                 => [
                'class'     => 'Autoborna\CampaignBundle\Form\Type\CampaignType',
                'arguments' => [
                    'autoborna.security',
                    'translator',
                ],
            ],
            'autoborna.campaignrange.type.action'          => [
                'class' => 'Autoborna\CampaignBundle\Form\Type\EventType',
            ],
            'autoborna.campaign.type.campaignlist'         => [
                'class'     => 'Autoborna\CampaignBundle\Form\Type\CampaignListType',
                'arguments' => [
                    'autoborna.campaign.model.campaign',
                    'translator',
                    'autoborna.security',
                ],
            ],
            'autoborna.campaign.type.trigger.leadchange'   => [
                'class' => 'Autoborna\CampaignBundle\Form\Type\CampaignEventLeadChangeType',
            ],
            'autoborna.campaign.type.action.addremovelead' => [
                'class' => 'Autoborna\CampaignBundle\Form\Type\CampaignEventAddRemoveLeadType',
            ],
            'autoborna.campaign.type.action.jump_to_event' => [
                'class' => \Autoborna\CampaignBundle\Form\Type\CampaignEventJumpToEventType::class,
            ],
            'autoborna.campaign.type.canvassettings'       => [
                'class' => 'Autoborna\CampaignBundle\Form\Type\EventCanvasSettingsType',
            ],
            'autoborna.campaign.type.leadsource'           => [
                'class'     => 'Autoborna\CampaignBundle\Form\Type\CampaignLeadSourceType',
                'arguments' => 'autoborna.factory',
            ],
            'autoborna.form.type.campaignconfig'           => [
                'class'     => 'Autoborna\CampaignBundle\Form\Type\ConfigType',
                'arguments' => 'translator',
            ],
        ],
        'models' => [
            'autoborna.campaign.model.campaign' => [
                'class'     => \Autoborna\CampaignBundle\Model\CampaignModel::class,
                'arguments' => [
                    'autoborna.lead.model.list',
                    'autoborna.form.model.form',
                    'autoborna.campaign.event_collector',
                    'autoborna.campaign.membership.builder',
                    'autoborna.tracker.contact',
                ],
            ],
            'autoborna.campaign.model.event'     => [
                'class'     => \Autoborna\CampaignBundle\Model\EventModel::class,
                'arguments' => [
                    'autoborna.user.model.user',
                    'autoborna.core.model.notification',
                    'autoborna.campaign.model.campaign',
                    'autoborna.lead.model.lead',
                    'autoborna.helper.ip_lookup',
                    'autoborna.campaign.executioner.realtime',
                    'autoborna.campaign.executioner.kickoff',
                    'autoborna.campaign.executioner.scheduled',
                    'autoborna.campaign.executioner.inactive',
                    'autoborna.campaign.event_executioner',
                    'autoborna.campaign.event_collector',
                    'autoborna.campaign.dispatcher.action',
                    'autoborna.campaign.dispatcher.condition',
                    'autoborna.campaign.dispatcher.decision',
                    'autoborna.campaign.repository.lead_event_log',
                ],
            ],
            'autoborna.campaign.model.event_log' => [
                'class'     => \Autoborna\CampaignBundle\Model\EventLogModel::class,
                'arguments' => [
                    'autoborna.campaign.model.event',
                    'autoborna.campaign.model.campaign',
                    'autoborna.helper.ip_lookup',
                    'autoborna.campaign.scheduler',
                ],
            ],
            'autoborna.campaign.model.summary' => [
                'class'     => \Autoborna\CampaignBundle\Model\SummaryModel::class,
                'arguments' => [
                    'autoborna.campaign.repository.lead_event_log',
                ],
            ],
        ],
        'repositories' => [
            'autoborna.campaign.repository.campaign' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Autoborna\CampaignBundle\Entity\Campaign::class,
                ],
            ],
            'autoborna.campaign.repository.lead' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Autoborna\CampaignBundle\Entity\Lead::class,
                ],
            ],
            'autoborna.campaign.repository.event' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Autoborna\CampaignBundle\Entity\Event::class,
                ],
            ],
            'autoborna.campaign.repository.lead_event_log' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Autoborna\CampaignBundle\Entity\LeadEventLog::class,
                ],
            ],
            'autoborna.campaign.repository.summary' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Autoborna\CampaignBundle\Entity\Summary::class,
                ],
            ],
        ],
        'execution'    => [
            'autoborna.campaign.contact_finder.kickoff'  => [
                'class'     => \Autoborna\CampaignBundle\Executioner\ContactFinder\KickoffContactFinder::class,
                'arguments' => [
                    'autoborna.lead.repository.lead',
                    'autoborna.campaign.repository.campaign',
                    'monolog.logger.autoborna',
                ],
            ],
            'autoborna.campaign.contact_finder.scheduled'  => [
                'class'     => \Autoborna\CampaignBundle\Executioner\ContactFinder\ScheduledContactFinder::class,
                'arguments' => [
                    'autoborna.lead.repository.lead',
                    'monolog.logger.autoborna',
                ],
            ],
            'autoborna.campaign.contact_finder.inactive'     => [
                'class'     => \Autoborna\CampaignBundle\Executioner\ContactFinder\InactiveContactFinder::class,
                'arguments' => [
                    'autoborna.lead.repository.lead',
                    'autoborna.campaign.repository.lead',
                    'monolog.logger.autoborna',
                ],
            ],
            'autoborna.campaign.dispatcher.action'        => [
                'class'     => \Autoborna\CampaignBundle\Executioner\Dispatcher\ActionDispatcher::class,
                'arguments' => [
                    'event_dispatcher',
                    'monolog.logger.autoborna',
                    'autoborna.campaign.scheduler',
                    'autoborna.campaign.helper.notification',
                    'autoborna.campaign.legacy_event_dispatcher',
                ],
            ],
            'autoborna.campaign.dispatcher.condition'        => [
                'class'     => \Autoborna\CampaignBundle\Executioner\Dispatcher\ConditionDispatcher::class,
                'arguments' => [
                    'event_dispatcher',
                ],
            ],
            'autoborna.campaign.dispatcher.decision'        => [
                'class'     => \Autoborna\CampaignBundle\Executioner\Dispatcher\DecisionDispatcher::class,
                'arguments' => [
                    'event_dispatcher',
                    'autoborna.campaign.legacy_event_dispatcher',
                ],
            ],
            'autoborna.campaign.event_logger' => [
                'class'     => \Autoborna\CampaignBundle\Executioner\Logger\EventLogger::class,
                'arguments' => [
                    'autoborna.helper.ip_lookup',
                    'autoborna.tracker.contact',
                    'autoborna.campaign.repository.lead_event_log',
                    'autoborna.campaign.repository.lead',
                    'autoborna.campaign.model.summary',
                ],
            ],
            'autoborna.campaign.event_collector' => [
                'class'     => \Autoborna\CampaignBundle\EventCollector\EventCollector::class,
                'arguments' => [
                    'translator',
                    'event_dispatcher',
                ],
            ],
            'autoborna.campaign.scheduler.datetime'      => [
                'class'     => \Autoborna\CampaignBundle\Executioner\Scheduler\Mode\DateTime::class,
                'arguments' => [
                    'monolog.logger.autoborna',
                ],
            ],
            'autoborna.campaign.scheduler.interval'      => [
                'class'     => \Autoborna\CampaignBundle\Executioner\Scheduler\Mode\Interval::class,
                'arguments' => [
                    'monolog.logger.autoborna',
                    'autoborna.helper.core_parameters',
                ],
            ],
            'autoborna.campaign.scheduler'               => [
                'class'     => \Autoborna\CampaignBundle\Executioner\Scheduler\EventScheduler::class,
                'arguments' => [
                    'monolog.logger.autoborna',
                    'autoborna.campaign.event_logger',
                    'autoborna.campaign.scheduler.interval',
                    'autoborna.campaign.scheduler.datetime',
                    'autoborna.campaign.event_collector',
                    'event_dispatcher',
                    'autoborna.helper.core_parameters',
                ],
            ],
            'autoborna.campaign.executioner.action' => [
                'class'     => \Autoborna\CampaignBundle\Executioner\Event\ActionExecutioner::class,
                'arguments' => [
                    'autoborna.campaign.dispatcher.action',
                    'autoborna.campaign.event_logger',
                ],
            ],
            'autoborna.campaign.executioner.condition' => [
                'class'     => \Autoborna\CampaignBundle\Executioner\Event\ConditionExecutioner::class,
                'arguments' => [
                    'autoborna.campaign.dispatcher.condition',
                ],
            ],
            'autoborna.campaign.executioner.decision' => [
                'class'     => \Autoborna\CampaignBundle\Executioner\Event\DecisionExecutioner::class,
                'arguments' => [
                    'autoborna.campaign.event_logger',
                    'autoborna.campaign.dispatcher.decision',
                ],
            ],
            'autoborna.campaign.event_executioner' => [
                'class'     => \Autoborna\CampaignBundle\Executioner\EventExecutioner::class,
                'arguments' => [
                    'autoborna.campaign.event_collector',
                    'autoborna.campaign.event_logger',
                    'autoborna.campaign.executioner.action',
                    'autoborna.campaign.executioner.condition',
                    'autoborna.campaign.executioner.decision',
                    'monolog.logger.autoborna',
                    'autoborna.campaign.scheduler',
                    'autoborna.campaign.helper.removed_contact_tracker',
                    'autoborna.campaign.repository.lead',
                ],
            ],
            'autoborna.campaign.executioner.kickoff'     => [
                'class'     => \Autoborna\CampaignBundle\Executioner\KickoffExecutioner::class,
                'arguments' => [
                    'monolog.logger.autoborna',
                    'autoborna.campaign.contact_finder.kickoff',
                    'translator',
                    'autoborna.campaign.event_executioner',
                    'autoborna.campaign.scheduler',
                ],
            ],
            'autoborna.campaign.executioner.scheduled'     => [
                'class'     => \Autoborna\CampaignBundle\Executioner\ScheduledExecutioner::class,
                'arguments' => [
                    'autoborna.campaign.repository.lead_event_log',
                    'monolog.logger.autoborna',
                    'translator',
                    'autoborna.campaign.event_executioner',
                    'autoborna.campaign.scheduler',
                    'autoborna.campaign.contact_finder.scheduled',
                ],
            ],
            'autoborna.campaign.executioner.realtime'     => [
                'class'     => \Autoborna\CampaignBundle\Executioner\RealTimeExecutioner::class,
                'arguments' => [
                    'monolog.logger.autoborna',
                    'autoborna.lead.model.lead',
                    'autoborna.campaign.repository.event',
                    'autoborna.campaign.event_executioner',
                    'autoborna.campaign.executioner.decision',
                    'autoborna.campaign.event_collector',
                    'autoborna.campaign.scheduler',
                    'autoborna.tracker.contact',
                    'autoborna.campaign.helper.decision',
                ],
            ],
            'autoborna.campaign.executioner.inactive'     => [
                'class'     => \Autoborna\CampaignBundle\Executioner\InactiveExecutioner::class,
                'arguments' => [
                    'autoborna.campaign.contact_finder.inactive',
                    'monolog.logger.autoborna',
                    'translator',
                    'autoborna.campaign.scheduler',
                    'autoborna.campaign.helper.inactivity',
                    'autoborna.campaign.event_executioner',
                ],
            ],
            'autoborna.campaign.helper.decision' => [
                'class'     => \Autoborna\CampaignBundle\Executioner\Helper\DecisionHelper::class,
                'arguments' => [
                    'autoborna.campaign.repository.lead',
                ],
            ],
            'autoborna.campaign.helper.inactivity' => [
                'class'     => \Autoborna\CampaignBundle\Executioner\Helper\InactiveHelper::class,
                'arguments' => [
                    'autoborna.campaign.scheduler',
                    'autoborna.campaign.contact_finder.inactive',
                    'autoborna.campaign.repository.lead_event_log',
                    'autoborna.campaign.repository.event',
                    'monolog.logger.autoborna',
                    'autoborna.campaign.helper.decision',
                ],
            ],
            'autoborna.campaign.helper.removed_contact_tracker' => [
                'class' => \Autoborna\CampaignBundle\Helper\RemovedContactTracker::class,
            ],
            'autoborna.campaign.helper.notification' => [
                'class'     => \Autoborna\CampaignBundle\Executioner\Helper\NotificationHelper::class,
                'arguments' => [
                    'autoborna.user.model.user',
                    'autoborna.core.model.notification',
                    'translator',
                    'router',
                    'autoborna.helper.core_parameters',
                ],
            ],
            // @deprecated 2.13.0 for BC support; to be removed in 3.0
            'autoborna.campaign.legacy_event_dispatcher' => [
                'class'     => \Autoborna\CampaignBundle\Executioner\Dispatcher\LegacyEventDispatcher::class,
                'arguments' => [
                    'event_dispatcher',
                    'autoborna.campaign.scheduler',
                    'monolog.logger.autoborna',
                    'autoborna.campaign.helper.notification',
                    'autoborna.factory',
                    'autoborna.tracker.contact',
                ],
            ],
        ],
        'membership' => [
            'autoborna.campaign.membership.adder' => [
                'class'     => \Autoborna\CampaignBundle\Membership\Action\Adder::class,
                'arguments' => [
                    'autoborna.campaign.repository.lead',
                    'autoborna.campaign.repository.lead_event_log',
                ],
            ],
            'autoborna.campaign.membership.remover' => [
                'class'     => \Autoborna\CampaignBundle\Membership\Action\Remover::class,
                'arguments' => [
                    'autoborna.campaign.repository.lead',
                    'autoborna.campaign.repository.lead_event_log',
                    'translator',
                    'autoborna.helper.template.date',
                ],
            ],
            'autoborna.campaign.membership.event_dispatcher' => [
                'class'     => \Autoborna\CampaignBundle\Membership\EventDispatcher::class,
                'arguments' => [
                    'event_dispatcher',
                ],
            ],
            'autoborna.campaign.membership.manager' => [
                'class'     => \Autoborna\CampaignBundle\Membership\MembershipManager::class,
                'arguments' => [
                    'autoborna.campaign.membership.adder',
                    'autoborna.campaign.membership.remover',
                    'autoborna.campaign.membership.event_dispatcher',
                    'autoborna.campaign.repository.lead',
                    'monolog.logger.autoborna',
                ],
            ],
            'autoborna.campaign.membership.builder' => [
                'class'     => \Autoborna\CampaignBundle\Membership\MembershipBuilder::class,
                'arguments' => [
                    'autoborna.campaign.membership.manager',
                    'autoborna.campaign.repository.lead',
                    'autoborna.lead.repository.lead',
                    'translator',
                ],
            ],
        ],
        'commands' => [
            'autoborna.campaign.command.trigger' => [
                'class'     => \Autoborna\CampaignBundle\Command\TriggerCampaignCommand::class,
                'arguments' => [
                    'autoborna.campaign.repository.campaign',
                    'event_dispatcher',
                    'translator',
                    'autoborna.campaign.executioner.kickoff',
                    'autoborna.campaign.executioner.scheduled',
                    'autoborna.campaign.executioner.inactive',
                    'monolog.logger.autoborna',
                    'autoborna.helper.template.formatter',
                    'autoborna.lead.model.list',
                    'autoborna.helper.segment.count.cache',
                ],
                'tag' => 'console.command',
            ],
            'autoborna.campaign.command.execute' => [
                'class'     => \Autoborna\CampaignBundle\Command\ExecuteEventCommand::class,
                'arguments' => [
                    'autoborna.campaign.executioner.scheduled',
                    'translator',
                    'autoborna.helper.template.formatter',
                ],
                'tag' => 'console.command',
            ],
            'autoborna.campaign.command.validate' => [
                'class'     => \Autoborna\CampaignBundle\Command\ValidateEventCommand::class,
                'arguments' => [
                    'autoborna.campaign.executioner.inactive',
                    'translator',
                    'autoborna.helper.template.formatter',
                ],
                'tag' => 'console.command',
            ],
            'autoborna.campaign.command.update' => [
                'class'     => \Autoborna\CampaignBundle\Command\UpdateLeadCampaignsCommand::class,
                'arguments' => [
                    'autoborna.campaign.repository.campaign',
                    'translator',
                    'autoborna.campaign.membership.builder',
                    'monolog.logger.autoborna',
                    'autoborna.helper.template.formatter',
                ],
                'tag' => 'console.command',
            ],
            'autoborna.campaign.command.summarize' => [
                'class'     => \Autoborna\CampaignBundle\Command\SummarizeCommand::class,
                'arguments' => [
                    'translator',
                    'autoborna.campaign.model.summary',
                ],
                'tag' => 'console.command',
            ],
        ],
        'services' => [
            'autoborna.campaign.service.campaign'=> [
                'class'     => \Autoborna\CampaignBundle\Service\Campaign::class,
                'arguments' => [
                    'autoborna.campaign.repository.campaign',
                    'autoborna.email.repository.email',
                ],
            ],
        ],
        'fixtures' => [
            'autoborna.campaign.fixture.campaign' => [
                'class'    => \Autoborna\CampaignBundle\DataFixtures\ORM\CampaignData::class,
                'tag'      => \Doctrine\Bundle\FixturesBundle\DependencyInjection\CompilerPass\FixturesCompilerPass::FIXTURE_TAG,
                'optional' => true,
            ],
        ],
    ],
    'parameters' => [
        'campaign_time_wait_on_event_false' => 'PT1H',
        'campaign_use_summary'              => 0,
        'campaign_by_range'                 => 0,
    ],
];
