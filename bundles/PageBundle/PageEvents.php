<?php

namespace Autoborna\PageBundle;

/**
 * Class PageEvents.
 *
 * Events available for PageBundle
 */
final class PageEvents
{
    /**
     * The autoborna.video_on_hit event is thrown when a public page is browsed and a hit recorded in the analytics table.
     *
     * The event listener receives a Autoborna\PageBundle\Event\VideoHitEvent instance.
     *
     * @var string
     */
    const VIDEO_ON_HIT = 'autoborna.video_on_hit';

    /**
     * The autoborna.page_on_hit event is thrown when a public page is browsed and a hit recorded in the analytics table.
     *
     * The event listener receives a Autoborna\PageBundle\Event\PageHitEvent instance.
     *
     * @var string
     */
    const PAGE_ON_HIT = 'autoborna.page_on_hit';

    /**
     * The autoborna.page_on_build event is thrown before displaying the page builder form to allow adding of tokens.
     *
     * The event listener receives a Autoborna\PageBundle\Event\PageEvent instance.
     *
     * @var string
     */
    const PAGE_ON_BUILD = 'autoborna.page_on_build';

    /**
     * The autoborna.page_on_display event is thrown before displaying the page content.
     *
     * The event listener receives a Autoborna\PageBundle\Event\PageDisplayEvent instance.
     *
     * @var string
     */
    const PAGE_ON_DISPLAY = 'autoborna.page_on_display';

    /**
     * The autoborna.page_pre_save event is thrown right before a page is persisted.
     *
     * The event listener receives a Autoborna\PageBundle\Event\PageEvent instance.
     *
     * @var string
     */
    const PAGE_PRE_SAVE = 'autoborna.page_pre_save';

    /**
     * The autoborna.page_post_save event is thrown right after a page is persisted.
     *
     * The event listener receives a Autoborna\PageBundle\Event\PageEvent instance.
     *
     * @var string
     */
    const PAGE_POST_SAVE = 'autoborna.page_post_save';

    /**
     * The autoborna.page_pre_delete event is thrown prior to when a page is deleted.
     *
     * The event listener receives a Autoborna\PageBundle\Event\PageEvent instance.
     *
     * @var string
     */
    const PAGE_PRE_DELETE = 'autoborna.page_pre_delete';

    /**
     * The autoborna.page_post_delete event is thrown after a page is deleted.
     *
     * The event listener receives a Autoborna\PageBundle\Event\PageEvent instance.
     *
     * @var string
     */
    const PAGE_POST_DELETE = 'autoborna.page_post_delete';

    /**
     * The autoborna.redirect_do_not_track event is thrown when converting email links to trackables/redirectables in order to compile of list of tokens/URLs
     * to ignore.
     *
     * The event listener receives a Autoborna\PageBundle\Event\UntrackableUrlsEvent instance.
     *
     * @var string
     */
    const REDIRECT_DO_NOT_TRACK = 'autoborna.redirect_do_not_track';

    /**
     * The autoborna.page.on_campaign_trigger_decision event is fired when the campaign decision triggers.
     *
     * The event listener receives a
     * Autoborna\CampaignBundle\Event\CampaignExecutionEvent
     *
     * @var string
     */
    const ON_CAMPAIGN_TRIGGER_DECISION = 'autoborna.page.on_campaign_trigger_decision';

    /**
     * The autoborna.page.on_campaign_trigger_action event is fired when the campaign action fired.
     *
     * The event listener receives a
     * Autoborna\CampaignBundle\Event\CampaignExecutionEvent
     *
     * @var string
     */
    const ON_CAMPAIGN_TRIGGER_ACTION = 'autoborna.page.on_campaign_trigger_action';

    /**
     * The autoborna.page.on_redirect_generate event is fired when generating a redirect.
     *
     * The event listener receives a
     * Autoborna\PageBundle\Event\RedirectGenerationEvent
     */
    const ON_REDIRECT_GENERATE = 'autoborna.page.on_redirect_generate';

    /**
     * The autoborna.page.on_bounce_rate_winner event is fired when there is a need to determine bounce rate winner.
     *
     * The event listener receives a
     * Autoborna\CoreBundle\Event\DetermineWinnerEvent
     *
     * @var string
     */
    const ON_DETERMINE_BOUNCE_RATE_WINNER = 'autoborna.page.on_bounce_rate_winner';

    /**
     * The autoborna.page.on_dwell_time_winner event is fired when there is a need to determine a winner based on dwell time.
     *
     * The event listener receives a
     * Autoborna\CoreBundles\Event\DetermineWinnerEvent
     *
     * @var string
     */
    const ON_DETERMINE_DWELL_TIME_WINNER = 'autoborna.page.on_dwell_time_winner';

    /**
     * The autoborna.page.on_contact_tracked event is dispatched when a contact is tracked via the mt() tracking event.
     *
     * The event listener receives a
     * Autoborna\PageBundle\Event\TrackingEvent
     */
    const ON_CONTACT_TRACKED = 'autoborna.page.on_contact_tracked';
}
