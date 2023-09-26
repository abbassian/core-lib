<?php

namespace Autoborna\NotificationBundle;

/**
 * Class NotificationEvents
 * Events available for NotificationBundle.
 */
final class NotificationEvents
{
    /**
     * The autoborna.notification_token_replacement event is thrown right before the content is returned.
     *
     * The event listener receives a
     * Autoborna\CoreBundle\Event\TokenReplacementEvent instance.
     *
     * @var string
     */
    const TOKEN_REPLACEMENT = 'autoborna.notification_token_replacement';

    /**
     * The autoborna.notification_form_action_send event is thrown when a notification is sent
     * as part of a form action.
     *
     * The event listener receives a
     * Autoborna\NotificationBundle\Event\SendingNotificationEvent instance.
     *
     * @var string
     */
    const NOTIFICATION_ON_FORM_ACTION_SEND = 'autoborna.notification_form_action_send';

    /**
     * The autoborna.notification_on_send event is thrown when a notification is sent.
     *
     * The event listener receives a
     * Autoborna\NotificationBundle\Event\NotificationSendEvent instance.
     *
     * @var string
     */
    const NOTIFICATION_ON_SEND = 'autoborna.notification_on_send';

    /**
     * The autoborna.notification_pre_save event is thrown right before a notification is persisted.
     *
     * The event listener receives a
     * Autoborna\NotificationBundle\Event\NotificationEvent instance.
     *
     * @var string
     */
    const NOTIFICATION_PRE_SAVE = 'autoborna.notification_pre_save';

    /**
     * The autoborna.notification_post_save event is thrown right after a notification is persisted.
     *
     * The event listener receives a
     * Autoborna\NotificationBundle\Event\NotificationEvent instance.
     *
     * @var string
     */
    const NOTIFICATION_POST_SAVE = 'autoborna.notification_post_save';

    /**
     * The autoborna.notification_pre_delete event is thrown prior to when a notification is deleted.
     *
     * The event listener receives a
     * Autoborna\NotificationBundle\Event\NotificationEvent instance.
     *
     * @var string
     */
    const NOTIFICATION_PRE_DELETE = 'autoborna.notification_pre_delete';

    /**
     * The autoborna.notification_post_delete event is thrown after a notification is deleted.
     *
     * The event listener receives a
     * Autoborna\NotificationBundle\Event\NotificationEvent instance.
     *
     * @var string
     */
    const NOTIFICATION_POST_DELETE = 'autoborna.notification_post_delete';

    /**
     * The autoborna.notification.on_campaign_trigger_action event is fired when the campaign action triggers.
     *
     * The event listener receives a
     * Autoborna\CampaignBundle\Event\CampaignExecutionEvent
     *
     * @var string
     */
    const ON_CAMPAIGN_TRIGGER_ACTION = 'autoborna.notification.on_campaign_trigger_action';

    /**
     * The autoborna.notification.on_campaign_trigger_condition event is fired when the campaign condition triggers.
     *
     * The event listener receives a
     * Autoborna\CampaignBundle\Event\CampaignExecutionEvent
     *
     * @var string
     */
    const ON_CAMPAIGN_TRIGGER_CONDITION = 'autoborna.notification.on_campaign_trigger_notification';
}
