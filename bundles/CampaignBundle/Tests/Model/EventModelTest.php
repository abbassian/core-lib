<?php

declare(strict_types=1);

namespace Autoborna\CampaignBundle\Tests\Model;

use Autoborna\CampaignBundle\Entity\LeadEventLogRepository;
use Autoborna\CampaignBundle\Model\EventModel;
use PHPUnit\Framework\TestCase;

class EventModelTest extends TestCase
{
    /**
     * @var LeadEventLogRepository
     */
    private $leadEventLogRepository;

    /**
     * @var EventModel
     */
    private $eventModel;

    protected function setUp(): void
    {
        $this->eventModel = $this->getMockBuilder(EventModel::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'getRepository',
                'getLeadEventLogRepository',
                'deleteEntities',
            ])
            ->getMock();

        $this->leadEventLogRepository = $this->createMock(LeadEventLogRepository::class);
    }

    public function testThatClonedEventsDoNotAttemptNullingParentInDeleteEvents(): void
    {
        $this->eventModel->expects($this->exactly(0))
            ->method('getRepository')
            ->willReturn($this->leadEventLogRepository);

        $currentEvents = [
            'new1',
            'new2',
            'new3',
        ];

        $deletedEvents = [
            'new1',
        ];

        $this->eventModel->deleteEvents($currentEvents, $deletedEvents);
    }

    public function testThatItDeletesEventLogs(): void
    {
        $idToDelete = 'old1';

        $this->eventModel->expects($this->once())
            ->method('getRepository')
            ->willReturn($this->leadEventLogRepository);

        $this->eventModel->expects($this->once())
            ->method('getLeadEventLogRepository')
            ->willReturn($this->leadEventLogRepository);

        $this->leadEventLogRepository->expects($this->once())
            ->method('removeEventLogs')
            ->with($idToDelete);

        $this->eventModel->expects($this->once())
            ->method('deleteEntities')
            ->with([$idToDelete]);

        $currentEvents = [
            'new1',
        ];

        $deletedEvents = [
            'new1',
            $idToDelete,
        ];

        $this->eventModel->deleteEvents($currentEvents, $deletedEvents);
    }
}
