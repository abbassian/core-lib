<?php

namespace Autoborna\ChannelBundle;

/**
 * Class ChannelEvents.
 */
final class ChannelEvents
{
    /**
     * The autoborna.add_channel event registers communication channels.
     *
     * The event listener receives a Autoborna\ChannelBundle\Event\ChannelEvent instance.
     *
     * @var string
     */
    const ADD_CHANNEL = 'autoborna.add_channel';

    /**
     * The autoborna.channel_broadcast event is dispatched by the autoborna:send:broadcast command to process communication to pending contacts.
     *
     * The event listener receives a Autoborna\ChannelBundle\Event\ChannelBroadcastEvent instance.
     *
     * @var string
     */
    const CHANNEL_BROADCAST = 'autoborna.channel_broadcast';

    /**
     * The autoborna.message_queued event is dispatched to save a message to the queue.
     *
     * The event listener receives a Autoborna\ChannelBundle\Event\MessageQueueEvent instance.
     *
     * @var string
     */
    const MESSAGE_QUEUED = 'autoborna.message_queued';

    /**
     * The autoborna.process_message_queue event is dispatched to be processed by a listener.
     *
     * The event listener receives a Autoborna\ChannelBundle\Event\MessageQueueProcessEvent instance.
     *
     * @var string
     */
    const PROCESS_MESSAGE_QUEUE = 'autoborna.process_message_queue';

    /**
     * The autoborna.process_message_queue_batch event is dispatched to process a batch of messages by channel and channel ID.
     *
     * The event listener receives a Autoborna\ChannelBundle\Event\MessageQueueBatchProcessEvent instance.
     *
     * @var string
     */
    const PROCESS_MESSAGE_QUEUE_BATCH = 'autoborna.process_message_queue_batch';

    /**
     * The autoborna.channel.on_campaign_batch_action event is dispatched when the campaign action triggers.
     *
     * The event listener receives a Autoborna\CampaignBundle\Event\PendingEvent
     *
     * @var string
     */
    const ON_CAMPAIGN_BATCH_ACTION = 'autoborna.channel.on_campaign_batch_action';

    /**
     * The autoborna.message_pre_save event is dispatched right before a form is persisted.
     *
     * The event listener receives a
     * Autoborna\ChannelEvent\Event\MessageEvent instance.
     *
     * @var string
     */
    const MESSAGE_PRE_SAVE = 'autoborna.message_pre_save';

    /**
     * The autoborna.message_post_save event is dispatched right after a form is persisted.
     *
     * The event listener receives a
     * Autoborna\ChannelEvent\Event\MessageEvent instance.
     *
     * @var string
     */
    const MESSAGE_POST_SAVE = 'autoborna.message_post_save';

    /**
     * The autoborna.message_pre_delete event is dispatched before a form is deleted.
     *
     * The event listener receives a
     * Autoborna\ChannelEvent\Event\MessageEvent instance.
     *
     * @var string
     */
    const MESSAGE_PRE_DELETE = 'autoborna.message_pre_delete';

    /**
     * The autoborna.message_post_delete event is dispatched after a form is deleted.
     *
     * The event listener receives a
     * Autoborna\ChannelEvent\Event\MessageEvent instance.
     *
     * @var string
     */
    const MESSAGE_POST_DELETE = 'autoborna.message_post_delete';
}
