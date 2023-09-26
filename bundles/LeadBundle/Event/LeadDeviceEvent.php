<?php

namespace Autoborna\LeadBundle\Event;

use Autoborna\CoreBundle\Event\CommonEvent;
use Autoborna\LeadBundle\Entity\LeadDevice;

/**
 * Class LeadDeviceEvent.
 */
class LeadDeviceEvent extends CommonEvent
{
    /**
     * @param bool $isNew
     */
    public function __construct(LeadDevice &$device, $isNew = false)
    {
        $this->entity = &$device;
        $this->isNew  = $isNew;
    }

    /**
     * Returns the LeadDevice entity.
     *
     * @return LeadDevice
     */
    public function getDevice()
    {
        return $this->entity;
    }

    /**
     * Sets the LeadDevice entity.
     */
    public function setDevice(LeadDevice $device)
    {
        $this->entity = $device;
    }
}
