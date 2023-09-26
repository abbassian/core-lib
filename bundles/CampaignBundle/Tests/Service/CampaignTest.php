<?php

namespace Autoborna\CampaignBundle\Tests\Service;

use Autoborna\CampaignBundle\Entity\CampaignRepository;
use Autoborna\CampaignBundle\Service\Campaign;
use Autoborna\EmailBundle\Entity\EmailRepository;

class CampaignTest extends \PHPUnit\Framework\TestCase
{
    public function testHasUnpublishedEmail()
    {
        $campaignId         = 1;
        $campaignRepository = $this->createMock(CampaignRepository::class);
        $campaignRepository
            ->expects($this->once())
            ->method('fetchEmailIdsById')
            ->with($campaignId)
            ->willReturn([]);
        $emailRepository = $this->createMock(EmailRepository::class);
        $campaignService = new Campaign($campaignRepository, $emailRepository);

        $this->assertFalse($campaignService->hasUnpublishedEmail($campaignId));

        $emailIds             = [1, 2.3];
        $hasUnpublishedEmails = true;
        $campaignRepository   = $this->createMock(CampaignRepository::class);
        $campaignRepository
            ->expects($this->once())
            ->method('fetchEmailIdsById')
            ->with($campaignId)
            ->willReturn($emailIds);
        $emailRepository = $this->createMock(EmailRepository::class);
        $emailRepository
            ->expects($this->once())
            ->method('isOneUnpublished')
            ->with($emailIds)
            ->willReturn($hasUnpublishedEmails);
        $campaignService = new Campaign($campaignRepository, $emailRepository);
        $this->assertTrue($campaignService->hasUnpublishedEmail($campaignId));
    }
}
