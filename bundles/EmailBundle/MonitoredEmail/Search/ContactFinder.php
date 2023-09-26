<?php

namespace Autoborna\EmailBundle\MonitoredEmail\Search;

use Autoborna\EmailBundle\Entity\Stat;
use Autoborna\EmailBundle\Entity\StatRepository;
use Autoborna\EmailBundle\MonitoredEmail\Processor\Address;
use Autoborna\LeadBundle\Entity\LeadRepository;
use Psr\Log\LoggerInterface;

class ContactFinder
{
    /**
     * @var StatRepository
     */
    private $statRepository;

    /**
     * @var LeadRepository
     */
    private $leadRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ContactFinder constructor.
     */
    public function __construct(StatRepository $statRepository, LeadRepository $leadRepository, LoggerInterface $logger)
    {
        $this->statRepository = $statRepository;
        $this->leadRepository = $leadRepository;
        $this->logger         = $logger;
    }

    /**
     * @param string $returnPathEmail
     * @param null   $contactEmail
     *
     * @return Result
     */
    public function find($contactEmail, $returnPathEmail = null)
    {
        $this->logger->debug("MONITORED EMAIL: Searching for a contact $contactEmail/$returnPathEmail");

        // We have a return path email so let's figure out who it originated to
        if ($returnPathEmail && $hash = Address::parseAddressForStatHash($returnPathEmail)) {
            $result = $this->findByHash($hash);
            if ($result->getStat()) {
                // A stat was found so need to search by email
                return $result;
            }
        }

        return $this->findByAddress($contactEmail);
    }

    /**
     * @param $hash
     *
     * @return Result
     */
    public function findByHash($hash)
    {
        $result = new Result();
        $this->logger->debug('MONITORED EMAIL: Searching for a contact by hash '.$hash);

        /** @var Stat $stat */
        $stat = $this->statRepository->findOneBy(['trackingHash' => $hash]);
        $this->logger->debug("MONITORED EMAIL: HashId of $hash found in return path");
        if ($stat && $stat->getLead()) {
            $this->logger->debug("MONITORED EMAIL: Stat ID {$stat->getId()} found for hash $hash");
            $result->setStat($stat);
        }

        return $result;
    }

    /**
     * @param $address
     *
     * @return Result
     */
    public function findByAddress($address)
    {
        $result = new Result();
        // Search by email address
        if ($contacts = $this->leadRepository->getContactsByEmail($address)) {
            $result->setContacts($contacts);
            $this->logger->debug('MONITORED EMAIL: '.count($contacts).' contacts found');
        }

        return $result;
    }
}
