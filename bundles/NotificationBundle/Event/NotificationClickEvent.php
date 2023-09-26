<?php

namespace Autoborna\NotificationBundle\Event;

use Autoborna\CoreBundle\Event\CommonEvent;
use Autoborna\NotificationBundle\Entity\Notification;
use Autoborna\NotificationBundle\Entity\Stat;

/**
 * Class NotificationClickEvent.
 */
class NotificationClickEvent extends CommonEvent
{
    private $request;

    private $notification;

    /**
     * @param $request
     */
    public function __construct(Stat $stat, $request)
    {
        $this->entity       = $stat;
        $this->notification = $stat->getNotification();
        $this->request      = $request;
    }

    /**
     * Returns the Notification entity.
     *
     * @return Notification
     */
    public function getNotification()
    {
        return $this->notification;
    }

    /**
     * Get notification request.
     *
     * @return string
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Stat
     */
    public function getStat()
    {
        return $this->entity;
    }
}
