<?php

namespace Autoborna\NotificationBundle\Event;

use Autoborna\CoreBundle\Event\CommonEvent;
use Autoborna\LeadBundle\Entity\Lead;
use Autoborna\NotificationBundle\Entity\Notification;

/**
 * Class SendingNotificationEvent.
 */
class SendingNotificationEvent extends CommonEvent
{
    /**
     * @var Lead
     */
    protected $lead;

    /**
     * @var Notification
     */
    protected $entity;

    /**
     * SendingNotificationEvent constructor.
     */
    public function __construct(Notification $notification, Lead $lead)
    {
        $this->entity = $notification;
        $this->lead   = $lead;
    }

    /**
     * @return Notification
     */
    public function getNotification()
    {
        return $this->entity;
    }

    /**
     * @return $this
     */
    public function setNotifiction(Notification $notification)
    {
        $this->entity = $notification;

        return $this;
    }

    /**
     * @return Lead
     */
    public function getLead()
    {
        return $this->lead;
    }

    /**
     * @return $this
     */
    public function setLead(Lead $lead)
    {
        $this->lead = $lead;

        return $this;
    }
}
