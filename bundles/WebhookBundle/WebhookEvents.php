<?php

namespace Autoborna\WebhookBundle;

/**
 * Class AutobornaWebhookEvents
 * Events available for AutobornaWebhookBundle.
 */
final class WebhookEvents
{
    /**
     * The autoborna.webhook_pre_save event is thrown right before a form is persisted.
     *
     * The event listener receives a Autoborna\WebhookBundle\Event\WebhookBundleEvent instance.
     *
     * @var string
     */
    const WEBHOOK_PRE_SAVE = 'autoborna.webhook_pre_save';

    /**
     * The autoborna.webhook_post_save event is thrown right after a form is persisted.
     *
     * The event listener receives a Autoborna\WebhookBundle\Event\WebhookBundleEvent instance.
     *
     * @var string
     */
    const WEBHOOK_POST_SAVE = 'autoborna.webhook_post_save';

    /**
     * The autoborna.webhook_pre_delete event is thrown before a form is deleted.
     *
     * The event listener receives a Autoborna\WebhookBundle\Event\WebhookBundleEvent instance.
     *
     * @var string
     */
    const WEBHOOK_PRE_DELETE = 'autoborna.webhook_pre_delete';

    /**
     * The autoborna.webhook_post_delete event is thrown after a form is deleted.
     *
     * The event listener receives a Autoborna\WebhookBundle\Event\WebhookBundleEvent instance.
     *
     * @var string
     */
    const WEBHOOK_POST_DELETE = 'autoborna.webhook_post_delete';

    /**
     * The autoborna.webhook_kill event is thrown when target is not available.
     *
     * The event listener receives a Autoborna\WebhookBundle\Event\WebhookEvent instance.
     *
     * @var string
     */
    const WEBHOOK_KILL = 'autoborna.webhook_kill';

    /**
     * The autoborna.webhook_queue_on_add event is thrown as the queue entity is created, before it is persisted to the database.
     *
     * The event listener receives a Autoborna\WebhookBundle\Event\WebhookQueueEvent instance.
     *
     * @var string
     */
    const WEBHOOK_QUEUE_ON_ADD = 'autoborna.webhook_queue_on_add';

    /**
     * The autoborna.webhook_pre_execute event is thrown right before a webhook URL is executed.
     *
     * The event listener receives a Autoborna\WebhookBundle\Event\WebhookExecuteEvent instance.
     *
     * @var string
     */
    const WEBHOOK_PRE_EXECUTE = 'autoborna.webhook_pre_execute';

    /**
     * The autoborna.webhook_post_execute event is thrown right after a webhook URL is executed.
     *
     * The event listener receives a Autoborna\WebhookBundle\Event\WebhookExecuteEvent instance.
     *
     * @var string
     */
    const WEBHOOK_POST_EXECUTE = 'autoborna.webhook_post_execute';

    /**
     * The autoborna.webhook_on_build event is as the webhook form is built.
     *
     * The event listener receives a Autoborna\WebhookBundle\Event\WebhookBuild instance.
     *
     * @var string
     */
    const WEBHOOK_ON_BUILD = 'autoborna.webhook_on_build';

    /**
     * The autoborna.webhook.campaign_on_trigger event is dispatched from the autoborna:campaign:trigger command.
     *
     * The event listener receives a
     * Autoborna\CampaignBundle\Event\CampaignTriggerEvent instance.
     *
     * @var string
     */
    const ON_CAMPAIGN_TRIGGER_ACTION = 'autoborna.webhook.campaign_on_trigger_action';

    /**
     * The autoborna.webhook_on_request event is fired before request is processed.
     *
     * The event listener receives a Autoborna\WebhookBundle\Event\WebhookRequestEvent instance.
     *
     * @var string
     */
    const WEBHOOK_ON_REQUEST = 'autoborna.webhook_on_request';
}
