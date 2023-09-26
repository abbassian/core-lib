<?php

namespace Autoborna\CampaignBundle\Tests\Executioner\ContactFinder;

use Doctrine\Common\Collections\ArrayCollection;
use Autoborna\CampaignBundle\Entity\Event;
use Autoborna\CampaignBundle\Entity\LeadRepository as CampaignLeadRepository;
use Autoborna\CampaignBundle\Executioner\ContactFinder\InactiveContactFinder;
use Autoborna\CampaignBundle\Executioner\ContactFinder\Limiter\ContactLimiter;
use Autoborna\CampaignBundle\Executioner\Exception\NoContactsFoundException;
use Autoborna\LeadBundle\Entity\Lead;
use Autoborna\LeadBundle\Entity\LeadRepository;
use Psr\Log\NullLogger;

class InactiveContactFinderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|LeadRepository
     */
    private $leadRepository;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|CampaignLeadRepository
     */
    private $campaignLeadRepository;

    protected function setUp(): void
    {
        $this->leadRepository         = $this->createMock(LeadRepository::class);
        $this->campaignLeadRepository = $this->createMock(CampaignLeadRepository::class);
    }

    public function testNoContactsFoundExceptionIsThrown()
    {
        $this->campaignLeadRepository->expects($this->once())
            ->method('getInactiveContacts')
            ->willReturn([]);

        $this->expectException(NoContactsFoundException::class);

        $limiter = new ContactLimiter(0, 0, 0, 0);
        $this->getContactFinder()->getContacts(1, new Event(), $limiter);
    }

    public function testNoContactsFoundExceptionIsThrownIfEntitiesAreNotFound()
    {
        $contactMemberDates = [
            1 => new \DateTime(),
        ];

        $this->campaignLeadRepository->expects($this->once())
            ->method('getInactiveContacts')
            ->willReturn($contactMemberDates);

        $this->leadRepository->expects($this->once())
            ->method('getContactCollection')
            ->willReturn([]);

        $this->expectException(NoContactsFoundException::class);

        $limiter = new ContactLimiter(0, 0, 0, 0);
        $this->getContactFinder()->getContacts(1, new Event(), $limiter);
    }

    public function testContactsAreFoundAndStoredInCampaignMemberDatesAdded()
    {
        $contactMemberDates = [
            1 => new \DateTime(),
        ];

        $this->campaignLeadRepository->expects($this->once())
            ->method('getInactiveContacts')
            ->willReturn($contactMemberDates);

        $this->leadRepository->expects($this->once())
            ->method('getContactCollection')
            ->willReturn(new ArrayCollection([new Lead()]));

        $contactFinder = $this->getContactFinder();

        $limiter  = new ContactLimiter(0, 0, 0, 0);
        $contacts = $contactFinder->getContacts(1, new Event(), $limiter);
        $this->assertCount(1, $contacts);

        $this->assertEquals($contactMemberDates, $contactFinder->getDatesAdded());
    }

    /**
     * @return InactiveContactFinder
     */
    private function getContactFinder()
    {
        return new InactiveContactFinder(
            $this->leadRepository,
            $this->campaignLeadRepository,
            new NullLogger()
        );
    }
}
