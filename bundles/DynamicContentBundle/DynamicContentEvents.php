<?php

namespace Autoborna\DynamicContentBundle;

/**
 * Class DynamicContentEvents
 * Events available for DynamicContentBundle.
 */
final class DynamicContentEvents
{
    /**
     * The autoborna.dwc_token_replacement event is thrown right before the content is returned.
     *
     * The event listener receives a
     * Autoborna\CoreBundle\Event\TokenReplacementEvent instance.
     *
     * @var string
     */
    const TOKEN_REPLACEMENT = 'autoborna.dwc_token_replacement';

    /**
     * The autoborna.dwc_pre_save event is thrown right before a asset is persisted.
     *
     * The event listener receives a
     * Autoborna\DynamicContentBundle\Event\DynamicContentEvent instance.
     *
     * @var string
     */
    const PRE_SAVE = 'autoborna.dwc_pre_save';

    /**
     * The autoborna.dwc_post_save event is thrown right after a asset is persisted.
     *
     * The event listener receives a
     * Autoborna\DynamicContentBundle\Event\DynamicContentEvent instance.
     *
     * @var string
     */
    const POST_SAVE = 'autoborna.dwc_post_save';

    /**
     * The autoborna.dwc_pre_delete event is thrown prior to when a asset is deleted.
     *
     * The event listener receives a
     * Autoborna\DynamicContentBundle\Event\DynamicContentEvent instance.
     *
     * @var string
     */
    const PRE_DELETE = 'autoborna.dwc_pre_delete';

    /**
     * The autoborna.dwc_post_delete event is thrown after a asset is deleted.
     *
     * The event listener receives a
     * Autoborna\DynamicContentBundle\Event\DynamicContentEvent instance.
     *
     * @var string
     */
    const POST_DELETE = 'autoborna.dwc_post_delete';

    /**
     * The autoborna.category_pre_save event is thrown right before a category is persisted.
     *
     * The event listener receives a
     * Autoborna\CategoryBundle\Event\CategoryEvent instance.
     *
     * @var string
     */
    const CATEGORY_PRE_SAVE = 'autoborna.category_pre_save';

    /**
     * The autoborna.category_post_save event is thrown right after a category is persisted.
     *
     * The event listener receives a
     * Autoborna\CategoryBundle\Event\CategoryEvent instance.
     *
     * @var string
     */
    const CATEGORY_POST_SAVE = 'autoborna.category_post_save';

    /**
     * The autoborna.category_pre_delete event is thrown prior to when a category is deleted.
     *
     * The event listener receives a
     * Autoborna\CategoryBundle\Event\CategoryEvent instance.
     *
     * @var string
     */
    const CATEGORY_PRE_DELETE = 'autoborna.category_pre_delete';

    /**
     * The autoborna.category_post_delete event is thrown after a category is deleted.
     *
     * The event listener receives a
     * Autoborna\CategoryBundle\Event\CategoryEvent instance.
     *
     * @var string
     */
    const CATEGORY_POST_DELETE = 'autoborna.category_post_delete';

    /**
     * The autoborna.asset.on_campaign_trigger_decision event is fired when the campaign decision triggers.
     *
     * The event listener receives a
     * Autoborna\CampaignBundle\Event\CampaignExecutionEvent
     *
     * @var string
     */
    const ON_CAMPAIGN_TRIGGER_DECISION = 'autoborna.dwc.on_campaign_trigger_decision';

    /**
     * The autoborna.asset.on_campaign_trigger_action event is fired when the campaign action triggers.
     *
     * The event listener receives a
     * Autoborna\CampaignBundle\Event\CampaignExecutionEvent
     *
     * @var string
     */
    const ON_CAMPAIGN_TRIGGER_ACTION = 'autoborna.dwc.on_campaign_trigger_action';

    /**
     * The autoborna.dwc.on_contact_filters_evaluate event is fired when dynamic content's decision's
     * filters need to be evaluated.
     *
     * The event listener receives a
     * Autoborna\DynamicContentBundle\Event\ContactFiltersEvaluateEvent
     *
     * @var string
     */
    const ON_CONTACTS_FILTER_EVALUATE = 'autoborna.dwc.on_contact_filters_evaluate';
}
