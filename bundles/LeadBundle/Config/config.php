<?php

return [
    'routes' => [
        'main' => [
            'autoborna_plugin_timeline_index' => [
                'path'         => '/plugin/{integration}/timeline/{page}',
                'controller'   => 'AutobornaLeadBundle:Timeline:pluginIndex',
                'requirements' => [
                    'integration' => '.+',
                ],
            ],
            'autoborna_plugin_timeline_view' => [
                'path'         => '/plugin/{integration}/timeline/view/{leadId}/{page}',
                'controller'   => 'AutobornaLeadBundle:Timeline:pluginView',
                'requirements' => [
                    'integration' => '.+',
                    'leadId'      => '\d+',
                ],
            ],
            'autoborna_segment_batch_contact_set' => [
                'path'       => '/segments/batch/contact/set',
                'controller' => 'AutobornaLeadBundle:BatchSegment:set',
            ],
            'autoborna_segment_batch_contact_view' => [
                'path'       => '/segments/batch/contact/view',
                'controller' => 'AutobornaLeadBundle:BatchSegment:index',
            ],
            'autoborna_segment_index' => [
                'path'       => '/segments/{page}',
                'controller' => 'AutobornaLeadBundle:List:index',
            ],
            'autoborna_segment_action' => [
                'path'       => '/segments/{objectAction}/{objectId}',
                'controller' => 'AutobornaLeadBundle:List:execute',
            ],
            'autoborna_contactfield_index' => [
                'path'       => '/contacts/fields/{page}',
                'controller' => 'AutobornaLeadBundle:Field:index',
            ],
            'autoborna_contactfield_action' => [
                'path'       => '/contacts/fields/{objectAction}/{objectId}',
                'controller' => 'AutobornaLeadBundle:Field:execute',
            ],
            'autoborna_contact_index' => [
                'path'       => '/contacts/{page}',
                'controller' => 'AutobornaLeadBundle:Lead:index',
            ],
            'autoborna_contactnote_index' => [
                'path'       => '/contacts/notes/{leadId}/{page}',
                'controller' => 'AutobornaLeadBundle:Note:index',
                'defaults'   => [
                    'leadId' => 0,
                ],
                'requirements' => [
                    'leadId' => '\d+',
                ],
            ],
            'autoborna_contactnote_action' => [
                'path'         => '/contacts/notes/{leadId}/{objectAction}/{objectId}',
                'controller'   => 'AutobornaLeadBundle:Note:executeNote',
                'requirements' => [
                    'leadId' => '\d+',
                ],
            ],
            'autoborna_contacttimeline_action' => [
                'path'         => '/contacts/timeline/{leadId}/{page}',
                'controller'   => 'AutobornaLeadBundle:Timeline:index',
                'requirements' => [
                    'leadId' => '\d+',
                ],
            ],
            'autoborna_contact_timeline_export_action' => [
                'path'         => '/contacts/timeline/batchExport/{leadId}',
                'controller'   => 'AutobornaLeadBundle:Timeline:batchExport',
                'requirements' => [
                    'leadId' => '\d+',
                ],
            ],
            'autoborna_contact_auditlog_action' => [
                'path'         => '/contacts/auditlog/{leadId}/{page}',
                'controller'   => 'AutobornaLeadBundle:Auditlog:index',
                'requirements' => [
                    'leadId' => '\d+',
                ],
            ],
            'autoborna_contact_auditlog_export_action' => [
                'path'         => '/contacts/auditlog/batchExport/{leadId}',
                'controller'   => 'AutobornaLeadBundle:Auditlog:batchExport',
                'requirements' => [
                    'leadId' => '\d+',
                ],
            ],
            'autoborna_contact_export_action' => [
                'path'         => '/contacts/contact/export/{contactId}',
                'controller'   => 'AutobornaLeadBundle:Lead:contactExport',
                'requirements' => [
                    'contactId' => '\d+',
                ],
            ],
            'autoborna_import_index' => [
                'path'       => '/{object}/import/{page}',
                'controller' => 'AutobornaLeadBundle:Import:index',
            ],
            'autoborna_import_action' => [
                'path'       => '/{object}/import/{objectAction}/{objectId}',
                'controller' => 'AutobornaLeadBundle:Import:execute',
            ],
            'autoborna_contact_action' => [
                'path'       => '/contacts/{objectAction}/{objectId}',
                'controller' => 'AutobornaLeadBundle:Lead:execute',
            ],
            'autoborna_company_index' => [
                'path'       => '/companies/{page}',
                'controller' => 'AutobornaLeadBundle:Company:index',
            ],
            'autoborna_company_contacts_list' => [
                'path'         => '/company/{objectId}/contacts/{page}',
                'controller'   => 'AutobornaLeadBundle:Company:contactsList',
                'requirements' => [
                    'objectId' => '\d+',
                ],
            ],
            'autoborna_company_action' => [
                'path'       => '/companies/{objectAction}/{objectId}',
                'controller' => 'AutobornaLeadBundle:Company:execute',
            ],
            'autoborna_company_export_action' => [
                'path'         => '/companies/company/export/{companyId}',
                'controller'   => 'AutobornaLeadBundle:Company:companyExport',
                'requirements' => [
                    'companyId' => '\d+',
                ],
            ],
            'autoborna_segment_contacts' => [
                'path'       => '/segment/view/{objectId}/contact/{page}',
                'controller' => 'AutobornaLeadBundle:List:contacts',
            ],
        ],
        'api' => [
            'autoborna_api_contactsstandard' => [
                'standard_entity' => true,
                'name'            => 'contacts',
                'path'            => '/contacts',
                'controller'      => 'AutobornaLeadBundle:Api\LeadApi',
            ],
            'autoborna_api_dncaddcontact' => [
                'path'       => '/contacts/{id}/dnc/{channel}/add',
                'controller' => 'AutobornaLeadBundle:Api\LeadApi:addDnc',
                'method'     => 'POST',
                'defaults'   => [
                    'channel' => 'email',
                ],
            ],
            'autoborna_api_dncremovecontact' => [
                'path'       => '/contacts/{id}/dnc/{channel}/remove',
                'controller' => 'AutobornaLeadBundle:Api\LeadApi:removeDnc',
                'method'     => 'POST',
            ],
            'autoborna_api_getcontactevents' => [
                'path'       => '/contacts/{id}/activity',
                'controller' => 'AutobornaLeadBundle:Api\LeadApi:getActivity',
            ],
            'autoborna_api_getcontactsevents' => [
                'path'       => '/contacts/activity',
                'controller' => 'AutobornaLeadBundle:Api\LeadApi:getAllActivity',
            ],
            'autoborna_api_getcontactnotes' => [
                'path'       => '/contacts/{id}/notes',
                'controller' => 'AutobornaLeadBundle:Api\LeadApi:getNotes',
            ],
            'autoborna_api_getcontactdevices' => [
                'path'       => '/contacts/{id}/devices',
                'controller' => 'AutobornaLeadBundle:Api\LeadApi:getDevices',
            ],
            'autoborna_api_getcontactcampaigns' => [
                'path'       => '/contacts/{id}/campaigns',
                'controller' => 'AutobornaLeadBundle:Api\LeadApi:getCampaigns',
            ],
            'autoborna_api_getcontactssegments' => [
                'path'       => '/contacts/{id}/segments',
                'controller' => 'AutobornaLeadBundle:Api\LeadApi:getLists',
            ],
            'autoborna_api_getcontactscompanies' => [
                'path'       => '/contacts/{id}/companies',
                'controller' => 'AutobornaLeadBundle:Api\LeadApi:getCompanies',
            ],
            'autoborna_api_utmcreateevent' => [
                'path'       => '/contacts/{id}/utm/add',
                'controller' => 'AutobornaLeadBundle:Api\LeadApi:addUtmTags',
                'method'     => 'POST',
            ],
            'autoborna_api_utmremoveevent' => [
                'path'       => '/contacts/{id}/utm/{utmid}/remove',
                'controller' => 'AutobornaLeadBundle:Api\LeadApi:removeUtmTags',
                'method'     => 'POST',
            ],
            'autoborna_api_getcontactowners' => [
                'path'       => '/contacts/list/owners',
                'controller' => 'AutobornaLeadBundle:Api\LeadApi:getOwners',
            ],
            'autoborna_api_getcontactfields' => [
                'path'       => '/contacts/list/fields',
                'controller' => 'AutobornaLeadBundle:Api\LeadApi:getFields',
            ],
            'autoborna_api_getcontactsegments' => [
                'path'       => '/contacts/list/segments',
                'controller' => 'AutobornaLeadBundle:Api\ListApi:getLists',
            ],
            'autoborna_api_segmentsstandard' => [
                'standard_entity' => true,
                'name'            => 'lists',
                'path'            => '/segments',
                'controller'      => 'AutobornaLeadBundle:Api\ListApi',
            ],
            'autoborna_api_segmentaddcontact' => [
                'path'       => '/segments/{id}/contact/{leadId}/add',
                'controller' => 'AutobornaLeadBundle:Api\ListApi:addLead',
                'method'     => 'POST',
            ],
            'autoborna_api_segmentaddcontacts' => [
                'path'       => '/segments/{id}/contacts/add',
                'controller' => 'AutobornaLeadBundle:Api\ListApi:addLeads',
                'method'     => 'POST',
            ],
            'autoborna_api_segmentremovecontact' => [
                'path'       => '/segments/{id}/contact/{leadId}/remove',
                'controller' => 'AutobornaLeadBundle:Api\ListApi:removeLead',
                'method'     => 'POST',
            ],
            'autoborna_api_companiesstandard' => [
                'standard_entity' => true,
                'name'            => 'companies',
                'path'            => '/companies',
                'controller'      => 'AutobornaLeadBundle:Api\CompanyApi',
            ],
            'autoborna_api_companyaddcontact' => [
                'path'       => '/companies/{companyId}/contact/{contactId}/add',
                'controller' => 'AutobornaLeadBundle:Api\CompanyApi:addContact',
                'method'     => 'POST',
            ],
            'autoborna_api_companyremovecontact' => [
                'path'       => '/companies/{companyId}/contact/{contactId}/remove',
                'controller' => 'AutobornaLeadBundle:Api\CompanyApi:removeContact',
                'method'     => 'POST',
            ],
            'autoborna_api_fieldsstandard' => [
                'standard_entity' => true,
                'name'            => 'fields',
                'path'            => '/fields/{object}',
                'controller'      => 'AutobornaLeadBundle:Api\FieldApi',
                'defaults'        => [
                    'object' => 'contact',
                ],
            ],
            'autoborna_api_notesstandard' => [
                'standard_entity' => true,
                'name'            => 'notes',
                'path'            => '/notes',
                'controller'      => 'AutobornaLeadBundle:Api\NoteApi',
            ],
            'autoborna_api_devicesstandard' => [
                'standard_entity' => true,
                'name'            => 'devices',
                'path'            => '/devices',
                'controller'      => 'AutobornaLeadBundle:Api\DeviceApi',
            ],
            'autoborna_api_tagsstandard' => [
                'standard_entity' => true,
                'name'            => 'tags',
                'path'            => '/tags',
                'controller'      => 'AutobornaLeadBundle:Api\TagApi',
            ],
        ],
    ],
    'menu' => [
        'main' => [
            'items' => [
                'autoborna.lead.leads' => [
                    'iconClass' => 'fa-user',
                    'access'    => ['lead:leads:viewown', 'lead:leads:viewother'],
                    'route'     => 'autoborna_contact_index',
                    'priority'  => 80,
                ],
                'autoborna.companies.menu.index' => [
                    'route'     => 'autoborna_company_index',
                    'iconClass' => 'fa-building-o',
                    'access'    => ['lead:leads:viewother'],
                    'priority'  => 75,
                ],
                'autoborna.lead.list.menu.index' => [
                    'iconClass' => 'fa-pie-chart',
                    'access'    => ['lead:leads:viewown', 'lead:leads:viewother'],
                    'route'     => 'autoborna_segment_index',
                    'priority'  => 70,
                ],
            ],
        ],
        'admin' => [
            'priority' => 50,
            'items'    => [
                'autoborna.lead.field.menu.index' => [
                    'id'        => 'autoborna_lead_field',
                    'iconClass' => 'fa-list',
                    'route'     => 'autoborna_contactfield_index',
                    'access'    => 'lead:fields:full',
                ],
            ],
        ],
    ],
    'categories' => [
        'segment' => null,
    ],
    'services' => [
        'events' => [
            'autoborna.lead.subscriber' => [
                'class'     => Autoborna\LeadBundle\EventListener\LeadSubscriber::class,
                'arguments' => [
                    'autoborna.helper.ip_lookup',
                    'autoborna.core.model.auditlog',
                    'autoborna.lead.event.dispatcher',
                    'autoborna.helper.template.dnc_reason',
                    'doctrine.orm.entity_manager',
                    'translator',
                    'router',
                ],
                'methodCalls' => [
                    'setModelFactory' => ['autoborna.model.factory'],
                ],
            ],
            'autoborna.lead.subscriber.company' => [
                'class'     => \Autoborna\LeadBundle\EventListener\CompanySubscriber::class,
                'arguments' => [
                    'autoborna.helper.ip_lookup',
                    'autoborna.core.model.auditlog',
                ],
            ],
            'autoborna.lead.emailbundle.subscriber' => [
                'class'     => Autoborna\LeadBundle\EventListener\EmailSubscriber::class,
                'arguments' => [
                    'autoborna.helper.token_builder.factory',
                ],
            ],
            'autoborna.lead.emailbundle.subscriber.owner' => [
                'class'     => \Autoborna\LeadBundle\EventListener\OwnerSubscriber::class,
                'arguments' => [
                    'autoborna.lead.model.lead',
                    'translator',
                ],
            ],
            'autoborna.lead.formbundle.subscriber' => [
                'class'     => Autoborna\LeadBundle\EventListener\FormSubscriber::class,
                'arguments' => [
                    'autoborna.email.model.email',
                    'autoborna.lead.model.lead',
                    'autoborna.tracker.contact',
                    'autoborna.helper.ip_lookup',
                ],
            ],
            'autoborna.lead.formbundle.contact.avatar.subscriber' => [
                'class'     => \Autoborna\LeadBundle\EventListener\SetContactAvatarFormSubscriber::class,
                'arguments' => [
                    'autoborna.helper.template.avatar',
                    'autoborna.form.helper.form_uploader',
                    'autoborna.lead.model.lead',
                ],
            ],
            'autoborna.lead.campaignbundle.subscriber' => [
                'class'     => \Autoborna\LeadBundle\EventListener\CampaignSubscriber::class,
                'arguments' => [
                    'autoborna.helper.ip_lookup',
                    'autoborna.lead.model.lead',
                    'autoborna.lead.model.field',
                    'autoborna.lead.model.list',
                    'autoborna.lead.model.company',
                    'autoborna.campaign.model.campaign',
                    'autoborna.helper.core_parameters',
                ],
            ],
            'autoborna.lead.campaignbundle.action_delete_contacts.subscriber' => [
                'class'     => \Autoborna\LeadBundle\EventListener\CampaignActionDeleteContactSubscriber::class,
                'arguments' => [
                    'autoborna.lead.model.lead',
                    'autoborna.campaign.helper.removed_contact_tracker',
                ],
            ],
            'autoborna.lead.campaignbundle.action_dnc.subscriber' => [
                'class'     => \Autoborna\LeadBundle\EventListener\CampaignActionDNCSubscriber::class,
                'arguments' => [
                   'autoborna.lead.model.dnc',
                   'autoborna.lead.model.lead',
                ],
            ],
            'autoborna.lead.reportbundle.subscriber' => [
                'class'     => \Autoborna\LeadBundle\EventListener\ReportSubscriber::class,
                'arguments' => [
                    'autoborna.lead.model.lead',
                    'autoborna.stage.model.stage',
                    'autoborna.campaign.model.campaign',
                    'autoborna.campaign.event_collector',
                    'autoborna.lead.model.company',
                    'autoborna.lead.model.company_report_data',
                    'autoborna.lead.reportbundle.fields_builder',
                    'translator',
                ],
            ],
            'autoborna.lead.reportbundle.segment_subscriber' => [
                'class'     => \Autoborna\LeadBundle\EventListener\SegmentReportSubscriber::class,
                'arguments' => [
                    'autoborna.lead.reportbundle.fields_builder',
                ],
            ],
            'autoborna.lead.reportbundle.report_dnc_subscriber' => [
                'class'     => \Autoborna\LeadBundle\EventListener\ReportDNCSubscriber::class,
                'arguments' => [
                    'autoborna.lead.reportbundle.fields_builder',
                    'autoborna.lead.model.company_report_data',
                    'translator',
                    'router',
                    'autoborna.channel.helper.channel_list',
                ],
            ],
            'autoborna.lead.reportbundle.segment_log_subscriber' => [
                'class'     => \Autoborna\LeadBundle\EventListener\SegmentLogReportSubscriber::class,
                'arguments' => [
                    'autoborna.lead.reportbundle.fields_builder',
                ],
            ],
            'autoborna.lead.reportbundle.report_utm_tag_subscriber' => [
                'class'     => \Autoborna\LeadBundle\EventListener\ReportUtmTagSubscriber::class,
                'arguments' => [
                    'autoborna.lead.reportbundle.fields_builder',
                    'autoborna.lead.model.company_report_data',
                ],
            ],
            'autoborna.lead.calendarbundle.subscriber' => [
                'class'     => \Autoborna\LeadBundle\EventListener\CalendarSubscriber::class,
                'arguments' => [
                    'doctrine.dbal.default_connection',
                    'translator',
                    'router',
                ],
            ],
            'autoborna.lead.pointbundle.subscriber' => [
                'class'     => \Autoborna\LeadBundle\EventListener\PointSubscriber::class,
                'arguments' => [
                    'autoborna.lead.model.lead',
                ],
            ],
            'autoborna.lead.search.subscriber' => [
                'class'     => \Autoborna\LeadBundle\EventListener\SearchSubscriber::class,
                'arguments' => [
                    'autoborna.lead.model.lead',
                    'autoborna.email.repository.email',
                    'translator',
                    'autoborna.security',
                    'autoborna.helper.templating',
                ],
            ],
            'autoborna.webhook.subscriber' => [
                'class'     => \Autoborna\LeadBundle\EventListener\WebhookSubscriber::class,
                'arguments' => [
                    'autoborna.webhook.model.webhook',
                ],
            ],
            'autoborna.lead.dashboard.subscriber' => [
                'class'     => \Autoborna\LeadBundle\EventListener\DashboardSubscriber::class,
                'arguments' => [
                    'autoborna.lead.model.lead',
                    'autoborna.lead.model.list',
                    'router',
                    'translator',
                ],
            ],
            'autoborna.lead.maintenance.subscriber' => [
                'class'     => \Autoborna\LeadBundle\EventListener\MaintenanceSubscriber::class,
                'arguments' => [
                    'doctrine.dbal.default_connection',
                    'translator',
                ],
            ],
            'autoborna.lead.stats.subscriber' => [
                'class'     => \Autoborna\LeadBundle\EventListener\StatsSubscriber::class,
                'arguments' => [
                    'autoborna.security',
                    'doctrine.orm.entity_manager',
                ],
            ],
            'autoborna.lead.button.subscriber' => [
                'class'     => \Autoborna\LeadBundle\EventListener\ButtonSubscriber::class,
                'arguments' => [
                    'translator',
                    'router',
                ],
            ],
            'autoborna.lead.import.contact.subscriber' => [
                'class'     => Autoborna\LeadBundle\EventListener\ImportContactSubscriber::class,
                'arguments' => [
                    'autoborna.lead.field.field_list',
                    'autoborna.security',
                    'autoborna.lead.model.lead',
                    'translator',
                ],
            ],
            'autoborna.lead.import.company.subscriber' => [
                'class'     => Autoborna\LeadBundle\EventListener\ImportCompanySubscriber::class,
                'arguments' => [
                    'autoborna.lead.field.field_list',
                    'autoborna.security',
                    'autoborna.lead.model.company',
                    'translator',
                ],
            ],
            'autoborna.lead.import.subscriber' => [
                'class'     => Autoborna\LeadBundle\EventListener\ImportSubscriber::class,
                'arguments' => [
                    'autoborna.helper.ip_lookup',
                    'autoborna.core.model.auditlog',
                ],
            ],
            'autoborna.lead.configbundle.subscriber' => [
                'class' => Autoborna\LeadBundle\EventListener\ConfigSubscriber::class,
            ],
            'autoborna.lead.timeline_events.subscriber' => [
                'class'     => \Autoborna\LeadBundle\EventListener\TimelineEventLogSubscriber::class,
                'arguments' => [
                    'translator',
                    'autoborna.lead.repository.lead_event_log',
                ],
            ],
            'autoborna.lead.timeline_events.campaign.subscriber' => [
                'class'     => \Autoborna\LeadBundle\EventListener\TimelineEventLogCampaignSubscriber::class,
                'arguments' => [
                    'autoborna.lead.repository.lead_event_log',
                    'autoborna.helper.user',
                    'translator',
                ],
            ],
            'autoborna.lead.timeline_events.segment.subscriber' => [
                'class'     => \Autoborna\LeadBundle\EventListener\TimelineEventLogSegmentSubscriber::class,
                'arguments' => [
                    'autoborna.lead.repository.lead_event_log',
                    'autoborna.helper.user',
                    'translator',
                    'doctrine.orm.entity_manager',
                ],
            ],
            'autoborna.lead.subscriber.segment' => [
                'class'     => \Autoborna\LeadBundle\EventListener\SegmentSubscriber::class,
                'arguments' => [
                    'autoborna.helper.ip_lookup',
                    'autoborna.core.model.auditlog',
                    'autoborna.lead.model.list',
                    'translator',
                ],
            ],
            'autoborna.lead.serializer.subscriber' => [
                'class'     => \Autoborna\LeadBundle\EventListener\SerializerSubscriber::class,
                'arguments' => [
                    'request_stack',
                ],
                'tag'          => 'jms_serializer.event_subscriber',
                'tagArguments' => [
                    'event' => \JMS\Serializer\EventDispatcher\Events::POST_SERIALIZE,
                ],
            ],
            'autoborna.lead.subscriber.donotcontact' => [
                'class'     => \Autoborna\LeadBundle\EventListener\DoNotContactSubscriber::class,
                'arguments' => [
                    'autoborna.lead.model.dnc',
                ],
            ],
            'autoborna.lead.subscriber.filterOperator' => [
                'class'     => \Autoborna\LeadBundle\EventListener\FilterOperatorSubscriber::class,
                'arguments' => [
                    'autoborna.lead.segment.operator_options',
                    'autoborna.lead.repository.field',
                    'autoborna.lead.provider.typeOperator',
                    'autoborna.lead.provider.fieldChoices',
                    'translator',
                ],
            ],
            'autoborna.lead.subscriber.typeOperator' => [
                'class'     => \Autoborna\LeadBundle\EventListener\TypeOperatorSubscriber::class,
                'arguments' => [
                    'autoborna.lead.model.lead',
                    'autoborna.lead.model.list',
                    'autoborna.campaign.model.campaign',
                    'autoborna.email.model.email',
                    'autoborna.stage.model.stage',
                    'autoborna.category.model.category',
                    'autoborna.asset.model.asset',
                    'translator',
                ],
            ],
            'autoborna.lead.subscriber.segmentOperatorQuery' => [
                'class'     => \Autoborna\LeadBundle\EventListener\SegmentOperatorQuerySubscriber::class,
            ],
            'autoborna.lead.generated_columns.subscriber' => [
                'class'     => \Autoborna\LeadBundle\EventListener\GeneratedColumnSubscriber::class,
                'arguments' => [
                    'autoborna.lead.model.list',
                    'translator',
                ],
            ],
        ],
        'forms' => [
            'autoborna.form.type.lead' => [
                'class'     => \Autoborna\LeadBundle\Form\Type\LeadType::class,
                'arguments' => [
                    'translator',
                    'autoborna.lead.model.company',
                    'doctrine.orm.entity_manager',
                ],
            ],
            'autoborna.form.type.leadlist' => [
                'class'     => \Autoborna\LeadBundle\Form\Type\ListType::class,
                'arguments' => [
                    'translator',
                    'autoborna.lead.model.list',
                ],
            ],
            'autoborna.form.type.leadlist_choices' => [
                'class'     => \Autoborna\LeadBundle\Form\Type\LeadListType::class,
                'arguments' => ['autoborna.lead.model.list'],
            ],
            'autoborna.form.type.leadlist_filter' => [
                'class'       => \Autoborna\LeadBundle\Form\Type\FilterType::class,
                'arguments'   => [
                    'autoborna.lead.provider.formAdjustments',
                    'autoborna.lead.model.list',
                ],
            ],
            'autoborna.form.type.leadfield' => [
                'class'     => \Autoborna\LeadBundle\Form\Type\FieldType::class,
                'arguments' => [
                    'doctrine.orm.default_entity_manager',
                    'translator',
                    'autoborna.lead.field.identifier_fields',
                ],
                'alias'     => 'leadfield',
            ],
            'autoborna.form.type.lead.submitaction.pointschange' => [
                'class'     => \Autoborna\LeadBundle\Form\Type\FormSubmitActionPointsChangeType::class,
            ],
            'autoborna.form.type.lead.submitaction.addutmtags' => [
                'class'     => \Autoborna\LeadBundle\Form\Type\ActionAddUtmTagsType::class,
            ],
            'autoborna.form.type.lead.submitaction.removedonotcontact' => [
                'class'     => \Autoborna\LeadBundle\Form\Type\ActionRemoveDoNotContact::class,
            ],
            'autoborna.form.type.leadpoints_action' => [
                'class' => \Autoborna\LeadBundle\Form\Type\PointActionType::class,
            ],
            'autoborna.form.type.leadlist_action' => [
                'class' => \Autoborna\LeadBundle\Form\Type\ListActionType::class,
            ],
            'autoborna.form.type.updatelead_action' => [
                'class'     => \Autoborna\LeadBundle\Form\Type\UpdateLeadActionType::class,
                'arguments' => ['autoborna.lead.model.field'],
            ],
            'autoborna.form.type.updatecompany_action' => [
                'class'     => Autoborna\LeadBundle\Form\Type\UpdateCompanyActionType::class,
                'arguments' => ['autoborna.lead.model.field'],
            ],
            'autoborna.form.type.leadnote' => [
                'class' => Autoborna\LeadBundle\Form\Type\NoteType::class,
            ],
            'autoborna.form.type.leaddevice' => [
                'class' => Autoborna\LeadBundle\Form\Type\DeviceType::class,
            ],
            'autoborna.form.type.lead_import' => [
                'class' => \Autoborna\LeadBundle\Form\Type\LeadImportType::class,
            ],
            'autoborna.form.type.lead_field_import' => [
                'class'     => \Autoborna\LeadBundle\Form\Type\LeadImportFieldType::class,
                'arguments' => ['translator', 'doctrine.orm.entity_manager'],
            ],
            'autoborna.form.type.lead_quickemail' => [
                'class'     => \Autoborna\LeadBundle\Form\Type\EmailType::class,
                'arguments' => ['autoborna.helper.user'],
            ],
            'autoborna.form.type.lead_tag' => [
                'class'     => \Autoborna\LeadBundle\Form\Type\TagType::class,
                'arguments' => ['doctrine.orm.entity_manager'],
            ],
            'autoborna.form.type.modify_lead_tags' => [
                'class'     => \Autoborna\LeadBundle\Form\Type\ModifyLeadTagsType::class,
                'arguments' => ['translator'],
            ],
            'autoborna.form.type.lead_entity_tag' => [
                'class' => \Autoborna\LeadBundle\Form\Type\TagEntityType::class,
            ],
            'autoborna.form.type.lead_batch' => [
                'class' => \Autoborna\LeadBundle\Form\Type\BatchType::class,
            ],
            'autoborna.form.type.lead_batch_dnc' => [
                'class' => \Autoborna\LeadBundle\Form\Type\DncType::class,
            ],
            'autoborna.form.type.lead_batch_stage' => [
                'class' => \Autoborna\LeadBundle\Form\Type\StageType::class,
            ],
            'autoborna.form.type.lead_batch_owner' => [
                'class' => \Autoborna\LeadBundle\Form\Type\OwnerType::class,
            ],
            'autoborna.form.type.lead_merge' => [
                'class' => \Autoborna\LeadBundle\Form\Type\MergeType::class,
            ],
            'autoborna.form.type.lead_contact_frequency_rules' => [
                'class'     => \Autoborna\LeadBundle\Form\Type\ContactFrequencyType::class,
                'arguments' => [
                    'autoborna.helper.core_parameters',
                ],
            ],
            'autoborna.form.type.contact_channels' => [
                'class'     => \Autoborna\LeadBundle\Form\Type\ContactChannelsType::class,
                'arguments' => [
                    'autoborna.helper.core_parameters',
                ],
            ],
            'autoborna.form.type.campaignevent_lead_field_value' => [
                'class'     => \Autoborna\LeadBundle\Form\Type\CampaignEventLeadFieldValueType::class,
                'arguments' => [
                    'translator',
                    'autoborna.lead.model.lead',
                    'autoborna.lead.model.field',
                ],
            ],
            'autoborna.form.type.campaignevent_lead_device' => [
                'class' => \Autoborna\LeadBundle\Form\Type\CampaignEventLeadDeviceType::class,
            ],
            'autoborna.form.type.campaignevent_lead_tags' => [
                'class'     => Autoborna\LeadBundle\Form\Type\CampaignEventLeadTagsType::class,
                'arguments' => ['translator'],
            ],
            'autoborna.form.type.campaignevent_lead_segments' => [
                'class' => \Autoborna\LeadBundle\Form\Type\CampaignEventLeadSegmentsType::class,
            ],
            'autoborna.form.type.campaignevent_lead_campaigns' => [
                'class'     => Autoborna\LeadBundle\Form\Type\CampaignEventLeadCampaignsType::class,
                'arguments' => ['autoborna.lead.model.list'],
            ],
            'autoborna.form.type.campaignevent_lead_owner' => [
                'class' => \Autoborna\LeadBundle\Form\Type\CampaignEventLeadOwnerType::class,
            ],
            'autoborna.form.type.lead_fields' => [
                'class'     => \Autoborna\LeadBundle\Form\Type\LeadFieldsType::class,
                'arguments' => ['autoborna.lead.model.field'],
            ],
            'autoborna.form.type.lead_columns' => [
                'class'     => \Autoborna\LeadBundle\Form\Type\ContactColumnsType::class,
                'arguments' => [
                    'autoborna.lead.columns.dictionary',
                ],
            ],
            'autoborna.form.type.lead_dashboard_leads_in_time_widget' => [
                'class' => \Autoborna\LeadBundle\Form\Type\DashboardLeadsInTimeWidgetType::class,
            ],
            'autoborna.form.type.lead_dashboard_leads_lifetime_widget' => [
                'class'     => \Autoborna\LeadBundle\Form\Type\DashboardLeadsLifetimeWidgetType::class,
                'arguments' => ['autoborna.lead.model.list', 'translator'],
            ],
            'autoborna.company.type.form' => [
                'class'     => \Autoborna\LeadBundle\Form\Type\CompanyType::class,
                'arguments' => ['doctrine.orm.entity_manager', 'router', 'translator'],
            ],
            'autoborna.company.campaign.action.type.form' => [
                'class'     => \Autoborna\LeadBundle\Form\Type\AddToCompanyActionType::class,
                'arguments' => ['router'],
            ],
            'autoborna.lead.events.changeowner.type.form' => [
                'class'     => 'Autoborna\LeadBundle\Form\Type\ChangeOwnerType',
                'arguments' => ['autoborna.user.model.user'],
            ],
            'autoborna.company.list.type.form' => [
                'class'     => \Autoborna\LeadBundle\Form\Type\CompanyListType::class,
                'arguments' => [
                    'autoborna.lead.model.company',
                    'autoborna.helper.user',
                    'translator',
                    'router',
                    'database_connection',
                ],
            ],
            'autoborna.form.type.lead_categories' => [
                'class'     => \Autoborna\LeadBundle\Form\Type\LeadCategoryType::class,
                'arguments' => ['autoborna.category.model.category'],
            ],
            'autoborna.company.merge.type.form' => [
                'class' => \Autoborna\LeadBundle\Form\Type\CompanyMergeType::class,
            ],
            'autoborna.form.type.company_change_score' => [
                'class' => \Autoborna\LeadBundle\Form\Type\CompanyChangeScoreActionType::class,
            ],
            'autoborna.form.type.config.form' => [
                'class' => Autoborna\LeadBundle\Form\Type\ConfigType::class,
            ],
            'autoborna.form.type.preference.channels' => [
                'class'     => \Autoborna\LeadBundle\Form\Type\PreferenceChannelsType::class,
                'arguments' => [
                    'autoborna.lead.model.lead',
                ],
            ],
            'autoborna.segment.config' => [
                'class' => \Autoborna\LeadBundle\Form\Type\SegmentConfigType::class,
            ],
        ],
        'other' => [
            'autoborna.lead.doctrine.subscriber' => [
                'class'     => 'Autoborna\LeadBundle\EventListener\DoctrineSubscriber',
                'tag'       => 'doctrine.event_subscriber',
                'arguments' => ['monolog.logger.autoborna'],
            ],
            'autoborna.validator.leadlistaccess' => [
                'class'     => \Autoborna\LeadBundle\Form\Validator\Constraints\LeadListAccessValidator::class,
                'arguments' => ['autoborna.lead.model.list'],
                'tag'       => 'validator.constraint_validator',
                'alias'     => 'leadlist_access',
            ],
            'autoborna.validator.emailaddress' => [
                'class'     => \Autoborna\LeadBundle\Form\Validator\Constraints\EmailAddressValidator::class,
                'arguments' => [
                    'autoborna.validator.email',
                ],
                'tag'       => 'validator.constraint_validator',
            ],
            \Autoborna\LeadBundle\Form\Validator\Constraints\FieldAliasKeywordValidator::class => [
                'class'     => \Autoborna\LeadBundle\Form\Validator\Constraints\FieldAliasKeywordValidator::class,
                'tag'       => 'validator.constraint_validator',
                'arguments' => [
                    'autoborna.lead.model.list',
                    'autoborna.helper.field.alias',
                    '@doctrine.orm.entity_manager',
                    'translator',
                    'autoborna.lead.repository.lead_segment_filter_descriptor',
                ],
            ],
            \Autoborna\CoreBundle\Form\Validator\Constraints\FileEncodingValidator::class => [
                'class'     => \Autoborna\CoreBundle\Form\Validator\Constraints\FileEncodingValidator::class,
                'tag'       => 'validator.constraint_validator',
                'arguments' => [
                    'autoborna.lead.model.list',
                    'autoborna.helper.field.alias',
                ],
            ],
            'autoborna.lead.constraint.alias' => [
                'class'     => \Autoborna\LeadBundle\Form\Validator\Constraints\UniqueUserAliasValidator::class,
                'arguments' => ['autoborna.lead.repository.lead_list', 'autoborna.helper.user'],
                'tag'       => 'validator.constraint_validator',
                'alias'     => 'uniqueleadlist',
            ],
            'autoborna.lead.validator.custom_field' => [
                'class'     => \Autoborna\LeadBundle\Validator\CustomFieldValidator::class,
                'arguments' => ['autoborna.lead.model.field', 'translator'],
            ],
            'autoborna.lead_list.constraint.in_use' => [
                'class'     => Autoborna\LeadBundle\Form\Validator\Constraints\SegmentInUseValidator::class,
                'arguments' => [
                    'autoborna.lead.model.list',
                ],
                'tag'       => 'validator.constraint_validator',
                'alias'     => 'segment_in_use',
            ],
            'autoborna.lead.event.dispatcher' => [
                'class'     => \Autoborna\LeadBundle\Helper\LeadChangeEventDispatcher::class,
                'arguments' => [
                    'event_dispatcher',
                ],
            ],
            'autoborna.lead.merger' => [
                'class'     => \Autoborna\LeadBundle\Deduplicate\ContactMerger::class,
                'arguments' => [
                    'autoborna.lead.model.lead',
                    'autoborna.lead.repository.merged_records',
                    'event_dispatcher',
                    'monolog.logger.autoborna',
                ],
            ],
            'autoborna.lead.deduper' => [
                'class'     => \Autoborna\LeadBundle\Deduplicate\ContactDeduper::class,
                'arguments' => [
                    'autoborna.lead.model.field',
                    'autoborna.lead.merger',
                    'autoborna.lead.repository.lead',
                ],
            ],
            'autoborna.company.deduper' => [
                'class'     => \Autoborna\LeadBundle\Deduplicate\CompanyDeduper::class,
                'arguments' => [
                    'autoborna.lead.model.field',
                    'autoborna.lead.repository.company',
                ],
            ],
            'autoborna.lead.helper.primary_company' => [
                'class'     => \Autoborna\LeadBundle\Helper\PrimaryCompanyHelper::class,
                'arguments' => [
                    'autoborna.lead.repository.company_lead',
                ],
            ],
            'autoborna.lead.validator.length' => [
                'class'     => Autoborna\LeadBundle\Validator\Constraints\LengthValidator::class,
                'tag'       => 'validator.constraint_validator',
            ],
            'autoborna.lead.segment.stat.dependencies' => [
                'class'     => \Autoborna\LeadBundle\Segment\Stat\SegmentDependencies::class,
                'arguments' => [
                    'autoborna.email.model.email',
                    'autoborna.campaign.model.campaign',
                    'autoborna.form.model.action',
                    'autoborna.lead.model.list',
                    'autoborna.point.model.triggerevent',
                    'autoborna.report.model.report',
                ],
            ],
            'autoborna.lead.segment.stat.chart.query.factory' => [
                'class'     => \Autoborna\LeadBundle\Segment\Stat\SegmentChartQueryFactory::class,
                'arguments' => [
                ],
            ],
            'autoborna.lead.segment.stat.campaign.share' => [
                'class'     => \Autoborna\LeadBundle\Segment\Stat\SegmentCampaignShare::class,
                'arguments' => [
                    'autoborna.campaign.model.campaign',
                    'autoborna.helper.cache_storage',
                    '@doctrine.orm.entity_manager',
                ],
            ],
            'autoborna.lead.columns.dictionary' => [
                'class'     => \Autoborna\LeadBundle\Services\ContactColumnsDictionary::class,
                'arguments' => [
                    'autoborna.lead.model.field',
                    'translator',
                    'autoborna.helper.core_parameters',
                ],
            ],
        ],
        'repositories' => [
            'autoborna.lead.repository.company' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Autoborna\LeadBundle\Entity\Company::class,
                ],
                'methodCalls' => [
                    'setUniqueIdentifiersOperator' => [
                        '%autoborna.company_unique_identifiers_operator%',
                    ],
                ],
            ],
            'autoborna.lead.repository.company_lead' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Autoborna\LeadBundle\Entity\CompanyLead::class,
                ],
            ],
            'autoborna.lead.repository.stages_lead_log' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Autoborna\LeadBundle\Entity\StagesChangeLog::class,
                ],
            ],
            'autoborna.lead.repository.dnc' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Autoborna\LeadBundle\Entity\DoNotContact::class,
                ],
            ],
            'autoborna.lead.repository.lead' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Autoborna\LeadBundle\Entity\Lead::class,
                ],
                'methodCalls' => [
                    'setUniqueIdentifiersOperator' => [
                        '%autoborna.contact_unique_identifiers_operator%',
                    ],
                    'setListLeadRepository' => [
                        '@autoborna.lead.repository.list_lead',
                    ],
                ],
            ],
            'autoborna.lead.repository.list_lead' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Autoborna\LeadBundle\Entity\ListLead::class,
                ],
            ],
            'autoborna.lead.repository.frequency_rule' => [
                'class'     => \Autoborna\LeadBundle\Entity\FrequencyRuleRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Autoborna\LeadBundle\Entity\FrequencyRule::class,
                ],
            ],
            'autoborna.lead.repository.lead_event_log' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Autoborna\LeadBundle\Entity\LeadEventLog::class,
                ],
            ],
            'autoborna.lead.repository.lead_device' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Autoborna\LeadBundle\Entity\LeadDevice::class,
                ],
            ],
            'autoborna.lead.repository.lead_list' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Autoborna\LeadBundle\Entity\LeadList::class,
                ],
            ],
            'autoborna.lead.repository.points_change_log' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Autoborna\LeadBundle\Entity\PointsChangeLog::class,
                ],
            ],
            'autoborna.lead.repository.merged_records' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Autoborna\LeadBundle\Entity\MergeRecord::class,
                ],
            ],
            'autoborna.lead.repository.field' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Autoborna\LeadBundle\Entity\LeadField::class,
                ],
            ],
            //  Segment Filter Query builders
            'autoborna.lead.query.builder.basic' => [
                'class'     => \Autoborna\LeadBundle\Segment\Query\Filter\BaseFilterQueryBuilder::class,
                'arguments' => ['autoborna.lead.model.random_parameter_name', 'event_dispatcher'],
            ],
            'autoborna.lead.query.builder.foreign.value' => [
                'class'     => \Autoborna\LeadBundle\Segment\Query\Filter\ForeignValueFilterQueryBuilder::class,
                'arguments' => ['autoborna.lead.model.random_parameter_name', 'event_dispatcher'],
            ],
            'autoborna.lead.query.builder.foreign.func' => [
                'class'     => \Autoborna\LeadBundle\Segment\Query\Filter\ForeignFuncFilterQueryBuilder::class,
                'arguments' => ['autoborna.lead.model.random_parameter_name', 'event_dispatcher'],
            ],
            'autoborna.lead.query.builder.special.dnc' => [
                'class'     => \Autoborna\LeadBundle\Segment\Query\Filter\DoNotContactFilterQueryBuilder::class,
                'arguments' => ['autoborna.lead.model.random_parameter_name', 'event_dispatcher'],
            ],
            'autoborna.lead.query.builder.special.integration' => [
                'class'     => \Autoborna\LeadBundle\Segment\Query\Filter\IntegrationCampaignFilterQueryBuilder::class,
                'arguments' => ['autoborna.lead.model.random_parameter_name', 'event_dispatcher'],
            ],
            'autoborna.lead.query.builder.special.sessions' => [
                'class'     => \Autoborna\LeadBundle\Segment\Query\Filter\SessionsFilterQueryBuilder::class,
                'arguments' => ['autoborna.lead.model.random_parameter_name', 'event_dispatcher'],
            ],
            'autoborna.lead.query.builder.complex_relation.value' => [
                'class'     => \Autoborna\LeadBundle\Segment\Query\Filter\ComplexRelationValueFilterQueryBuilder::class,
                'arguments' => ['autoborna.lead.model.random_parameter_name', 'event_dispatcher'],
            ],
            'autoborna.lead.query.builder.special.leadlist' => [
                'class'     => \Autoborna\LeadBundle\Segment\Query\Filter\SegmentReferenceFilterQueryBuilder::class,
                'arguments' => [
                    'autoborna.lead.model.random_parameter_name',
                    'autoborna.lead.repository.lead_segment_query_builder',
                    'doctrine.orm.entity_manager',
                    'autoborna.lead.model.lead_segment_filter_factory',
                    'event_dispatcher',
                ],
            ],
            'autoborna.lead.query.builder.channel_click.value' => [
                'class'     => \Autoborna\LeadBundle\Segment\Query\Filter\ChannelClickQueryBuilder::class,
                'arguments' => [
                    'autoborna.lead.model.random_parameter_name',
                    'event_dispatcher',
                ],
            ],
        ],
        'helpers' => [
            'autoborna.helper.template.avatar' => [
                'class'     => Autoborna\LeadBundle\Templating\Helper\AvatarHelper::class,
                'arguments' => [
                    'templating.helper.assets',
                    'autoborna.helper.paths',
                    'autoborna.helper.template.gravatar',
                    'autoborna.helper.template.default_avatar',
                ],
                'alias'     => 'lead_avatar',
            ],
            'autoborna.helper.template.default_avatar' => [
                'class'     => Autoborna\LeadBundle\Templating\Helper\DefaultAvatarHelper::class,
                'arguments' => [
                    'autoborna.helper.paths',
                    'templating.helper.assets',
                ],
                'alias'     => 'default_avatar',
            ],
            'autoborna.helper.field.alias' => [
                'class'     => \Autoborna\LeadBundle\Helper\FieldAliasHelper::class,
                'arguments' => ['autoborna.lead.model.field'],
            ],
            'autoborna.helper.template.dnc_reason' => [
                'class'     => Autoborna\LeadBundle\Templating\Helper\DncReasonHelper::class,
                'arguments' => ['translator'],
                'alias'     => 'lead_dnc_reason',
            ],
            'autoborna.helper.segment.count.cache' => [
                'class'     => \Autoborna\LeadBundle\Helper\SegmentCountCacheHelper::class,
                'arguments' => ['autoborna.helper.cache_storage'],
            ],
        ],
        'models' => [
            'autoborna.lead.model.lead' => [
                'class'     => \Autoborna\LeadBundle\Model\LeadModel::class,
                'arguments' => [
                    'request_stack',
                    'autoborna.helper.cookie',
                    'autoborna.helper.ip_lookup',
                    'autoborna.helper.paths',
                    'autoborna.helper.integration',
                    'autoborna.lead.model.field',
                    'autoborna.lead.model.list',
                    'form.factory',
                    'autoborna.lead.model.company',
                    'autoborna.category.model.category',
                    'autoborna.channel.helper.channel_list',
                    'autoborna.helper.core_parameters',
                    'autoborna.validator.email',
                    'autoborna.user.provider',
                    'autoborna.tracker.contact',
                    'autoborna.tracker.device',
                    'autoborna.lead.model.legacy_lead',
                    'autoborna.lead.model.ipaddress',
                ],
            ],

            // Deprecated support for circular dependency
            'autoborna.lead.model.legacy_lead' => [
                'class'     => \Autoborna\LeadBundle\Model\LegacyLeadModel::class,
                'arguments' => [
                    'service_container',
                ],
            ],
            'autoborna.lead.model.field' => [
                'class'     => \Autoborna\LeadBundle\Model\FieldModel::class,
                'arguments' => [
                    'autoborna.schema.helper.column',
                    'autoborna.lead.model.list',
                    'autoborna.lead.field.custom_field_column',
                    'autoborna.lead.field.dispatcher.field_save_dispatcher',
                    'autoborna.lead.repository.field',
                    'autoborna.lead.field.fields_with_unique_identifier',
                    'autoborna.lead.field.field_list',
                    'autoborna.lead.field.lead_field_saver',
                ],
            ],
            'autoborna.lead.model.list' => [
                'class'     => \Autoborna\LeadBundle\Model\ListModel::class,
                'arguments' => [
                    'autoborna.category.model.category',
                    'autoborna.helper.core_parameters',
                    'autoborna.lead.model.lead_segment_service',
                    'autoborna.lead.segment.stat.chart.query.factory',
                    'request_stack',
                    'autoborna.helper.segment.count.cache',
                ],
            ],
            'autoborna.lead.repository.lead_segment_filter_descriptor' => [
                'class'     => \Autoborna\LeadBundle\Services\ContactSegmentFilterDictionary::class,
                'arguments' => [
                    'event_dispatcher',
                ],
            ],
            'autoborna.lead.repository.lead_segment_query_builder' => [
                'class'     => Autoborna\LeadBundle\Segment\Query\ContactSegmentQueryBuilder::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                    'autoborna.lead.model.random_parameter_name',
                    'event_dispatcher',
                ],
            ],
            'autoborna.lead.model.lead_segment_service' => [
                'class'     => \Autoborna\LeadBundle\Segment\ContactSegmentService::class,
                'arguments' => [
                    'autoborna.lead.model.lead_segment_filter_factory',
                    'autoborna.lead.repository.lead_segment_query_builder',
                    'monolog.logger.autoborna',
                ],
            ],
            'autoborna.lead.model.lead_segment_filter_factory' => [
                'class'     => \Autoborna\LeadBundle\Segment\ContactSegmentFilterFactory::class,
                'arguments' => [
                    'autoborna.lead.model.lead_segment_schema_cache',
                    '@service_container',
                    'autoborna.lead.model.lead_segment_decorator_factory',
                ],
            ],
            'autoborna.lead.model.lead_segment_schema_cache' => [
                'class'     => \Autoborna\LeadBundle\Segment\TableSchemaColumnsCache::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                ],
            ],
            'autoborna.lead.model.relative_date' => [
                'class'     => \Autoborna\LeadBundle\Segment\RelativeDate::class,
                'arguments' => [
                    'translator',
                ],
            ],
            'autoborna.lead.model.lead_segment_filter_operator' => [
                'class'     => \Autoborna\LeadBundle\Segment\ContactSegmentFilterOperator::class,
                'arguments' => [
                    'autoborna.lead.provider.fillterOperator',
                ],
            ],
            'autoborna.lead.model.lead_segment_decorator_factory' => [
                'class'     => \Autoborna\LeadBundle\Segment\Decorator\DecoratorFactory::class,
                'arguments' => [
                    'autoborna.lead.repository.lead_segment_filter_descriptor',
                    'autoborna.lead.model.lead_segment_decorator_base',
                    'autoborna.lead.model.lead_segment_decorator_custom_mapped',
                    'autoborna.lead.model.lead_segment.decorator.date.optionFactory',
                    'autoborna.lead.model.lead_segment_decorator_company',
                    'event_dispatcher',
                ],
            ],
            'autoborna.lead.model.lead_segment_decorator_base' => [
                'class'     => \Autoborna\LeadBundle\Segment\Decorator\BaseDecorator::class,
                'arguments' => [
                    'autoborna.lead.model.lead_segment_filter_operator',
                    'autoborna.lead.repository.lead_segment_filter_descriptor',
                ],
            ],
            'autoborna.lead.model.lead_segment_decorator_custom_mapped' => [
                'class'     => \Autoborna\LeadBundle\Segment\Decorator\CustomMappedDecorator::class,
                'arguments' => [
                    'autoborna.lead.model.lead_segment_filter_operator',
                    'autoborna.lead.repository.lead_segment_filter_descriptor',
                ],
            ],
            'autoborna.lead.model.lead_segment_decorator_company' => [
                'class'     => \Autoborna\LeadBundle\Segment\Decorator\CompanyDecorator::class,
                'arguments' => [
                    'autoborna.lead.model.lead_segment_filter_operator',
                    'autoborna.lead.repository.lead_segment_filter_descriptor',
                ],
            ],
            'autoborna.lead.model.lead_segment_decorator_date' => [
                'class'     => \Autoborna\LeadBundle\Segment\Decorator\DateDecorator::class,
                'arguments' => [
                    'autoborna.lead.model.lead_segment_filter_operator',
                    'autoborna.lead.repository.lead_segment_filter_descriptor',
                ],
            ],
            'autoborna.lead.model.lead_segment.decorator.date.optionFactory' => [
                'class'     => \Autoborna\LeadBundle\Segment\Decorator\Date\DateOptionFactory::class,
                'arguments' => [
                    'autoborna.lead.model.lead_segment_decorator_date',
                    'autoborna.lead.model.relative_date',
                    'autoborna.lead.model.lead_segment.timezoneResolver',
                ],
            ],
            'autoborna.lead.model.lead_segment.timezoneResolver' => [
                'class'     => \Autoborna\LeadBundle\Segment\Decorator\Date\TimezoneResolver::class,
                'arguments' => [
                    'autoborna.helper.core_parameters',
                ],
            ],
            'autoborna.lead.provider.fillterOperator' => [
                'class'     => \Autoborna\LeadBundle\Provider\FilterOperatorProvider::class,
                'arguments' => [
                    'event_dispatcher',
                    'translator',
                ],
            ],
            'autoborna.lead.provider.typeOperator' => [
                'class'     => \Autoborna\LeadBundle\Provider\TypeOperatorProvider::class,
                'arguments' => [
                    'event_dispatcher',
                    'autoborna.lead.provider.fillterOperator',
                ],
            ],
            'autoborna.lead.provider.fieldChoices' => [
                'class'     => \Autoborna\LeadBundle\Provider\FieldChoicesProvider::class,
                'arguments' => [
                    'event_dispatcher',
                ],
            ],
            'autoborna.lead.provider.formAdjustments' => [
                'class'     => \Autoborna\LeadBundle\Provider\FormAdjustmentsProvider::class,
                'arguments' => [
                    'event_dispatcher',
                ],
            ],
            'autoborna.lead.model.random_parameter_name' => [
                'class'     => \Autoborna\LeadBundle\Segment\RandomParameterName::class,
            ],
            'autoborna.lead.segment.operator_options' => [
                'class'     => \Autoborna\LeadBundle\Segment\OperatorOptions::class,
            ],
            'autoborna.lead.model.note' => [
                'class' => 'Autoborna\LeadBundle\Model\NoteModel',
            ],
            'autoborna.lead.model.device' => [
                'class'     => Autoborna\LeadBundle\Model\DeviceModel::class,
                'arguments' => [
                    'autoborna.lead.repository.lead_device',
                ],
            ],
            'autoborna.lead.model.company' => [
                'class'     => 'Autoborna\LeadBundle\Model\CompanyModel',
                'arguments' => [
                    'autoborna.lead.model.field',
                    'session',
                    'autoborna.validator.email',
                    'autoborna.company.deduper',
                ],
            ],
            'autoborna.lead.model.import' => [
                'class'     => Autoborna\LeadBundle\Model\ImportModel::class,
                'arguments' => [
                    'autoborna.helper.paths',
                    'autoborna.lead.model.lead',
                    'autoborna.core.model.notification',
                    'autoborna.helper.core_parameters',
                    'autoborna.lead.model.company',
                ],
            ],
            'autoborna.lead.model.tag' => [
                'class' => \Autoborna\LeadBundle\Model\TagModel::class,
            ],
            'autoborna.lead.model.company_report_data' => [
                'class'     => \Autoborna\LeadBundle\Model\CompanyReportData::class,
                'arguments' => [
                    'autoborna.lead.model.field',
                    'translator',
                ],
            ],
            'autoborna.lead.reportbundle.fields_builder' => [
                'class'     => \Autoborna\LeadBundle\Report\FieldsBuilder::class,
                'arguments' => [
                    'autoborna.lead.model.field',
                    'autoborna.lead.model.list',
                    'autoborna.user.model.user',
                ],
            ],
            'autoborna.lead.model.dnc' => [
                'class'     => \Autoborna\LeadBundle\Model\DoNotContact::class,
                'arguments' => [
                    'autoborna.lead.model.lead',
                    'autoborna.lead.repository.dnc',
                ],
            ],
            'autoborna.lead.model.segment.action' => [
                'class'     => \Autoborna\LeadBundle\Model\SegmentActionModel::class,
                'arguments' => [
                    'autoborna.lead.model.lead',
                ],
            ],
            'autoborna.lead.factory.device_detector_factory' => [
                'class'     => \Autoborna\LeadBundle\Tracker\Factory\DeviceDetectorFactory\DeviceDetectorFactory::class,
                'arguments' => [
                  'autoborna.cache.provider',
                ],
            ],
            'autoborna.lead.service.contact_tracking_service' => [
                'class'     => \Autoborna\LeadBundle\Tracker\Service\ContactTrackingService\ContactTrackingService::class,
                'arguments' => [
                    'autoborna.helper.cookie',
                    'autoborna.lead.repository.lead_device',
                    'autoborna.lead.repository.lead',
                    'autoborna.lead.repository.merged_records',
                    'request_stack',
                ],
            ],
            'autoborna.lead.service.device_creator_service' => [
                'class' => \Autoborna\LeadBundle\Tracker\Service\DeviceCreatorService\DeviceCreatorService::class,
            ],
            'autoborna.lead.service.device_tracking_service' => [
                'class'     => \Autoborna\LeadBundle\Tracker\Service\DeviceTrackingService\DeviceTrackingService::class,
                'arguments' => [
                    'autoborna.helper.cookie',
                    'doctrine.orm.entity_manager',
                    'autoborna.lead.repository.lead_device',
                    'autoborna.helper.random',
                    'request_stack',
                    'autoborna.security',
                ],
            ],
            'autoborna.tracker.contact' => [
                'class'     => \Autoborna\LeadBundle\Tracker\ContactTracker::class,
                'arguments' => [
                    'autoborna.lead.repository.lead',
                    'autoborna.lead.service.contact_tracking_service',
                    'autoborna.tracker.device',
                    'autoborna.security',
                    'monolog.logger.autoborna',
                    'autoborna.helper.ip_lookup',
                    'request_stack',
                    'autoborna.helper.core_parameters',
                    'event_dispatcher',
                    'autoborna.lead.model.field',
                ],
            ],
            'autoborna.tracker.device' => [
                'class'     => \Autoborna\LeadBundle\Tracker\DeviceTracker::class,
                'arguments' => [
                    'autoborna.lead.service.device_creator_service',
                    'autoborna.lead.factory.device_detector_factory',
                    'autoborna.lead.service.device_tracking_service',
                    'monolog.logger.autoborna',
                ],
            ],
            'autoborna.lead.model.ipaddress' => [
                'class'     => Autoborna\LeadBundle\Model\IpAddressModel::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                    'monolog.logger.autoborna',
                ],
            ],
            'autoborna.lead.field.schema_definition' => [
                'class'     => Autoborna\LeadBundle\Field\SchemaDefinition::class,
            ],
            'autoborna.lead.field.custom_field_column' => [
                'class'     => Autoborna\LeadBundle\Field\CustomFieldColumn::class,
                'arguments' => [
                    'autoborna.schema.helper.column',
                    'autoborna.lead.field.schema_definition',
                    'monolog.logger.autoborna',
                    'autoborna.lead.field.lead_field_saver',
                    'autoborna.lead.field.custom_field_index',
                    'autoborna.lead.field.dispatcher.field_column_dispatcher',
                    'translator',
                ],
            ],
            'autoborna.lead.field.custom_field_index' => [
                'class'     => Autoborna\LeadBundle\Field\CustomFieldIndex::class,
                'arguments' => [
                    'autoborna.schema.helper.index',
                    'monolog.logger.autoborna',
                    'autoborna.lead.field.fields_with_unique_identifier',
                ],
            ],
            'autoborna.lead.field.dispatcher.field_save_dispatcher' => [
                'class'     => Autoborna\LeadBundle\Field\Dispatcher\FieldSaveDispatcher::class,
                'arguments' => [
                    'event_dispatcher',
                    'doctrine.orm.entity_manager',
                ],
            ],
            'autoborna.lead.field.dispatcher.field_column_dispatcher' => [
                'class'     => Autoborna\LeadBundle\Field\Dispatcher\FieldColumnDispatcher::class,
                'arguments' => [
                    'event_dispatcher',
                    'autoborna.lead.field.settings.background_settings',
                ],
            ],
            'autoborna.lead.field.dispatcher.field_column_background_dispatcher' => [
                'class'     => Autoborna\LeadBundle\Field\Dispatcher\FieldColumnBackgroundJobDispatcher::class,
                'arguments' => [
                    'event_dispatcher',
                ],
            ],
            'autoborna.lead.field.fields_with_unique_identifier' => [
                'class'     => Autoborna\LeadBundle\Field\FieldsWithUniqueIdentifier::class,
                'arguments' => [
                    'autoborna.lead.field.field_list',
                ],
            ],
            'autoborna.lead.field.field_list' => [
                'class'     => Autoborna\LeadBundle\Field\FieldList::class,
                'arguments' => [
                    'autoborna.lead.repository.field',
                    'translator',
                ],
            ],
            'autoborna.lead.field.identifier_fields' => [
                'class'     => \Autoborna\LeadBundle\Field\IdentifierFields::class,
                'arguments' => [
                    'autoborna.lead.field.fields_with_unique_identifier',
                    'autoborna.lead.field.field_list',
                ],
            ],
            'autoborna.lead.field.lead_field_saver' => [
                'class'     => Autoborna\LeadBundle\Field\LeadFieldSaver::class,
                'arguments' => [
                    'autoborna.lead.repository.field',
                    'autoborna.lead.field.dispatcher.field_save_dispatcher',
                ],
            ],
            'autoborna.lead.field.settings.background_settings' => [
                'class'     => Autoborna\LeadBundle\Field\Settings\BackgroundSettings::class,
                'arguments' => [
                    'autoborna.helper.core_parameters',
                ],
            ],
            'autoborna.lead.field.settings.background_service' => [
                'class'     => Autoborna\LeadBundle\Field\BackgroundService::class,
                'arguments' => [
                    'autoborna.lead.model.field',
                    'autoborna.lead.field.custom_field_column',
                    'autoborna.lead.field.lead_field_saver',
                    'autoborna.lead.field.dispatcher.field_column_background_dispatcher',
                    'autoborna.lead.field.notification.custom_field',
                ],
            ],
            'autoborna.lead.field.notification.custom_field' => [
                'class'     => Autoborna\LeadBundle\Field\Notification\CustomFieldNotification::class,
                'arguments' => [
                    'autoborna.core.model.notification',
                    'autoborna.user.model.user',
                    'translator',
                ],
            ],
        ],
        'command' => [
            'autoborna.lead.command.deduplicate' => [
                'class'     => \Autoborna\LeadBundle\Command\DeduplicateCommand::class,
                'arguments' => [
                    'autoborna.lead.deduper',
                    'translator',
                ],
                'tag' => 'console.command',
            ],
            'autoborna.lead.command.create_custom_field' => [
                'class'     => \Autoborna\LeadBundle\Field\Command\CreateCustomFieldCommand::class,
                'arguments' => [
                    'autoborna.lead.field.settings.background_service',
                    'translator',
                    'autoborna.lead.repository.field',
                ],
                'tag' => 'console.command',
            ],
        ],
        'fixtures' => [
            'autoborna.lead.fixture.company' => [
                'class'     => \Autoborna\LeadBundle\DataFixtures\ORM\LoadCompanyData::class,
                'tag'       => \Doctrine\Bundle\FixturesBundle\DependencyInjection\CompilerPass\FixturesCompilerPass::FIXTURE_TAG,
                'arguments' => ['autoborna.lead.model.company'],
            ],
            'autoborna.lead.fixture.contact' => [
                'class'     => \Autoborna\LeadBundle\DataFixtures\ORM\LoadLeadData::class,
                'tag'       => \Doctrine\Bundle\FixturesBundle\DependencyInjection\CompilerPass\FixturesCompilerPass::FIXTURE_TAG,
                'arguments' => ['doctrine.orm.entity_manager', 'autoborna.helper.core_parameters'],
            ],
            'autoborna.lead.fixture.contact_field' => [
                'class'     => \Autoborna\LeadBundle\DataFixtures\ORM\LoadLeadFieldData::class,
                'tag'       => \Doctrine\Bundle\FixturesBundle\DependencyInjection\CompilerPass\FixturesCompilerPass::FIXTURE_TAG,
                'arguments' => [],
            ],
            'autoborna.lead.fixture.segment' => [
                'class'     => \Autoborna\LeadBundle\DataFixtures\ORM\LoadLeadListData::class,
                'tag'       => \Doctrine\Bundle\FixturesBundle\DependencyInjection\CompilerPass\FixturesCompilerPass::FIXTURE_TAG,
                'arguments' => ['autoborna.lead.model.list'],
            ],
            'autoborna.lead.fixture.category' => [
                'class'     => \Autoborna\LeadBundle\DataFixtures\ORM\LoadCategoryData::class,
                'tag'       => \Doctrine\Bundle\FixturesBundle\DependencyInjection\CompilerPass\FixturesCompilerPass::FIXTURE_TAG,
                'arguments' => ['doctrine.orm.entity_manager'],
            ],
            'autoborna.lead.fixture.categorizedleadlists' => [
                'class'     => \Autoborna\LeadBundle\DataFixtures\ORM\LoadCategorizedLeadListData::class,
                'tag'       => \Doctrine\Bundle\FixturesBundle\DependencyInjection\CompilerPass\FixturesCompilerPass::FIXTURE_TAG,
                'arguments' => ['doctrine.orm.entity_manager'],
            ],
            'autoborna.lead.fixture.test.page_hit' => [
                'class'     => \Autoborna\LeadBundle\Tests\DataFixtures\ORM\LoadPageHitData::class,
                'tag'       => \Doctrine\Bundle\FixturesBundle\DependencyInjection\CompilerPass\FixturesCompilerPass::FIXTURE_TAG,
                'optional'  => true,
            ],
            'autoborna.lead.fixture.test.segment' => [
                'class'     => \Autoborna\LeadBundle\Tests\DataFixtures\ORM\LoadSegmentsData::class,
                'tag'       => \Doctrine\Bundle\FixturesBundle\DependencyInjection\CompilerPass\FixturesCompilerPass::FIXTURE_TAG,
                'arguments' => ['autoborna.lead.model.list', 'autoborna.lead.model.lead'],
                'optional'  => true,
            ],
            'autoborna.lead.fixture.test.click' => [
                'class'     => \Autoborna\LeadBundle\Tests\DataFixtures\ORM\LoadClickData::class,
                'tag'       => \Doctrine\Bundle\FixturesBundle\DependencyInjection\CompilerPass\FixturesCompilerPass::FIXTURE_TAG,
                'arguments' => ['autoborna.lead.model.list', 'autoborna.lead.model.lead'],
                'optional'  => true,
            ],
            'autoborna.lead.fixture.test.dnc' => [
                'class'     => \Autoborna\LeadBundle\Tests\DataFixtures\ORM\LoadDncData::class,
                'tag'       => \Doctrine\Bundle\FixturesBundle\DependencyInjection\CompilerPass\FixturesCompilerPass::FIXTURE_TAG,
                'arguments' => ['autoborna.lead.model.list', 'autoborna.lead.model.lead'],
                'optional'  => true,
            ],
            'autoborna.lead.fixture.test.tag' => [
                'class'     => \Autoborna\LeadBundle\Tests\DataFixtures\ORM\LoadTagData::class,
                'tag'       => \Doctrine\Bundle\FixturesBundle\DependencyInjection\CompilerPass\FixturesCompilerPass::FIXTURE_TAG,
                'optional'  => true,
            ],
        ],
    ],
    'parameters' => [
        'parallel_import_limit'               => 1,
        'background_import_if_more_rows_than' => 0,
        'contact_columns'                     => [
            '0' => 'name',
            '1' => 'email',
            '2' => 'location',
            '3' => 'stage',
            '4' => 'points',
            '5' => 'last_active',
            '6' => 'id',
        ],
        \Autoborna\LeadBundle\Field\Settings\BackgroundSettings::CREATE_CUSTOM_FIELD_IN_BACKGROUND => false,
        'company_unique_identifiers_operator'                                                   => \Doctrine\DBAL\Query\Expression\CompositeExpression::TYPE_OR,
        'contact_unique_identifiers_operator'                                                   => \Doctrine\DBAL\Query\Expression\CompositeExpression::TYPE_OR,
        'segment_rebuild_time_warning'                                                          => 30,
    ],
];
