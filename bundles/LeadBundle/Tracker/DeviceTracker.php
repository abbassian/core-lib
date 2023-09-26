<?php

namespace Autoborna\LeadBundle\Tracker;

use Autoborna\LeadBundle\Entity\Lead;
use Autoborna\LeadBundle\Entity\LeadDevice;
use Autoborna\LeadBundle\Tracker\Factory\DeviceDetectorFactory\DeviceDetectorFactoryInterface;
use Autoborna\LeadBundle\Tracker\Service\DeviceCreatorService\DeviceCreatorServiceInterface;
use Autoborna\LeadBundle\Tracker\Service\DeviceTrackingService\DeviceTrackingServiceInterface;
use Monolog\Logger;

class DeviceTracker
{
    /**
     * @var DeviceCreatorServiceInterface
     */
    private $deviceCreatorService;

    /**
     * @var DeviceDetectorFactoryInterface
     */
    private $deviceDetectorFactory;

    /**
     * @var DeviceTrackingServiceInterface
     */
    private $deviceTrackingService;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var bool
     */
    private $deviceWasChanged = false;

    /**
     * @var LeadDevice[]
     */
    private $trackedDevice = [];

    /**
     * DeviceTracker constructor.
     */
    public function __construct(
        DeviceCreatorServiceInterface $deviceCreatorService,
        DeviceDetectorFactoryInterface $deviceDetectorFactory,
        DeviceTrackingServiceInterface $deviceTrackingService,
        Logger $logger
    ) {
        $this->deviceCreatorService  = $deviceCreatorService;
        $this->deviceDetectorFactory = $deviceDetectorFactory;
        $this->deviceTrackingService = $deviceTrackingService;
        $this->logger                = $logger;
    }

    /**
     * @param $userAgent
     *
     * @return \Autoborna\LeadBundle\Entity\LeadDevice|null
     */
    public function createDeviceFromUserAgent(Lead $trackedContact, $userAgent)
    {
        $signature = $trackedContact->getId().$userAgent;
        if (isset($this->trackedDevice[$signature])) {
            // Prevent subsequent calls within the same session from creating multiple entries
            return $this->trackedDevice[$signature];
        }

        $this->trackedDevice[$signature] = $trackedDevice = $this->deviceTrackingService->getTrackedDevice();

        $deviceDetector = $this->deviceDetectorFactory->create($userAgent);
        $deviceDetector->parse();
        $currentDevice = $this->deviceCreatorService->getCurrentFromDetector($deviceDetector, $trackedContact);

        if ( // Do not create a new device if
            // ... the device is new
            $trackedDevice && $trackedDevice->getId()
            && // ... the device is the same
            $trackedDevice->getSignature() === $currentDevice->getSignature()
            && // ... the contact given is the same as the owner of the device tracked
            $trackedDevice->getLead()->getId() === $trackedContact->getId()
        ) {
            return $trackedDevice;
        }

        // New device so record it and track it
        $this->deviceWasChanged = true;

        $this->trackedDevice[$signature] = $this->deviceTrackingService->trackCurrentDevice($currentDevice, true);

        return $this->trackedDevice[$signature];
    }

    /**
     * @return \Autoborna\LeadBundle\Entity\LeadDevice|null
     */
    public function getTrackedDevice()
    {
        $trackedDevice = $this->deviceTrackingService->getTrackedDevice();

        if (null !== $trackedDevice) {
            $this->logger->addDebug("LEAD: Tracking ID for this device is {$trackedDevice->getTrackingId()}");
        }

        return $trackedDevice;
    }

    /**
     * @return bool
     */
    public function wasDeviceChanged()
    {
        return $this->deviceWasChanged;
    }

    public function clearTrackingCookies()
    {
        $this->deviceTrackingService->clearTrackingCookies();
    }
}
