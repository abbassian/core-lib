<?php

namespace Autoborna\LeadBundle\Tracker\Service\DeviceCreatorService;

use DeviceDetector\DeviceDetector;
use Autoborna\LeadBundle\Entity\Lead;
use Autoborna\LeadBundle\Entity\LeadDevice;

/**
 * Interface DeviceCreatorServiceInterface.
 */
interface DeviceCreatorServiceInterface
{
    /**
     * @return LeadDevice|null Null is returned if device can't be detected
     */
    public function getCurrentFromDetector(DeviceDetector $deviceDetector, Lead $assignedLead);
}
