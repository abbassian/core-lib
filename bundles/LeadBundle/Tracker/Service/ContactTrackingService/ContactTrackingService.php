<?php

namespace Autoborna\LeadBundle\Tracker\Service\ContactTrackingService;

use Autoborna\CoreBundle\Helper\CookieHelper;
use Autoborna\LeadBundle\Entity\Lead;
use Autoborna\LeadBundle\Entity\LeadDeviceRepository;
use Autoborna\LeadBundle\Entity\LeadRepository;
use Autoborna\LeadBundle\Entity\MergeRecordRepository;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class ContactTrackingService.
 *
 * Used to ensure that contacts tracked under the old method are continued to be tracked under the new
 */
final class ContactTrackingService implements ContactTrackingServiceInterface
{
    /**
     * @var CookieHelper
     */
    private $cookieHelper;

    /**
     * @var LeadDeviceRepository
     */
    private $leadDeviceRepository;

    /**
     * @var LeadRepository
     */
    private $leadRepository;

    /**
     * @var MergeRecordRepository
     */
    private $mergeRecordRepository;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * ContactTrackingService constructor.
     */
    public function __construct(
        CookieHelper $cookieHelper,
        LeadDeviceRepository $leadDeviceRepository,
        LeadRepository $leadRepository,
        MergeRecordRepository $mergeRecordRepository,
        RequestStack $requestStack
    ) {
        $this->cookieHelper          = $cookieHelper;
        $this->leadDeviceRepository  = $leadDeviceRepository;
        $this->leadRepository        = $leadRepository;
        $this->mergeRecordRepository = $mergeRecordRepository;
        $this->requestStack          = $requestStack;
    }

    /**
     * @return Lead|null
     */
    public function getTrackedLead()
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            return null;
        }

        $trackingId = $this->getTrackedIdentifier();
        if (null === $trackingId) {
            return null;
        }

        $leadId = $this->cookieHelper->getCookie($trackingId, null);
        if (null === $leadId) {
            $leadId = $request->get('mtc_id', null);
            if (null === $leadId) {
                return null;
            }
        }

        $lead = $this->leadRepository->getEntity($leadId);
        if (null === $lead) {
            // Check if this contact was merged into another and if so, return the new contact
            $lead = $this->mergeRecordRepository->findMergedContact($leadId);

            if (null === $lead) {
                return null;
            }

            // Hydrate fields with custom field data
            $fields = $this->leadRepository->getFieldValues($lead->getId());
            $lead->setFields($fields);
        }

        $anotherDeviceAlreadyTracked = $this->leadDeviceRepository->isAnyLeadDeviceTracked($lead);

        return $anotherDeviceAlreadyTracked ? null : $lead;
    }

    /**
     * @return string|null
     */
    public function getTrackedIdentifier()
    {
        return $this->cookieHelper->getCookie('autoborna_session_id', null);
    }
}
