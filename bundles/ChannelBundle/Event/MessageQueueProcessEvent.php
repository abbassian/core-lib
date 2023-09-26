<?php

namespace Autoborna\ChannelBundle\Event;

use Autoborna\ChannelBundle\Entity\MessageQueue;
use Autoborna\CoreBundle\Event\CommonEvent;

class MessageQueueProcessEvent extends CommonEvent
{
    /**
     * MessageQueueEvent constructor.
     */
    public function __construct(MessageQueue $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return MessageQueue
     */
    public function getMessageQueue()
    {
        return $this->entity;
    }

    /**
     * @param $channel
     *
     * @return bool
     */
    public function checkContext($channel)
    {
        return $channel === $this->entity->getChannel();
    }
}
