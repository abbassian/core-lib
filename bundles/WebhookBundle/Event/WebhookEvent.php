<?php

namespace Autoborna\WebhookBundle\Event;

use Autoborna\CoreBundle\Event\CommonEvent;
use Autoborna\WebhookBundle\Entity\Webhook;

/**
 * Class WebhookEvent.
 */
class WebhookEvent extends CommonEvent
{
    /**
     * @var Webhook
     */
    protected $entity;

    /**
     * @var bool
     */
    protected $isNew = false;

    /**
     * @var string
     */
    private $reason = '';

    /**
     * @param bool $isNew
     */
    public function __construct(Webhook $webhook, $isNew = false, $reason = '')
    {
        $this->entity = $webhook;
        $this->isNew  = $isNew;
        $this->reason = $reason;
    }

    /**
     * Returns the Webhook entity.
     *
     * @return Webhook
     */
    public function getWebhook()
    {
        return $this->entity;
    }

    /**
     * Sets the Webhook entity.
     */
    public function setWebhook(Webhook $webhook)
    {
        $this->entity = $webhook;
    }

    /**
     * @param $reason
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
    }

    public function getReason(): string
    {
        return $this->reason;
    }
}
