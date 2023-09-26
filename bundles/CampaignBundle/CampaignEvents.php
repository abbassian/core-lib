<?php

namespace Autoborna\CampaignBundle;

/**
 * Class CampaignEvents
 * Events available for CampaignBundle.
 */
final class CampaignEvents
{
    /**
     * The autoborna.campaign_pre_save event is dispatched right before a form is persisted.
     *
     * The event listener receives a
     * Autoborna\CampaignBundle\Event\CampaignEvent instance.
     *
     * @var string
     */
    const CAMPAIGN_PRE_SAVE = 'autoborna.campaign_pre_save';

    /**
     * The autoborna.campaign_post_save event is dispatched right after a form is persisted.
     *
     * The event listener receives a
     * Autoborna\CampaignBundle\Event\CampaignEvent instance.
     *
     * @var string
     */
    const CAMPAIGN_POST_SAVE = 'autoborna.campaign_post_save';

    /**
     * The autoborna.campaign_pre_delete event is dispatched before a form is deleted.
     *
     * The event listener receives a
     * Autoborna\CampaignBundle\Event\CampaignEvent instance.
     *
     * @var string
     */
    const CAMPAIGN_PRE_DELETE = 'autoborna.campaign_pre_delete';

    /**
     * The autoborna.campaign_post_delete event is dispatched after a form is deleted.
     *
     * The event listener receives a
     * Autoborna\CampaignBundle\Event\CampaignEvent instance.
     *
     * @var string
     */
    const CAMPAIGN_POST_DELETE = 'autoborna.campaign_post_delete';

    /**
     * The autoborna.campaign_on_build event is dispatched before displaying the campaign builder form to allow adding of custom actions.
     *
     * The event listener receives a
     * Autoborna\CampaignBundle\Event\CampaignBuilderEvent instance.
     *
     * @var string
     */
    const CAMPAIGN_ON_BUILD = 'autoborna.campaign_on_build';

    /**
     * The autoborna.campaign_on_trigger event is dispatched from the autoborna:campaign:trigger command.
     *
     * The event listener receives a
     * Autoborna\CampaignBundle\Event\CampaignTriggerEvent instance.
     *
     * @var string
     */
    const CAMPAIGN_ON_TRIGGER = 'autoborna.campaign_on_trigger';

    /**
     * The autoborna.campaign_on_leadchange event is dispatched when a lead was added or removed from the campaign.
     *
     * The event listener receives a
     * Autoborna\CampaignBundle\Event\CampaignLeadChangeEvent instance.
     *
     * @var string
     */
    const CAMPAIGN_ON_LEADCHANGE = 'autoborna.campaign_on_leadchange';

    /**
     * The autoborna.campaign_on_leadchange event is dispatched if a batch of leads are changed from CampaignModel::rebuildCampaignLeads().
     *
     * The event listener receives a
     * Autoborna\CampaignBundle\Event\CampaignLeadChangeEvent instance.
     *
     * @var string
     */
    const LEAD_CAMPAIGN_BATCH_CHANGE = 'autoborna.lead_campaign_batch_change';

    /**
     * The autoborna.campaign_on_event_executed event is dispatched when a campaign event is executed.
     *
     * The event listener receives a Autoborna\CampaignBundle\Event\ExecutedEvent instance.
     *
     * @var string
     */
    const ON_EVENT_EXECUTED = 'autoborna.campaign_on_event_executed';

    /**
     * The autoborna.campaign_on_event_executed_batch event is dispatched when a batch of campaign events are executed.
     *
     * The event listener receives a Autoborna\CampaignBundle\Event\ExecutedBatchEvent instance.
     *
     * @var string
     */
    const ON_EVENT_EXECUTED_BATCH = 'autoborna.campaign_on_event_executed_batch';

    /**
     * The autoborna.campaign_on_event_scheduled event is dispatched when a campaign event is scheduled or scheduling is modified.
     *
     * The event listener receives a Autoborna\CampaignBundle\Event\ScheduledEvent instance.
     *
     * @var string
     */
    const ON_EVENT_SCHEDULED = 'autoborna.campaign_on_event_scheduled';

    /**
     * The autoborna.campaign_on_event_scheduled_batch event is dispatched when a batch of events are scheduled at once.
     *
     * The event listener receives a Autoborna\CampaignBundle\Event\ScheduledBatchEvent instance.
     *
     * @var string
     */
    const ON_EVENT_SCHEDULED_BATCH = 'autoborna.campaign_on_event_scheduled_batch';

    /**
     * The autoborna.campaign_on_event_failed event is dispatched when an event fails for whatever reason.
     *
     * The event listener receives a Autoborna\CampaignBundle\Event\FailedEvent instance.
     *
     * @var string
     */
    const ON_EVENT_FAILED = 'autoborna.campaign_on_event_failed';

    /**
     * The autoborna.campaign_on_event_decision_evaluation event is dispatched when a campaign decision is to be evaluated.
     *
     * The event listener receives a Autoborna\CampaignBundle\Event\DecisionEvent instance.
     *
     * @var string
     */
    const ON_EVENT_DECISION_EVALUATION = 'autoborna.campaign_on_event_decision_evaluation';

    /**
     * The autoborna.campaign_on_event_decision_evaluation_results event is dispatched when a batch of contacts were evaluted for a decision.
     *
     * The event listener receives a Autoborna\CampaignBundle\Event\DecisionBatchEvent instance.
     *
     * @var string
     */
    const ON_EVENT_DECISION_EVALUATION_RESULTS = 'autoborna.campaign_on_event_decision_evaluation_results';

    /**
     * The autoborna.campaign_on_event_decision_evaluation event is dispatched when a campaign decision is to be evaluated.
     *
     * The event listener receives a Autoborna\CampaignBundle\Event\DecisionEvent instance.
     *
     * @var string
     */
    const ON_EVENT_CONDITION_EVALUATION = 'autoborna.campaign_on_event_decision_evaluation';

    /**
     * The autoborna.campaign_on_event_jump_to_event event is dispatched when a campaign jump to event is triggered.
     *
     * The event listener receives a Autoborna\CampaignBundle\Event\PendingEvent instance.
     *
     * @var string
     */
    const ON_EVENT_JUMP_TO_EVENT = 'autoborna.campaign_on_event_jump_to_event';

    /**
     * The autoborna.lead.on_campaign_action_change_membership event is dispatched when the campaign action to change a contact's membership is executed.
     *
     * The event listener receives a Autoborna\CampaignBundle\Event\PendingEvent
     *
     * @var string
     */
    const ON_CAMPAIGN_ACTION_CHANGE_MEMBERSHIP = 'autoborna.lead.on_campaign_action_change_membership';

    /**
     * @deprecated 2.13.0; to be removed in 3.0. Listen to ON_EVENT_EXECUTED and ON_EVENT_FAILED
     *
     * The autoborna.campaign_on_event_execution event is dispatched when a campaign event is executed.
     *
     * The event listener receives a Autoborna\CampaignBundle\Event\CampaignExecutionEvent instance.
     *
     * @var string
     */
    const ON_EVENT_EXECUTION = 'autoborna.campaign_on_event_execution';

    /**
     * @deprecated 2.13.0; to be removed in 3.0; Listen to ON_EVENT_DECISION_EVALUATION instead
     *
     * The autoborna.campaign_on_event_decision_trigger event is dispatched after a lead decision triggers a set of actions or if the decision is set
     * as a root level event.
     *
     * The event listener receives a Autoborna\CampaignBundle\Event\CampaignDecisionEvent instance.
     *
     * @var string
     */
    const ON_EVENT_DECISION_TRIGGER = 'autoborna.campaign_on_event_decision_trigger';
}
