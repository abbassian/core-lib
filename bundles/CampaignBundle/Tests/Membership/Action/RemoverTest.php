<?php

namespace Autoborna\CampaignBundle\Tests\Membership\Action;

use Autoborna\CampaignBundle\Entity\Lead as CampaignMember;
use Autoborna\CampaignBundle\Entity\LeadEventLogRepository;
use Autoborna\CampaignBundle\Entity\LeadRepository;
use Autoborna\CampaignBundle\Membership\Action\Remover;
use Autoborna\CampaignBundle\Membership\Exception\ContactAlreadyRemovedFromCampaignException;
use Autoborna\CoreBundle\Templating\Helper\DateHelper;
use Symfony\Component\Translation\TranslatorInterface;

class RemoverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var LeadRepository|\PHPUnit\Framework\MockObject\MockObject
     */
    private $leadRepository;

    /**
     * @var LeadEventLogRepository|\PHPUnit\Framework\MockObject\MockObject
     */
    private $leadEventLogRepository;

    protected function setUp(): void
    {
        $this->leadRepository         = $this->createMock(LeadRepository::class);
        $this->leadEventLogRepository = $this->createMock(LeadEventLogRepository::class);
    }

    public function testMemberHasDateExitedSetWithForcedExit()
    {
        $campaignMember = new CampaignMember();
        $campaignMember->setManuallyRemoved(false);

        $this->leadEventLogRepository->expects($this->once())
            ->method('unscheduleEvents');

        $this->getRemover()->updateExistingMembership($campaignMember, true);

        $this->assertInstanceOf(\DateTime::class, $campaignMember->getDateLastExited());
    }

    public function testMemberHasDateExistedSetToNullWhenRemovedByFilter()
    {
        $campaignMember = new CampaignMember();
        $campaignMember->setManuallyRemoved(false);

        $this->leadEventLogRepository->expects($this->once())
            ->method('unscheduleEvents');

        $this->getRemover()->updateExistingMembership($campaignMember, false);

        $this->assertNull($campaignMember->getDateLastExited());
    }

    public function testExceptionThrownWhenMemberIsAlreadyRemoved()
    {
        $this->expectException(ContactAlreadyRemovedFromCampaignException::class);

        $campaignMember = new CampaignMember();
        $campaignMember->setManuallyRemoved(true);

        $this->getRemover()->updateExistingMembership($campaignMember, false);
    }

    /**
     * @return Remover
     */
    private function getRemover()
    {
        $translator     = $this->createMock(TranslatorInterface::class);
        $dateTimeHelper = $this->createMock(DateHelper::class);

        return new Remover($this->leadRepository, $this->leadEventLogRepository, $translator, $dateTimeHelper);
    }
}
