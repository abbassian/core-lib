<?php

namespace Autoborna\AssetBundle;

/**
 * Events available for AssetBundle.
 */
final class AssetEvents
{
    /**
     * The autoborna.asset_on_load event is dispatched when a public asset is downloaded, publicly viewed, or redirected to (remote).
     *
     * The event listener receives a
     * Autoborna\AssetBundle\Event\AssetLoadEvent instance.
     *
     * @var string
     */
    const ASSET_ON_LOAD = 'autoborna.asset_on_load';

    /**
     * The autoborna.asset_on_remote_browse event is dispatched when browsing a remote provider.
     *
     * The event listener receives a
     * Autoborna\AssetBundle\Event\RemoteAssetBrowseEvent instance.
     *
     * @var string
     */
    const ASSET_ON_REMOTE_BROWSE = 'autoborna.asset_on_remote_browse';

    /**
     * The autoborna.asset_on_upload event is dispatched before uploading a file.
     *
     * The event listener receives a
     * Autoborna\AssetBundle\Event\AssetEvent instance.
     *
     * @var string
     */
    const ASSET_ON_UPLOAD = 'autoborna.asset_on_upload';

    /**
     * The autoborna.asset_pre_save event is dispatched right before a asset is persisted.
     *
     * The event listener receives a
     * Autoborna\AssetBundle\Event\AssetEvent instance.
     *
     * @var string
     */
    const ASSET_PRE_SAVE = 'autoborna.asset_pre_save';

    /**
     * The autoborna.asset_post_save event is dispatched right after a asset is persisted.
     *
     * The event listener receives a
     * Autoborna\AssetBundle\Event\AssetEvent instance.
     *
     * @var string
     */
    const ASSET_POST_SAVE = 'autoborna.asset_post_save';

    /**
     * The autoborna.asset_pre_delete event is dispatched prior to when a asset is deleted.
     *
     * The event listener receives a
     * Autoborna\AssetBundle\Event\AssetEvent instance.
     *
     * @var string
     */
    const ASSET_PRE_DELETE = 'autoborna.asset_pre_delete';

    /**
     * The autoborna.asset_post_delete event is dispatched after a asset is deleted.
     *
     * The event listener receives a
     * Autoborna\AssetBundle\Event\AssetEvent instance.
     *
     * @var string
     */
    const ASSET_POST_DELETE = 'autoborna.asset_post_delete';

    /**
     * The autoborna.asset.on_campaign_trigger_decision event is fired when the campaign action triggers.
     *
     * The event listener receives a
     * Autoborna\CampaignBundle\Event\CampaignExecutionEvent
     *
     * @var string
     */
    const ON_CAMPAIGN_TRIGGER_DECISION = 'autoborna.asset.on_campaign_trigger_decision';

    /**
     * The autoborna.asset.on_download_rate_winner event is fired when there is a need to determine download rate winner.
     *
     * The event listener receives a
     * Autoborna\CoreBundles\Event\DetermineWinnerEvent
     *
     * @var string
     */
    const ON_DETERMINE_DOWNLOAD_RATE_WINNER = 'autoborna.asset.on_download_rate_winner';
}
