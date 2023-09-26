<?php

namespace Autoborna\CampaignBundle\Tests\Executioner\Logger;

use Doctrine\Common\Collections\ArrayCollection;
use Autoborna\CampaignBundle\Entity\Campaign;
use Autoborna\CampaignBundle\Entity\Event;
use Autoborna\CampaignBundle\Entity\LeadEventLog;
use Autoborna\CampaignBundle\Entity\LeadEventLogRepository;
use Autoborna\CampaignBundle\Entity\LeadRepository;
use Autoborna\CampaignBundle\Executioner\Logger\EventLogger;
use Autoborna\CampaignBundle\Model\SummaryModel;
use Autoborna\CoreBundle\Entity\IpAddress;
use Autoborna\CoreBundle\Helper\IpLookupHelper;
use Autoborna\LeadBundle\Entity\Lead;
use Autoborna\LeadBundle\Tracker\ContactTracker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EventLoggerTest extends TestCase
{
    /**
     * @var LeadRepository|MockObject
     */
    private $ipLookupHelper;

    /**
     * @var ContactTracker|MockObject
     */
    private $contactTracker;

    /**
     * @var LeadEventLogRepository|MockObject
     */
    private $leadEventLogRepository;

    /**
     * @var LeadRepository|MockObject
     */
    private $leadRepository;

    /**
     * @var SummaryModel|MockObject
     */
    private $summaryModel;

    protected function setUp(): void
    {
        $this->ipLookupHelper         = $this->createMock(IpLookupHelper::class);
        $this->contactTracker         = $this->createMock(ContactTracker::class);
        $this->leadEventLogRepository = $this->createMock(LeadEventLogRepository::class);
        $this->leadRepository         = $this->createMock(LeadRepository::class);
        $this->summaryModel           = $this->createMock(SummaryModel::class);
    }

    public function testAllLogsAreReturnedWithFinalPersist(): void
    {
        $logCollection = new ArrayCollection();
        while ($logCollection->count() < 60) {
            $log = $this->createMock(LeadEventLog::class);
            $log->method('getId')
                ->willReturn($logCollection->count() + 1);

            $logCollection->add($log);
        }

        $this->leadEventLogRepository->expects($this->exactly(3))
            ->method('saveEntities');

        $logger = $this->getLogger();
        foreach ($logCollection as $log) {
            $logger->queueToPersist($log);
        }

        $persistedLogs = $logger->persistQueuedLogs();

        $this->assertEquals($persistedLogs->count(), $logCollection->count());
        $this->assertEquals($logCollection->getValues(), $persistedLogs->getValues());
    }

    public function testBuildLogEntry()
    {
        $this->ipLookupHelper->method('getIpAddress')->willReturn(new IpAddress());

        $this->leadRepository->expects($this->exactly(3))
            ->method('getContactRotations')
            ->willReturnOnConsecutiveCalls([1 => 1], [1 => 2], [1 => 1]);

        $campaign = $this->createMock(Campaign::class);
        $campaign->method('getId')->willReturnOnConsecutiveCalls([1, 1, 2]);

        $event = $this->createMock(Event::class);
        $event->method('getCampaign')->willReturn($campaign);

        $contact = $this->createMock(Lead::class);
        $contact->method('getId')->willReturn(1);

        // rotation for campaign 1 and contact 1
        $log = $this->getLogger()->buildLogEntry($event, $contact, false);
        $this->assertEquals(1, $log->getRotation());

        // rotation for campaign 1 and contact 1
        $log = $this->getLogger()->buildLogEntry($event, $contact, false);
        $this->assertEquals(2, $log->getRotation());

        // rotation for campaign 2 and contact 1
        $log = $this->getLogger()->buildLogEntry($event, $contact, false);
        $this->assertEquals(1, $log->getRotation());
    }

    private function getLogger(): EventLogger
    {
        return new EventLogger(
            $this->ipLookupHelper,
            $this->contactTracker,
            $this->leadEventLogRepository,
            $this->leadRepository,
            $this->summaryModel
        );
    }
}
