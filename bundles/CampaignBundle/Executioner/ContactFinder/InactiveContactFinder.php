<?php

namespace Autoborna\CampaignBundle\Executioner\ContactFinder;

use Doctrine\Common\Collections\ArrayCollection;
use Autoborna\CampaignBundle\Entity\Event;
use Autoborna\CampaignBundle\Entity\LeadRepository as CampaignLeadRepository;
use Autoborna\CampaignBundle\Executioner\ContactFinder\Limiter\ContactLimiter;
use Autoborna\CampaignBundle\Executioner\Exception\NoContactsFoundException;
use Autoborna\LeadBundle\Entity\LeadRepository;
use Psr\Log\LoggerInterface;

class InactiveContactFinder
{
    /**
     * @var LeadRepository
     */
    private $leadRepository;

    /**
     * @var CampaignLeadRepository
     */
    private $campaignLeadRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ArrayCollection
     */
    private $campaignMemberDatesAdded;

    public function __construct(
        LeadRepository $leadRepository,
        CampaignLeadRepository $campaignLeadRepository,
        LoggerInterface $logger
    ) {
        $this->leadRepository         = $leadRepository;
        $this->campaignLeadRepository = $campaignLeadRepository;
        $this->logger                 = $logger;
    }

    /**
     * @param int $campaignId
     *
     * @return ArrayCollection
     *
     * @throws NoContactsFoundException
     */
    public function getContacts($campaignId, Event $decisionEvent, ContactLimiter $limiter)
    {
        if ($limiter->hasCampaignLimit() && 0 === $limiter->getCampaignLimitRemaining()) {
            // Limit was reached but do not trigger the NoContactsFoundException
            return new ArrayCollection();
        }

        // Get list of all campaign leads
        $decisionParentEvent            = $decisionEvent->getParent();
        $this->campaignMemberDatesAdded = $this->campaignLeadRepository->getInactiveContacts(
            $campaignId,
            $decisionEvent->getId(),
            ($decisionParentEvent) ? $decisionParentEvent->getId() : null,
            $limiter
        );

        if (empty($this->campaignMemberDatesAdded)) {
            // No new contacts found in the campaign
            throw new NoContactsFoundException();
        }

        $campaignContacts = array_keys($this->campaignMemberDatesAdded);
        $this->logger->debug('CAMPAIGN: Processing the following contacts: '.implode(', ', $campaignContacts));

        // Fetch entity objects for the found contacts
        $contacts = $this->leadRepository->getContactCollection($campaignContacts);

        if (!count($contacts)) {
            // Just a precaution in case non-existent contacts are lingering in the campaign leads table
            $this->logger->debug('CAMPAIGN: No contact entities found.');

            throw new NoContactsFoundException();
        }

        return $contacts;
    }

    /**
     * @return ArrayCollection
     */
    public function getDatesAdded()
    {
        return $this->campaignMemberDatesAdded;
    }

    /**
     * @param int $campaignId
     *
     * @return int
     */
    public function getContactCount($campaignId, array $decisionEvents, ContactLimiter $limiter)
    {
        return $this->campaignLeadRepository->getInactiveContactCount($campaignId, $decisionEvents, $limiter);
    }

    /**
     * Clear Lead entities from memory.
     */
    public function clear()
    {
        $this->leadRepository->clear();
    }
}
