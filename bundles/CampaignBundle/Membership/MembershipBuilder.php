<?php

namespace Autoborna\CampaignBundle\Membership;

use Autoborna\CampaignBundle\Entity\Campaign;
use Autoborna\CampaignBundle\Entity\LeadRepository as CampaignMemberRepository;
use Autoborna\CampaignBundle\Executioner\ContactFinder\Limiter\ContactLimiter;
use Autoborna\CampaignBundle\Membership\Exception\RunLimitReachedException;
use Autoborna\CoreBundle\Helper\ProgressBarHelper;
use Autoborna\LeadBundle\Entity\LeadRepository;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Translation\TranslatorInterface;

class MembershipBuilder
{
    /**
     * @var MembershipManager
     */
    private $manager;

    /**
     * @var CampaignMemberRepository
     */
    private $campaignMemberRepository;

    /**
     * @var LeadRepository
     */
    private $leadRepository;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var Campaign
     */
    private $campaign;

    /**
     * @var ContactLimiter
     */
    private $contactLimiter;

    /**
     * @var int
     */
    private $runLimit;

    /**
     * @var OutputInterface|null
     */
    private $output;

    /**
     * @var ProgressBar|null
     */
    private $progressBar;

    public function __construct(
        MembershipManager $manager,
        CampaignMemberRepository $campaignMemberRepository,
        LeadRepository $leadRepository,
        TranslatorInterface $translator
    ) {
        $this->manager                  = $manager;
        $this->campaignMemberRepository = $campaignMemberRepository;
        $this->leadRepository           = $leadRepository;
        $this->translator               = $translator;
    }

    /**
     * @param int $runLimit
     *
     * @return int
     */
    public function build(Campaign $campaign, ContactLimiter $contactLimiter, $runLimit, OutputInterface $output = null)
    {
        defined('MAUTIC_REBUILDING_CAMPAIGNS') or define('MAUTIC_REBUILDING_CAMPAIGNS', 1);

        $this->campaign       = $campaign;
        $this->contactLimiter = $contactLimiter;
        $this->runLimit       = (int) $runLimit;
        $this->output         = $output;

        $contactsProcessed = 0;

        try {
            $contactsProcessed += $this->addNewlyQualifiedMembers($contactsProcessed);
        } catch (RunLimitReachedException $exception) {
            return $exception->getContactsProcessed();
        }

        try {
            $contactsProcessed += $this->removeUnqualifiedMembers($contactsProcessed);
        } catch (RunLimitReachedException $exception) {
            return $exception->getContactsProcessed();
        }

        return $contactsProcessed;
    }

    /**
     * @throws RunLimitReachedException
     */
    private function addNewlyQualifiedMembers(int $totalContactsProcessed): int
    {
        $contactsProcessed = 0;

        if ($this->output) {
            $countResult = $this->campaignMemberRepository->getCountsForCampaignContactsBySegment($this->campaign->getId(), $this->contactLimiter, $this->campaign->allowRestart());

            $this->output->writeln(
                $this->translator->trans(
                    'autoborna.campaign.rebuild.to_be_added',
                    ['%leads%' => $countResult->getCount(), '%batch%' => $this->contactLimiter->getBatchLimit()]
                )
            );

            if (0 === $countResult->getCount()) {
                // No use continuing
                return 0;
            }

            $this->startProgressBar($countResult->getCount());
        }

        $contacts = $this->campaignMemberRepository->getCampaignContactsBySegments($this->campaign->getId(), $this->contactLimiter, $this->campaign->allowRestart());

        while (count($contacts)) {
            $contactCollection = $this->leadRepository->getContactCollection($contacts);
            if (!$contactCollection->count()) {
                // Prevent endless loop just in case
                break;
            }

            $contactsProcessed += $contactCollection->count();

            // Add the contacts to this segment
            $this->manager->addContacts($contactCollection, $this->campaign, false);

            // Clear Lead entities from RAM
            $this->leadRepository->clear();

            // Have we hit the run limit?
            if ($this->runLimit && $contactsProcessed >= $this->runLimit) {
                $this->finishProgressBar();
                throw new RunLimitReachedException($contactsProcessed + $totalContactsProcessed);
            }

            // Get next batch
            $contacts = $this->campaignMemberRepository->getCampaignContactsBySegments(
                $this->campaign->getId(),
                $this->contactLimiter,
                $this->campaign->allowRestart()
            );
        }

        $this->finishProgressBar();

        return $contactsProcessed;
    }

    /**
     * @throws RunLimitReachedException
     */
    private function removeUnqualifiedMembers(int $totalContactsProcessed): int
    {
        $contactsProcessed = 0;

        if ($this->output) {
            $countResult = $this->campaignMemberRepository->getCountsForOrphanedContactsBySegments($this->campaign->getId(), $this->contactLimiter);

            $this->output->writeln(
                $this->translator->trans(
                    'autoborna.lead.list.rebuild.to_be_removed',
                    ['%leads%' => $countResult->getCount(), '%batch%' => $this->contactLimiter->getBatchLimit()]
                )
            );

            if (0 === $countResult->getCount()) {
                // No use continuing
                return 0;
            }

            $this->startProgressBar($countResult->getCount());
        }

        $contacts = $this->campaignMemberRepository->getOrphanedContacts($this->campaign->getId(), $this->contactLimiter);
        while (count($contacts)) {
            $contactCollection = $this->leadRepository->getContactCollection($contacts);
            if (!$contactCollection->count()) {
                // Prevent endless loop just in case
                break;
            }

            $contactsProcessed += $contactCollection->count();

            // Add the contacts to this segment
            $this->manager->removeContacts($contactCollection, $this->campaign, true);

            // Clear Lead entities from RAM
            $this->leadRepository->clear();

            // Have we hit the run limit?
            if ($this->runLimit && $contactsProcessed >= $this->runLimit) {
                $this->finishProgressBar();
                throw new RunLimitReachedException($contactsProcessed + $totalContactsProcessed);
            }

            // Get next batch
            $contacts = $this->campaignMemberRepository->getOrphanedContacts($this->campaign->getId(), $this->contactLimiter);
        }

        $this->finishProgressBar();

        return $contactsProcessed;
    }

    private function startProgressBar(int $total): void
    {
        if (!$this->output) {
            $this->progressBar = null;
            $this->manager->setProgressBar($this->progressBar);

            return;
        }

        $this->progressBar = ProgressBarHelper::init($this->output, $total);
        $this->progressBar->start();

        // Notify the manager to increment progress as contacts are added
        $this->manager->setProgressBar($this->progressBar);
    }

    private function finishProgressBar(): void
    {
        if ($this->progressBar) {
            $this->progressBar->finish();
            $this->output->writeln('');
        }
    }
}
