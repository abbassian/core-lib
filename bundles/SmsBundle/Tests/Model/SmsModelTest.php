<?php

declare(strict_types=1);

namespace Autoborna\SmsBundle\Tests\Model;

use Doctrine\ORM\EntityManager;
use Autoborna\ChannelBundle\Model\MessageQueueModel;
use Autoborna\CoreBundle\Helper\CacheStorageHelper;
use Autoborna\CoreBundle\Security\Permissions\CorePermissions;
use Autoborna\LeadBundle\Entity\Lead;
use Autoborna\LeadBundle\Model\LeadModel;
use Autoborna\PageBundle\Model\TrackableModel;
use Autoborna\SmsBundle\Entity\Sms;
use Autoborna\SmsBundle\Entity\SmsRepository;
use Autoborna\SmsBundle\Form\Type\SmsType;
use Autoborna\SmsBundle\Model\SmsModel;
use Autoborna\SmsBundle\Sms\TransportChain;
use PHPUnit\Framework\MockObject\MockObject;

class SmsModelTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var MockObject|CacheStorageHelper
     */
    private $cacheStorageHelper;

    /**
     * @var MockObject|EntityManager
     */
    private $entityManger;

    /**
     * @var MockObject|LeadModel
     */
    private $leadModel;

    /**
     * @var MockObject|MessageQueueModel
     */
    private $messageQueueModel;

    /**
     * @var MockObject|TrackableModel
     */
    private $pageTrackableModel;

    /**
     * @var MockObject|TransportChain
     */
    private $transport;

    private SmsModel $smsModel;

    protected function setUp(): void
    {
        $this->pageTrackableModel = $this->createMock(TrackableModel::class);
        $this->leadModel          = $this->createMock(LeadModel::class);
        $this->messageQueueModel  = $this->createMock(MessageQueueModel::class);
        $this->transport          = $this->createMock(TransportChain::class);
        $this->cacheStorageHelper = $this->createMock(CacheStorageHelper::class);
        $this->entityManger       = $this->createMock(EntityManager::class);
        $this->smsModel           = new SmsModel(
            $this->pageTrackableModel,
            $this->leadModel,
            $this->messageQueueModel,
            $this->transport,
            $this->cacheStorageHelper
        );
    }

    /**
     * Test to get lookup results when class name is sent as a parameter.
     */
    public function testGetLookupResultsWhenTypeIsClass(): void
    {
        $entities = [['name' => 'Autoborna', 'id' => 1, 'language' => 'cs']];

        /** @var MockObject|SmsRepository $repositoryMock */
        $repositoryMock = $this->createMock(SmsRepository::class);
        $repositoryMock->method('getSmsList')
            ->with('', 10, 0, true, false)
            ->willReturn($entities);

        // Partial mock, mocks just getRepository
        /** @var MockObject|SmsModel $smsModel */
        $smsModel = $this->getMockBuilder(SmsModel::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getRepository'])
            ->getMock();
        $smsModel->method('getRepository')
            ->willReturn($repositoryMock);

        $securityMock = $this->createMock(CorePermissions::class);

        $securityMock->method('isGranted')
            ->with('sms:smses:viewother')
            ->willReturn(true);
        $smsModel->setSecurity($securityMock);

        $textMessages = $smsModel->getLookupResults(SmsType::class);
        $this->assertSame('Autoborna', $textMessages['cs'][1], 'Autoborna is the right text message name');
    }

    public function testSendSmsNotPublished(): void
    {
        $sms = new Sms();
        $sms->setIsPublished(false);
        $lead = new Lead();
        $lead->setId(1);
        $this->smsModel->setEntityManager($this->entityManger);
        $results = $this->smsModel->sendSms($sms, $lead);
        self::assertFalse((bool) $results[1]['sent']);
        self::assertSame('autoborna.sms.campaign.failed.unpublished', $results[1]['status']);
    }
}
