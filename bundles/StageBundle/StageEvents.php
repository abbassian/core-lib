<?php

namespace Autoborna\StageBundle;

/**
 * Class StageEvents.
 *
 * Events available for StageBundle
 */
final class StageEvents
{
    /**
     * The autoborna.stage_pre_save event is thrown right before a form is persisted.
     *
     * The event listener receives a Autoborna\StageBundle\Event\StageEvent instance.
     *
     * @var string
     */
    const STAGE_PRE_SAVE = 'autoborna.stage_pre_save';

    /**
     * The autoborna.stage_post_save event is thrown right after a form is persisted.
     *
     * The event listener receives a Autoborna\StageBundle\Event\StageEvent instance.
     *
     * @var string
     */
    const STAGE_POST_SAVE = 'autoborna.stage_post_save';

    /**
     * The autoborna.stage_pre_delete event is thrown before a form is deleted.
     *
     * The event listener receives a Autoborna\StageBundle\Event\StageEvent instance.
     *
     * @var string
     */
    const STAGE_PRE_DELETE = 'autoborna.stage_pre_delete';

    /**
     * The autoborna.stage_post_delete event is thrown after a form is deleted.
     *
     * The event listener receives a Autoborna\StageBundle\Event\StageEvent instance.
     *
     * @var string
     */
    const STAGE_POST_DELETE = 'autoborna.stage_post_delete';

    /**
     * The autoborna.stage_on_build event is thrown before displaying the stage builder form to allow adding of custom actions.
     *
     * The event listener receives a Autoborna\StageBundle\Event\StageBuilderEvent instance.
     *
     * @var string
     */
    const STAGE_ON_BUILD = 'autoborna.stage_on_build';

    /**
     * The autoborna.stage_on_action event is thrown to execute a stage action.
     *
     * The event listener receives a Autoborna\StageBundle\Event\StageActionEvent instance.
     *
     * @var string
     */
    const STAGE_ON_ACTION = 'autoborna.stage_on_action';

    /**
     * The autoborna.stage.on_campaign_batch_action event is dispatched when the campaign action triggers.
     *
     * The event listener receives a Autoborna\CampaignBundle\Event\PendingEvent
     *
     * @var string
     */
    const ON_CAMPAIGN_BATCH_ACTION = 'autoborna.stage.on_campaign_batch_action';

    /**
     * @deprecated; use ON_CAMPAIGN_BATCH_ACTION instead
     *
     * The autoborna.stage.on_campaign_trigger_action event is fired when the campaign action triggers.
     *
     * The event listener receives a
     * Autoborna\CampaignBundle\Event\CampaignExecutionEvent
     *
     * @var string
     */
    const ON_CAMPAIGN_TRIGGER_ACTION = 'autoborna.stage.on_campaign_trigger_action';
}
