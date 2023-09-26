<?php

namespace Autoborna\SmsBundle;

/**
 * Class SmsEvents
 * Events available for SmsBundle.
 */
final class SmsEvents
{
    /**
     * The autoborna.sms_token_replacement event is thrown right before the content is returned.
     *
     * The event listener receives a
     * Autoborna\CoreBundle\Event\TokenReplacementEvent instance.
     *
     * @var string
     */
    const TOKEN_REPLACEMENT = 'autoborna.sms_token_replacement';

    /**
     * The autoborna.sms_on_send event is thrown when a sms is sent.
     *
     * The event listener receives a
     * Autoborna\SmsBundle\Event\SmsSendEvent instance.
     *
     * @var string
     */
    const SMS_ON_SEND = 'autoborna.sms_on_send';

    /**
     * The autoborna.sms_pre_save event is thrown right before a sms is persisted.
     *
     * The event listener receives a
     * Autoborna\SmsBundle\Event\SmsEvent instance.
     *
     * @var string
     */
    const SMS_PRE_SAVE = 'autoborna.sms_pre_save';

    /**
     * The autoborna.sms_post_save event is thrown right after a sms is persisted.
     *
     * The event listener receives a
     * Autoborna\SmsBundle\Event\SmsEvent instance.
     *
     * @var string
     */
    const SMS_POST_SAVE = 'autoborna.sms_post_save';

    /**
     * The autoborna.sms_pre_delete event is thrown prior to when a sms is deleted.
     *
     * The event listener receives a
     * Autoborna\SmsBundle\Event\SmsEvent instance.
     *
     * @var string
     */
    const SMS_PRE_DELETE = 'autoborna.sms_pre_delete';

    /**
     * The autoborna.sms_post_delete event is thrown after a sms is deleted.
     *
     * The event listener receives a
     * Autoborna\SmsBundle\Event\SmsEvent instance.
     *
     * @var string
     */
    const SMS_POST_DELETE = 'autoborna.sms_post_delete';

    /**
     * The autoborna.sms.on_campaign_trigger_action event is fired when the campaign action triggers.
     *
     * The event listener receives a
     * Autoborna\CampaignBundle\Event\CampaignExecutionEvent
     *
     * @var string
     */
    const ON_CAMPAIGN_TRIGGER_ACTION = 'autoborna.sms.on_campaign_trigger_action';

    /**
     * The autoborna.sms.on_reply event is dispatched when a SMS service receives a reply.
     *
     * The event listener receives a Autoborna\SmsBundle\Event\ReplyEvent
     *
     * @var string
     */
    const ON_REPLY = 'autoborna.sms.on_reply';

    /**
     * The autoborna.sms.on_campaign_reply event is dispatched when a SMS reply campaign decision is processed.
     *
     * The event listener receives a Autoborna\SmsBundle\Event\ReplyEvent
     *
     * @var string
     */
    const ON_CAMPAIGN_REPLY = 'autoborna.sms.on_campaign_reply';
}
