<?php

namespace Autoborna\QueueBundle;

/**
 * Class AutobornaQueueEvents
 * Events available for AutobornaQueueBundle.
 */
final class QueueEvents
{
    const CONSUME_MESSAGE = 'autoborna.queue_consume_message';

    const PUBLISH_MESSAGE = 'autoborna.queue_publish_message';

    const EMAIL_HIT = 'autoborna.queue_email_hit';

    const PAGE_HIT = 'autoborna.queue_page_hit';

    const TRANSPORT_WEBHOOK = 'autoborna.queue_transport_webhook';
}
