<?php

namespace Autoborna\ChannelBundle\Event;

use Autoborna\ChannelBundle\Entity\Message;
use Autoborna\CoreBundle\Event\CommonEvent;

class MessageEvent extends CommonEvent
{
    /**
     * MessageEvent constructor.
     *
     * @param bool $isNew
     */
    public function __construct(Message $message, $isNew = false)
    {
        $this->entity = $message;
        $this->isNew  = $isNew;
    }

    /**
     * @return Message
     */
    public function getMessage()
    {
        return $this->entity;
    }
}
