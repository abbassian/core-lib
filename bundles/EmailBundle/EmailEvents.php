<?php

namespace Autoborna\EmailBundle;

/**
 * Class EmailEvents
 * Events available for EmailBundle.
 */
final class EmailEvents
{
    /**
     * The autoborna.email_token_replacement event is thrown right before the content is returned.
     *
     * The event listener receives a
     * Autoborna\CoreBundle\Event\TokenReplacementEvent instance.
     *
     * @var string
     */
    const TOKEN_REPLACEMENT = 'autoborna.email_token_replacement';

    /**
     * The autoborna.email_address_token_replacement event is thrown right before a email address token needs replacement.
     *
     * The event listener receives a
     * Autoborna\CoreBundle\Event\TokenReplacementEvent instance.
     *
     * @var string
     */
    const ON_EMAIL_ADDRESS_TOKEN_REPLACEMENT = 'autoborna.email_address_token_replacement';

    /**
     * The autoborna.email_on_open event is dispatched when an email is opened.
     *
     * The event listener receives a
     * Autoborna\EmailBundle\Event\EmailOpenEvent instance.
     *
     * @var string
     */
    const EMAIL_ON_OPEN = 'autoborna.email_on_open';

    /**
     * The autoborna.email_on_send event is dispatched when an email is sent.
     *
     * The event listener receives a
     * Autoborna\EmailBundle\Event\EmailSendEvent instance.
     *
     * @var string
     */
    const EMAIL_ON_SEND = 'autoborna.email_on_send';

    /**
     * The autoborna.email_on_display event is dispatched when an email is viewed via a browser.
     *
     * The event listener receives a
     * Autoborna\EmailBundle\Event\EmailSendEvent instance.
     *
     * @var string
     */
    const EMAIL_ON_DISPLAY = 'autoborna.email_on_display';

    /**
     * The autoborna.email_on_build event is dispatched before displaying the email builder form to allow adding of tokens.
     *
     * The event listener receives a
     * Autoborna\EmailBundle\Event\EmailEvent instance.
     *
     * @var string
     */
    const EMAIL_ON_BUILD = 'autoborna.email_on_build';

    /**
     * The autoborna.email_pre_save event is dispatched right before a email is persisted.
     *
     * The event listener receives a
     * Autoborna\EmailBundle\Event\EmailEvent instance.
     *
     * @var string
     */
    const EMAIL_PRE_SAVE = 'autoborna.email_pre_save';

    /**
     * The autoborna.email_post_save event is dispatched right after a email is persisted.
     *
     * The event listener receives a
     * Autoborna\EmailBundle\Event\EmailEvent instance.
     *
     * @var string
     */
    const EMAIL_POST_SAVE = 'autoborna.email_post_save';

    /**
     * The autoborna.email_pre_delete event is dispatched prior to when a email is deleted.
     *
     * The event listener receives a
     * Autoborna\EmailBundle\Event\EmailEvent instance.
     *
     * @var string
     */
    const EMAIL_PRE_DELETE = 'autoborna.email_pre_delete';

    /**
     * The autoborna.email_post_delete event is dispatched after a email is deleted.
     *
     * The event listener receives a
     * Autoborna\EmailBundle\Event\EmailEvent instance.
     *
     * @var string
     */
    const EMAIL_POST_DELETE = 'autoborna.email_post_delete';

    /**
     * The autoborna.monitored_email_config event is dispatched during the configuration in order to inject custom folder locations.
     *
     * The event listener receives a Autoborna\CoreBundle\Event\MonitoredEmailEvent instance.
     *
     * @var string
     */
    const MONITORED_EMAIL_CONFIG = 'autoborna.monitored_email_config';

    /**
     * The autoborna.on_email_parse event is dispatched when a monitored email box retrieves messages.
     *
     * The event listener receives a Autoborna\EmailBundle\Event\ParseEmailEvent instance.
     *
     * @var string
     */
    const EMAIL_PARSE = 'autoborna.on_email_parse';

    /**
     * The autoborna.on_email_pre_fetch event is dispatched prior to fetching email through a configured monitored inbox in order to set
     * search criteria for the mail to be fetched.
     *
     * The event listener receives a Autoborna\EmailBundle\Event\ParseEmailEvent instance.
     *
     * @var string
     */
    const EMAIL_PRE_FETCH = 'autoborna.on_email_pre_fetch';

    /**
     * The autoborna.on_email_failed event is dispatched when an email has failed to clear the queue and is about to be deleted
     * in order to give a bundle a chance to do an action based on failed email if required.
     *
     * The event listener receives a Autoborna\EmailBundle\Event\QueueEmailEvent instance.
     *
     * @var string
     */
    const EMAIL_FAILED = 'autoborna.on_email_failed';

    /**
     * The autoborna.on_email_resend event is dispatched when an attempt to resend an email occurs
     * in order to give a bundle a chance to do an action based on failed email if required.
     *
     * The event listener receives a Autoborna\EmailBundle\Event\QueueEmailEvent instance.
     *
     * @var string
     */
    const EMAIL_RESEND = 'autoborna.on_email_resend';

    /**
     * The autoborna.email.on_campaign_batch_action event is dispatched when the campaign action triggers.
     *
     * The event listener receives a Autoborna\CampaignBundle\Event\PendingEvent
     *
     * @var string
     */
    const ON_CAMPAIGN_BATCH_ACTION = 'autoborna.email.on_campaign_batch_action';

    /**
     * The autoborna.email.on_campaign_trigger_decision event is fired when the campaign action triggers.
     *
     * The event listener receives a
     * Autoborna\CampaignBundle\Event\CampaignExecutionEvent
     *
     * @var string
     */
    const ON_CAMPAIGN_TRIGGER_DECISION = 'autoborna.email.on_campaign_trigger_decision';

    /**
     * The autoborna.email.on_campaign_trigger_condition event is dispatched when the campaign condition triggers.
     *
     * The event listener receives a
     * Autoborna\CampaignBundle\Event\CampaignExecutionEvent
     *
     * @var string
     */
    const ON_CAMPAIGN_TRIGGER_CONDITION = 'autoborna.email.on_campaign_trigger_condition';

    /**
     * The autoborna.email_on_reply event is dispatched when an reply came to an email.
     *
     * The event listener receives a
     * Autoborna\EmailBundle\Event\EmailOpenEvent instance.
     *
     * @var string
     */
    const EMAIL_ON_REPLY = 'autoborna.email_on_reply';

    /**
     * The autoborna.email.on_email_validation event is dispatched when an email is validated through the validator.
     *
     * The event listener receives a Autoborna\EmailBundle\Event\EmailValidationEvent
     *
     * @var string
     */
    const ON_EMAIL_VALIDATION = 'autoborna.email.on_email_validation';

    /**
     * The autoborna.email.on_sent_email_to_user event is dispatched when email is sent to user.
     *
     * The event listener receives a
     * Autoborna\PointBundle\Events\TriggerExecutedEvent
     *
     * @var string
     */
    const ON_SENT_EMAIL_TO_USER = 'autoborna.email.on_sent_email_to_user';

    /**
     * @deprecated 2.13.0; to be removed in 3.0. Listen to ON_CAMPAIGN_BATCH_ACTION instead.
     *
     * The autoborna.email.on_campaign_trigger_action event is fired when the campaign action triggers.
     *
     * The event listener receives a
     * Autoborna\CampaignBundle\Event\CampaignExecutionEvent
     *
     * @var string
     */
    const ON_CAMPAIGN_TRIGGER_ACTION = 'autoborna.email.on_campaign_trigger_action';

    /**
     * The autoborna.email.on_transport_webhook event is fired when an email transport service sends Autoborna a webhook request.
     *
     * The event listener receives a
     * Autoborna\EmailBundle\Event\TransportWebhookEvent
     *
     * @var string
     */
    const ON_TRANSPORT_WEBHOOK = 'autoborna.email.on_transport_webhook';

    /**
     * The autoborna.email.on_open_rate_winner event is fired when there is a need to determine open rate winner.
     *
     * The event listener receives a
     * Autoborna\CoreBundle\Event\DetermineWinnerEvent
     *
     * @var string
     */
    const ON_DETERMINE_OPEN_RATE_WINNER = 'autoborna.email.on_open_rate_winner';

    /**
     * The autoborna.email.on_open_rate_winner event is fired when there is a need to determine clickthrough rate winner.
     *
     * The event listener receives a
     * Autoborna\CoreBundles\Event\DetermineWinnerEvent
     *
     * @var string
     */
    const ON_DETERMINE_CLICKTHROUGH_RATE_WINNER = 'autoborna.email.on_clickthrough_rate_winner';
}
