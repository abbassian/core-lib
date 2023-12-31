<?php

declare(strict_types=1);

namespace Autoborna\CampaignBundle\Tests\EventListener;

use Autoborna\CampaignBundle\CampaignEvents;
use Autoborna\CampaignBundle\Entity\Campaign;
use Autoborna\CampaignBundle\Event\CampaignEvent;
use Autoborna\CampaignBundle\EventListener\CampaignSubscriber;
use Autoborna\CampaignBundle\Service\Campaign as CampaignService;
use Autoborna\CoreBundle\Helper\IpLookupHelper;
use Autoborna\CoreBundle\Model\AuditLogModel;
use Autoborna\CoreBundle\Service\FlashBag;
use PHPUnit\Framework\TestCase;

class CampaignSubscriberTest extends TestCase
{
    private $ipLookupHelper;
    private $auditLogModel;
    private $campaignService;
    private $flashBag;

    /**
     * @var CampaignSubscriber
     */
    private $subscriber;

    public function setUp(): void
    {
        parent::setUp();

        $this->ipLookupHelper  = $this->createMock(IpLookupHelper::class);
        $this->auditLogModel   = $this->createMock(AuditLogModel::class);
        $this->campaignService = $this->createMock(CampaignService::class);
        $this->flashBag        = $this->createMock(FlashBag::class);

        $this->subscriber = new CampaignSubscriber(
            $this->ipLookupHelper,
            $this->auditLogModel,
            $this->campaignService,
            $this->flashBag
        );
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertEquals(
            [
                CampaignEvents::CAMPAIGN_POST_SAVE     => ['onCampaignPostSave', 0],
                CampaignEvents::CAMPAIGN_POST_DELETE   => ['onCampaignDelete', 0],
            ],
            CampaignSubscriber::getSubscribedEvents()
        );
    }

    public function testOnCampaignPostSaveNothingHappened(): void
    {
        $campaign            = new Campaign();
        $event               = new CampaignEvent($campaign);

        $this->auditLogModel->expects($this->never())
            ->method('writeToLog');

        $this->subscriber->onCampaignPostSave($event);
    }

    public function testOnCampaignPostSaveUnpublished(): void
    {
        $ipAddress    = 'someIp';

        $dateTime = new \DateTime();
        $dateTime->setTimestamp(1597752193);

        $campaign = new Campaign();
        $campaign->setPublishDown($dateTime);

        $event = new CampaignEvent($campaign);

        $this->ipLookupHelper->expects($this->once())
            ->method('getIpAddressFromRequest')
            ->willReturn($ipAddress);

        $expectedLog = [
            'bundle'    => 'campaign',
            'object'    => 'campaign',
            'objectId'  => $campaign->getId(),
            'action'    => 'update',
            'details'   => [
                'publishDown' => [
                    0 => null,
                    1 => '2020-08-18T12:03:13+00:00',
                ],
            ],
            'ipAddress' => $ipAddress,
        ];

        $this->auditLogModel->expects($this->once())
            ->method('writeToLog')
            ->with($expectedLog);

        $this->subscriber->onCampaignPostSave($event);
    }

    public function testOnCampaignPostSaveCreateFlash(): void
    {
        $ipAddress    = 'someIp';
        $campaignName = 'campaignName';

        $dateTime = new \DateTime();
        $dateTime->setTimestamp(1597752193);

        $campaign = new Campaign();
        $campaign->setPublishUp($dateTime);
        $campaign->setName($campaignName);

        $event = new CampaignEvent($campaign);

        $this->campaignService->expects($this->once())
            ->method('hasUnpublishedEmail')
            ->with(null)
            ->willReturn(true);

        $this->flashBag->expects($this->once())
            ->method('add')
            ->with(
                'autoborna.core.notice.campaign.unpublished.email',
                [
                    '%name%' => $campaign->getName(),
                ]
            );

        $this->ipLookupHelper->expects($this->once())
            ->method('getIpAddressFromRequest')
            ->willReturn($ipAddress);

        $expectedLog = [
            'action'    => 'update',
            'bundle'    => 'campaign',
            'details'   => [
                'name' => [
                    0 => null,
                    1 => $campaignName,
                ],
                'publishUp' => [
                    0 => null,
                    1 => '2020-08-18T12:03:13+00:00',
                ],
            ],
            'ipAddress' => $ipAddress,
            'object'    => 'campaign',
            'objectId'  => $campaign->getId(),
        ];

        $this->auditLogModel->expects($this->once())
            ->method('writeToLog')
            ->with($expectedLog);

        $this->subscriber->onCampaignPostSave($event);
    }

    public function testOnCampaignDelete(): void
    {
        $deletedId    = 1;
        $campaignName = 'campaignName';
        $ipAddress    = 'someIp';

        $campaign            = new Campaign();
        $campaign->deletedId = $deletedId;
        $campaign->setName($campaignName);

        $event = new CampaignEvent($campaign);

        $this->ipLookupHelper->expects($this->once())
            ->method('getIpAddressFromRequest')
            ->willReturn($ipAddress);

        $expectedLog = [
            'bundle'    => 'campaign',
            'object'    => 'campaign',
            'objectId'  => $deletedId,
            'action'    => 'delete',
            'details'   => ['name' => $campaignName],
            'ipAddress' => $ipAddress,
        ];

        $this->auditLogModel->expects($this->once())
            ->method('writeToLog')
            ->with($expectedLog);

        $this->subscriber->onCampaignDelete($event);
    }
}
