<?php

namespace Autoborna\LeadBundle;

/**
 * Class LeadEvents
 * Events available for LeadBundle.
 */
final class LeadEvents
{
    /**
     * The autoborna.lead_pre_save event is dispatched right before a lead is persisted.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\LeadEvent instance.
     *
     * @var string
     */
    const LEAD_PRE_SAVE = 'autoborna.lead_pre_save';

    /**
     * The autoborna.lead_post_save event is dispatched right after a lead is persisted.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\LeadEvent instance.
     *
     * @var string
     */
    const LEAD_POST_SAVE = 'autoborna.lead_post_save';

    /**
     * The autoborna.lead_points_change event is dispatched if a lead's points changes.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\PointsChangeEvent instance.
     *
     * @var string
     */
    const LEAD_POINTS_CHANGE = 'autoborna.lead_points_change';

    /**
     * The autoborna.lead_points_change event is dispatched if a lead's points changes.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\PointsChangeEvent instance.
     *
     * @var string
     */
    const LEAD_UTMTAGS_ADD = 'autoborna.lead_utmtags_add';

    /**
     * The autoborna.lead_company_change event is dispatched if a lead's company changes.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\LeadChangeCompanyEvent instance.
     *
     * @var string
     */
    const LEAD_COMPANY_CHANGE = 'autoborna.lead_company_change';

    /**
     * The autoborna.lead_list_change event is dispatched if a lead's lists changes.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\ListChangeEvent instance.
     *
     * @var string
     */
    const LEAD_LIST_CHANGE = 'autoborna.lead_list_change';

    /**
     * The autoborna.lead_category_change event is dispatched if a lead's subscribed categories change.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\LeadCategoryEvent instance.
     *
     * @var string
     */
    const LEAD_CATEGORY_CHANGE = 'autoborna.lead_category_change';

    /**
     * The autoborna.lead_list_batch_change event is dispatched if a batch of leads are changed from ListModel::rebuildListLeads().
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\LeadListChange instance.
     *
     * @var string
     */
    const LEAD_LIST_BATCH_CHANGE = 'autoborna.lead_list_batch_change';

    /**
     * The autoborna.lead_pre_delete event is dispatched before a lead is deleted.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\LeadEvent instance.
     *
     * @var string
     */
    const LEAD_PRE_DELETE = 'autoborna.lead_pre_delete';

    /**
     * The autoborna.lead_post_delete event is dispatched after a lead is deleted.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\LeadEvent instance.
     *
     * @var string
     */
    const LEAD_POST_DELETE = 'autoborna.lead_post_delete';

    /**
     * The autoborna.lead_pre_merge event is dispatched before two leads are merged.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\LeadMergeEvent instance.
     *
     * @var string
     */
    const LEAD_PRE_MERGE = 'autoborna.lead_pre_merge';

    /**
     * The autoborna.lead_post_merge event is dispatched after two leads are merged.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\LeadMergeEvent instance.
     *
     * @var string
     */
    const LEAD_POST_MERGE = 'autoborna.lead_post_merge';

    /**
     * The autoborna.lead_identified event is dispatched when a lead first becomes known, i.e. name, email, company.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\LeadEvent instance.
     *
     * @var string
     */
    const LEAD_IDENTIFIED = 'autoborna.lead_identified';

    /**
     * The autoborna.lead_channel_subscription_changed event is dispatched when a lead's DNC status changes.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\ChannelSubscriptionChange instance.
     *
     * @var string
     */
    const CHANNEL_SUBSCRIPTION_CHANGED = 'autoborna.lead_channel_subscription_changed';

    /**
     * The autoborna.lead_build_search_commands event is dispatched when the search commands are built.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\LeadBuildSearchEvent instance.
     *
     * @var string
     */
    const LEAD_BUILD_SEARCH_COMMANDS = 'autoborna.lead_build_search_commands';

    /**
     * The autoborna.company_build_search_commands event is dispatched when the search commands are built.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\CompanyBuildSearchEvent instance.
     *
     * @var string
     */
    const COMPANY_BUILD_SEARCH_COMMANDS = 'autoborna.company_build_search_commands';

    /**
     * The autoborna.current_lead_changed event is dispatched when the current lead is changed to another such as when
     * a new lead is created from a form submit.  This gives opportunity to update session data if applicable.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\LeadChangeEvent instance.
     *
     * @var string
     */
    const CURRENT_LEAD_CHANGED = 'autoborna.current_lead_changed';

    /**
     * The autoborna.lead_list_pre_save event is dispatched right before a lead_list is persisted.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\LeadEvent instance.
     *
     * @var string
     */
    const LIST_PRE_SAVE = 'autoborna.lead_list_pre_save';

    /**
     * The autoborna.lead_list_post_save event is dispatched right after a lead_list is persisted.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\LeadListEvent instance.
     *
     * @var string
     */
    const LIST_POST_SAVE = 'autoborna.lead_list_post_save';

    /**
     * The autoborna.lead_list_pre_unpublish event is dispatched before a lead_list is unpublished.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\LeadListEvent instance.
     *
     * @var string
     */
    const LIST_PRE_UNPUBLISH = 'autoborna.lead_list_pre_unpublish';

    /**
     * The autoborna.lead_list_pre_delete event is dispatched before a lead_list is deleted.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\LeadListEvent instance.
     *
     * @var string
     */
    const LIST_PRE_DELETE = 'autoborna.lead_list_pre_delete';

    /**
     * The autoborna.lead_list_post_delete event is dispatched after a lead_list is deleted.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\LeadListEvent instance.
     *
     * @var string
     */
    const LIST_POST_DELETE = 'autoborna.lead_list_post_delete';

    /**
     * The autoborna.lead_field_pre_save event is dispatched right before a lead_field is persisted.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\LeadEvent instance.
     *
     * @var string
     */
    const FIELD_PRE_SAVE = 'autoborna.lead_field_pre_save';

    /**
     * The autoborna.lead_field_post_save event is dispatched right after a lead_field is persisted.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\LeadFieldEvent instance.
     *
     * @var string
     */
    const FIELD_POST_SAVE = 'autoborna.lead_field_post_save';

    /**
     * The autoborna.lead_field_pre_delete event is dispatched before a lead_field is deleted.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\LeadFieldEvent instance.
     *
     * @var string
     */
    const FIELD_PRE_DELETE = 'autoborna.lead_field_pre_delete';

    /**
     * The autoborna.lead_field_post_delete event is dispatched after a lead_field is deleted.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\LeadFieldEvent instance.
     *
     * @var string
     */
    const FIELD_POST_DELETE = 'autoborna.lead_field_post_delete';

    /**
     * The autoborna.lead_timeline_on_generate event is dispatched when generating a lead's timeline view.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\LeadTimelineEvent instance.
     *
     * @var string
     */
    const TIMELINE_ON_GENERATE = 'autoborna.lead_timeline_on_generate';

    /**
     * The autoborna.lead_note_pre_save event is dispatched right before a lead note is persisted.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\LeadEvent instance.
     *
     * @var string
     */
    const NOTE_PRE_SAVE = 'autoborna.lead_note_pre_save';

    /**
     * The autoborna.lead_note_post_save event is dispatched right after a lead note is persisted.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\LeadFieldEvent instance.
     *
     * @var string
     */
    const NOTE_POST_SAVE = 'autoborna.lead_note_post_save';

    /**
     * The autoborna.lead_note_pre_delete event is dispatched before a lead note is deleted.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\LeadFieldEvent instance.
     *
     * @var string
     */
    const NOTE_PRE_DELETE = 'autoborna.lead_note_pre_delete';

    /**
     * The autoborna.lead_note_post_delete event is dispatched after a lead note is deleted.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\LeadFieldEvent instance.
     *
     * @var string
     */
    const NOTE_POST_DELETE = 'autoborna.lead_note_post_delete';

    /**
     * The autoborna.lead_import_pre_save event is dispatched right before an import is persisted.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\ImportEvent instance.
     *
     * @var string
     */
    const IMPORT_PRE_SAVE = 'autoborna.lead_import_pre_save';

    /**
     * The autoborna.lead_import_post_save event is dispatched right after an import is persisted.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\ImportEvent instance.
     *
     * @var string
     */
    const IMPORT_POST_SAVE = 'autoborna.lead_import_post_save';

    /**
     * The autoborna.lead_import_pre_delete event is dispatched before an import is deleted.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\ImportEvent instance.
     *
     * @var string
     */
    const IMPORT_PRE_DELETE = 'autoborna.lead_import_pre_delete';

    /**
     * The autoborna.lead_import_post_delete event is dispatched after an import is deleted.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\ImportEvent instance.
     *
     * @var string
     */
    const IMPORT_POST_DELETE = 'autoborna.lead_import_post_delete';

    /**
     * The autoborna.lead_import_on_initialize event is dispatched when the import is being initialized.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\ImportInitEvent instance.
     *
     * @var string
     */
    const IMPORT_ON_INITIALIZE = 'autoborna.lead_import_on_initialize';

    /**
     * The autoborna.lead_import_on_field_mapping event is dispatched when the import needs the list of fields for mapping.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\ImportMappingEvent instance.
     *
     * @var string
     */
    const IMPORT_ON_FIELD_MAPPING = 'autoborna.lead_import_on_field_mapping';

    /**
     * The autoborna.lead_import_on_process event is dispatched when the import batch is processing.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\ImportEvent instance.
     *
     * @var string
     */
    const IMPORT_ON_PROCESS = 'autoborna.lead_import_on_process';

    /**
     * The autoborna.lead_import_on_validate event is dispatched when the import form is being validated.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\ImportEvent instance
     */
    const IMPORT_ON_VALIDATE = 'autoborna.lead_import_on_validate';

    /**
     * The autoborna.lead_import_batch_processed event is dispatched after an import batch is processed.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\ImportEvent instance.
     *
     * @var string
     */
    const IMPORT_BATCH_PROCESSED = 'autoborna.lead_import_batch_processed';

    /**
     * The autoborna.lead_device_pre_save event is dispatched right before a lead device is persisted.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\LeadEvent instance.
     *
     * @var string
     */
    const DEVICE_PRE_SAVE = 'autoborna.lead_device_pre_save';

    /**
     * The autoborna.lead_device_post_save event is dispatched right after a lead device is persisted.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\LeadFieldEvent instance.
     *
     * @var string
     */
    const DEVICE_POST_SAVE = 'autoborna.lead_device_post_save';

    /**
     * The autoborna.lead_device_pre_delete event is dispatched before a lead device is deleted.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\LeadFieldEvent instance.
     *
     * @var string
     */
    const DEVICE_PRE_DELETE = 'autoborna.lead_device_pre_delete';

    /**
     * The autoborna.lead_device_post_delete event is dispatched after a lead device is deleted.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\LeadFieldEvent instance.
     *
     * @var string
     */
    const DEVICE_POST_DELETE = 'autoborna.lead_device_post_delete';

    /**
     * The autoborna.lead_tag_pre_save event is dispatched right before a lead tag is persisted.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\TagEvent instance.
     *
     * @var string
     */
    const TAG_PRE_SAVE = 'autoborna.lead_tag_pre_save';

    /**
     * The autoborna.lead_tag_post_save event is dispatched right after a lead tag is persisted.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\TagEvent instance.
     *
     * @var string
     */
    const TAG_POST_SAVE = 'autoborna.lead_tag_post_save';

    /**
     * The autoborna.lead_tag_pre_delete event is dispatched before a lead tag is deleted.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\TagEvent instance.
     *
     * @var string
     */
    const TAG_PRE_DELETE = 'autoborna.lead_tag_pre_delete';

    /**
     * The autoborna.lead_tag_post_delete event is dispatched after a lead tag is deleted.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\TagEvent instance.
     *
     * @var string
     */
    const TAG_POST_DELETE = 'autoborna.lead_tag_post_delete';

    /**
     * The autoborna.filter_choice_fields event is dispatched when the list filter dropdown is populated.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\FilterChoiceEvent
     *
     * @var string
     */
    const FILTER_CHOICE_FIELDS = 'autoborna.filter_choice_fields';

    /**
     * The autoborna.lead.on_campaign_trigger_action event is fired when the campaign action triggers.
     *
     * The event listener receives a
     * Autoborna\CampaignBundle\Event\CampaignExecutionEvent
     *
     * @var string
     */
    const ON_CAMPAIGN_TRIGGER_ACTION = 'autoborna.lead.on_campaign_trigger_action';

    /**
     * The autoborna.lead.on_campaign_action_delete_contact event is dispatched when the campaign action to delete a contact is executed.
     *
     * The event listener receives a Autoborna\CampaignBundle\Event\PendingEvent
     *
     * @var string
     */
    const ON_CAMPAIGN_ACTION_DELETE_CONTACT = 'autoborna.lead.on_campaign_action_delete_contact';

    /**
     * The autoborna.lead.on_campaign_action_add_donotcontact event is dispatched when the campaign action to add a donotcontact is executed.
     *
     * The event listener receives a Autoborna\CampaignBundle\Event\PendingEvent
     *
     * @var string
     */
    const ON_CAMPAIGN_ACTION_ADD_DONOTCONTACT = 'autoborna.lead.on_campaign_action_add_donotcontact';

    /**
     * The autoborna.lead.on_campaign_action_remove_donotcontact event is dispatched when the campaign action to remove a donotcontact is executed.
     *
     * The event listener receives a Autoborna\CampaignBundle\Event\PendingEvent
     *
     * @var string
     */
    const ON_CAMPAIGN_ACTION_REMOVE_DONOTCONTACT = 'autoborna.lead.on_campaign_action_remove_donotcontact';

    /**
     * The autoborna.lead.on_campaign_trigger_condition event is fired when the campaign condition triggers.
     *
     * The event listener receives a
     * Autoborna\CampaignBundle\Event\CampaignExecutionEvent
     *
     * @var string
     */
    const ON_CAMPAIGN_TRIGGER_CONDITION = 'autoborna.lead.on_campaign_trigger_condition';

    /**
     * The autoborna.company_pre_save event is thrown right before a form is persisted.
     *
     * The event listener receives a Autoborna\LeadBundle\Event\CompanyEvent instance.
     *
     * @var string
     */
    const COMPANY_PRE_SAVE = 'autoborna.company_pre_save';

    /**
     * The autoborna.company_post_save event is thrown right after a form is persisted.
     *
     * The event listener receives a Autoborna\LeadBundle\Event\CompanyEvent instance.
     *
     * @var string
     */
    const COMPANY_POST_SAVE = 'autoborna.company_post_save';

    /**
     * The autoborna.company_pre_delete event is thrown before a form is deleted.
     *
     * The event listener receives a Autoborna\LeadBundle\Event\CompanyEvent instance.
     *
     * @var string
     */
    const COMPANY_PRE_DELETE = 'autoborna.company_pre_delete';

    /**
     * The autoborna.company_post_delete event is thrown after a form is deleted.
     *
     * The event listener receives a Autoborna\LeadBundle\Event\CompanyEvent instance.
     *
     * @var string
     */
    const COMPANY_POST_DELETE = 'autoborna.company_post_delete';

    /**
     * The autoborna.list_filters_choices_on_generate event is dispatched when the choices for list filters are generated.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\LeadListFiltersChoicesEvent instance.
     *
     * @var string
     */
    const LIST_FILTERS_CHOICES_ON_GENERATE = 'autoborna.list_filters_choices_on_generate';

    /**
     * The event is dispatched to allow inserting segment filters translations.
     *
     * The listener receives SegmentDictionaryGenerationEvent
     */
    const SEGMENT_DICTIONARY_ON_GENERATE = 'autoborna.list_dictionary_on_generate';

    /**
     * The autoborna.list_filters_operators_on_generate event is dispatched when the operators for list filters are generated.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\LeadListFiltersOperatorsEvent instance.
     *
     * @var string
     */
    const LIST_FILTERS_OPERATORS_ON_GENERATE = 'autoborna.list_filters_operators_on_generate';

    /**
     * The autoborna.collect_filter_choices_for_list_field_type event is dispatched when some filter based on a list type needs to load its choices.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\ListFieldChoicesEvent
     *
     * @var string
     */
    const COLLECT_FILTER_CHOICES_FOR_LIST_FIELD_TYPE = 'autoborna.collect_filter_choices_for_list_field_type';

    /**
     * The autoborna.collect_operators_for_field_type event is dispatched when some filter needs operators for a field type.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\TypeOperatorsEvent
     *
     * @var string
     */
    const COLLECT_OPERATORS_FOR_FIELD_TYPE = 'autoborna.collect_operators_for_field_type';

    /**
     * The autoborna.collect_operators_for_field event is dispatched when some filter needs operators for a specific field.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\TypeOperatorsEvent
     *
     * @var string
     */
    const COLLECT_OPERATORS_FOR_FIELD = 'autoborna.collect_operators_for_field';

    /**
     * The autoborna.adjust_filter_form_type_for_field event is dispatched when the segment filter form is built so events can add new or modify existing fields.
     *
     * The event listener receives a
     * Symfony\Component\Form\FormEvent
     *
     * @var string
     */
    const ADJUST_FILTER_FORM_TYPE_FOR_FIELD = 'autoborna.adjust_filter_form_type_for_field';

    /**
     * The autoborna.list_filters_delegate_decorator event id dispatched when decorator is delegated for segment filter.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\LeadListFiltersDecoratorDelegateEvent instance.
     */
    const SEGMENT_ON_DECORATOR_DELEGATE = 'autoborna.list_filters_delegate_decorator';

    /**
     * The autoborna.list_filters_on_filtering event is dispatched when the lists are updated.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\LeadListFilteringEvent instance.
     *
     * @var string
     */
    const LIST_FILTERS_ON_FILTERING = 'autoborna.list_filters_on_filtering';

    /**
     * The autoborna.list_filters_querybuilder_generated event is dispatched when the queryBuilder for segment was generated.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\LeadListQueryBuilderGeneratedEvent instance.
     *
     * @var string
     */
    const LIST_FILTERS_QUERYBUILDER_GENERATED = 'autoborna.list_filters_querybuilder_generated';

    /**
     * The autoborna.list_filters_operator_querybuilder_on_generate event is dispatched when the queryBuilder for segment filter operators is being generated.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\SegmentOperatorQueryBuilderEvent instance.
     *
     * @var string
     */
    const LIST_FILTERS_OPERATOR_QUERYBUILDER_ON_GENERATE = 'autoborna.list_filters_operator_querybuilder_on_generate';

    /**
     * The autoborna.list_filters_on_filtering event is dispatched when the lists are updated.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Event\LeadListFilteringEvent instance.
     *
     * @var string
     */
    const LIST_PRE_PROCESS_LIST = 'autoborna.list_pre_process_list';

    /**
     * The autoborna.clickthrough_contact_identification event is dispatched when a clickthrough array is parsed from a tracking
     * URL.
     *
     * The event listener receives a Autoborna\LeadBundle\Event\ContactIdentificationEvent instance.
     *
     * @var string
     */
    const ON_CLICKTHROUGH_IDENTIFICATION = 'autoborna.clickthrough_contact_identification';

    /**
     * The autoborna.lead_field_pre_add_column event is dispatched before adding a new column to lead_fields table.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Field\Event\AddColumnEvent instance.
     *
     * @var string
     */
    const LEAD_FIELD_PRE_ADD_COLUMN = 'autoborna.lead_field_pre_add_column';

    /**
     * The autoborna.lead_field_pre_add_column_background_job event is dispatched before adding a new column to lead_fields table
     * in background job.
     *
     * The event listener receives a
     * Autoborna\LeadBundle\Field\Event\AddColumnBackgroundEvent instance.
     *
     * @var string
     */
    const LEAD_FIELD_PRE_ADD_COLUMN_BACKGROUND_JOB = 'autoborna.lead_field_pre_add_column_background_job';
}
