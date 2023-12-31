<?php

namespace Autoborna\LeadBundle\Deduplicate;

use Autoborna\LeadBundle\Deduplicate\Exception\SameContactException;
use Autoborna\LeadBundle\Entity\Lead;
use Autoborna\LeadBundle\Entity\LeadRepository;
use Autoborna\LeadBundle\Model\FieldModel;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class ContactDeduper
{
    use DeduperTrait;

    /**
     * @var ContactMerger
     */
    private $contactMerger;

    /**
     * @var LeadRepository
     */
    private $leadRepository;

    /**
     * @var bool
     */
    private $mergeNewerIntoOlder = false;

    /**
     * DedupModel constructor.
     */
    public function __construct(FieldModel $fieldModel, ContactMerger $contactMerger, LeadRepository $leadRepository)
    {
        $this->fieldModel     = $fieldModel;
        $this->contactMerger  = $contactMerger;
        $this->leadRepository = $leadRepository;
    }

    /**
     * @param bool $mergeNewerIntoOlder
     *
     * @return int
     */
    public function deduplicate($mergeNewerIntoOlder = false, OutputInterface $output = null)
    {
        $this->mergeNewerIntoOlder = $mergeNewerIntoOlder;
        $lastContactId             = 0;
        $totalContacts             = $this->leadRepository->getIdentifiedContactCount();
        $progress                  = null;

        if ($output) {
            $progress = new ProgressBar($output, $totalContacts);
        }

        $dupCount = 0;
        while ($contact = $this->leadRepository->getNextIdentifiedContact($lastContactId)) {
            $lastContactId = $contact->getId();
            $fields        = $contact->getProfileFields();
            $duplicates    = $this->checkForDuplicateContacts($fields);

            if ($progress) {
                $progress->advance();
            }

            // Were duplicates found?
            if (count($duplicates) > 1) {
                $loser = reset($duplicates);
                while ($winner = next($duplicates)) {
                    try {
                        $this->contactMerger->merge($winner, $loser);

                        ++$dupCount;

                        if ($progress) {
                            // Advance the progress bar for the deleted contacts that are no longer in the total count
                            $progress->advance();
                        }
                    } catch (SameContactException $exception) {
                    }

                    $loser = $winner;
                }
            }

            // Clear all entities in memory for RAM control
            $this->leadRepository->clear();
            gc_collect_cycles();
        }

        return $dupCount;
    }

    /**
     * @return Lead[]
     */
    public function checkForDuplicateContacts(array $queryFields)
    {
        $duplicates = [];
        $uniqueData = $this->getUniqueData($queryFields);
        if (!empty($uniqueData)) {
            $duplicates = $this->leadRepository->getLeadsByUniqueFields($uniqueData);

            // By default, duplicates are ordered by newest first
            if (!$this->mergeNewerIntoOlder) {
                // Reverse the array so that oldest are on "top" in order to merge oldest into the next until they all have been merged into the
                // the newest record
                $duplicates = array_reverse($duplicates);
            }
        }

        return $duplicates;
    }
}
