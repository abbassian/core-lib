<?php

declare(strict_types=1);

namespace Autoborna\EmailBundle\Tests\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Autoborna\ChannelBundle\Entity\MessageRepository;
use Autoborna\ChannelBundle\Model\MessageQueueModel;
use Autoborna\CoreBundle\Entity\IpAddress;
use Autoborna\CoreBundle\Helper\CacheStorageHelper;
use Autoborna\CoreBundle\Helper\CoreParametersHelper;
use Autoborna\CoreBundle\Helper\IpLookupHelper;
use Autoborna\CoreBundle\Helper\ThemeHelperInterface;
use Autoborna\CoreBundle\Helper\UserHelper;
use Autoborna\CoreBundle\Security\Permissions\CorePermissions;
use Autoborna\CoreBundle\Test\Doctrine\DBALMocker;
use Autoborna\CoreBundle\Translation\Translator;
use Autoborna\EmailBundle\EmailEvents;
use Autoborna\EmailBundle\Entity\Email;
use Autoborna\EmailBundle\Entity\EmailRepository;
use Autoborna\EmailBundle\Entity\Stat;
use Autoborna\EmailBundle\Entity\StatDevice;
use Autoborna\EmailBundle\Entity\StatRepository;
use Autoborna\EmailBundle\Event\EmailEvent;
use Autoborna\EmailBundle\Helper\MailHelper;
use Autoborna\EmailBundle\Helper\StatsCollectionHelper;
use Autoborna\EmailBundle\Model\EmailModel;
use Autoborna\EmailBundle\Model\SendEmailToContact;
use Autoborna\EmailBundle\MonitoredEmail\Mailbox;
use Autoborna\EmailBundle\Stat\StatHelper;
use Autoborna\LeadBundle\Entity\CompanyRepository;
use Autoborna\LeadBundle\Entity\DoNotContactRepository;
use Autoborna\LeadBundle\Entity\FrequencyRuleRepository;
use Autoborna\LeadBundle\Entity\Lead;
use Autoborna\LeadBundle\Entity\LeadDevice;
use Autoborna\LeadBundle\Entity\LeadList;
use Autoborna\LeadBundle\Model\CompanyModel;
use Autoborna\LeadBundle\Model\DoNotContact;
use Autoborna\LeadBundle\Model\LeadModel;
use Autoborna\LeadBundle\Tracker\ContactTracker;
use Autoborna\LeadBundle\Tracker\DeviceTracker;
use Autoborna\PageBundle\Entity\RedirectRepository;
use Autoborna\PageBundle\Entity\TrackableRepository;
use Autoborna\PageBundle\Model\TrackableModel;
use Autoborna\UserBundle\Model\UserModel;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

class EmailModelTest extends \PHPUnit\Framework\TestCase
{
    const SEGMENT_A = 'segment A';

    const SEGMENT_B = 'segment B';

    /**
     * @var MockObject|IpLookupHelper
     */
    private $ipLookupHelper;

    /**
     * @var MockObject|ThemeHelperInterface
     */
    private $themeHelper;

    /**
     * @var MockObject|Mailbox
     */
    private $mailboxHelper;

    /**
     * @var MockObject|MailHelper
     */
    private $mailHelper;

    /**
     * @var MockObject|LeadModel
     */
    private $leadModel;

    /**
     * @var MockObject|TrackableModel
     */
    private $trackableModel;

    /**
     * @var MockObject|UserModel
     */
    private $userModel;

    /**
     * @var MockObject|UserHelper
     */
    private $userHelper;

    /**
     * @var MockObject|Translator
     */
    private $translator;

    /**
     * @var MockObject|Email
     */
    private $emailEntity;

    /**
     * @var MockObject|EntityManager
     */
    private $entityManager;

    /**
     * @var MockObject|StatRepository
     */
    private $statRepository;

    /**
     * @var MockObject|EmailRepository
     */
    private $emailRepository;

    /**
     * @var MockObject|FrequencyRuleRepository
     */
    private $frequencyRepository;

    /**
     * @var MockObject|MessageQueueModel
     */
    private $messageModel;

    /**
     * @var MockObject|CompanyModel
     */
    private $companyModel;

    /**
     * @var MockObject|CompanyRepository
     */
    private $companyRepository;

    /**
     * @var MockObject|DoNotContact
     */
    private $dncModel;

    /**
     * @var StatHelper
     */
    private $statHelper;

    /**
     * @var SendEmailToContact
     */
    private $sendToContactModel;

    /**
     * @var MockObject|DeviceTracker
     */
    private $deviceTrackerMock;

    /**
     * @var MockObject|RedirectRepository
     */
    private $redirectRepositoryMock;

    /**
     * @var MockObject|CacheStorageHelper
     */
    private $cacheStorageHelperMock;

    /**
     * @var MockObject|ContactTracker
     */
    private $contactTracker;

    /**
     * @var EmailModel
     */
    private $emailModel;

    /**
     * @var MockObject|DoNotContact
     */
    private $doNotContact;

    /**
     * @var MockObject|CorePermissions
     */
    private $corePermissions;

    /**
     * @var StatsCollectionHelper|MockObject
     */
    private $statsCollectionHelper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ipLookupHelper           = $this->createMock(IpLookupHelper::class);
        $this->themeHelper              = $this->createMock(ThemeHelperInterface::class);
        $this->mailboxHelper            = $this->createMock(Mailbox::class);
        $this->mailHelper               = $this->createMock(MailHelper::class);
        $this->leadModel                = $this->createMock(LeadModel::class);
        $this->trackableModel           = $this->createMock(TrackableModel::class);
        $this->userModel                = $this->createMock(UserModel::class);
        $this->userHelper               = $this->createMock(UserHelper::class);
        $this->translator               = $this->createMock(Translator::class);
        $this->emailEntity              = $this->createMock(Email::class);
        $this->entityManager            = $this->createMock(EntityManager::class);
        $this->statRepository           = $this->createMock(StatRepository::class);
        $this->emailRepository          = $this->createMock(EmailRepository::class);
        $this->frequencyRepository      = $this->createMock(FrequencyRuleRepository::class);
        $this->messageModel             = $this->createMock(MessageQueueModel::class);
        $this->companyModel             = $this->createMock(CompanyModel::class);
        $this->companyRepository        = $this->createMock(CompanyRepository::class);
        $this->dncModel                 = $this->createMock(DoNotContact::class);
        $this->statHelper               = new StatHelper($this->statRepository);
        $this->sendToContactModel       = new SendEmailToContact($this->mailHelper, $this->statHelper, $this->dncModel, $this->translator);
        $this->deviceTrackerMock        = $this->createMock(DeviceTracker::class);
        $this->redirectRepositoryMock   = $this->createMock(RedirectRepository::class);
        $this->cacheStorageHelperMock   = $this->createMock(CacheStorageHelper::class);
        $this->contactTracker           = $this->createMock(ContactTracker::class);
        $this->doNotContact             = $this->createMock(DoNotContact::class);
        $this->statsCollectionHelper    = $this->createMock(StatsCollectionHelper::class);
        $this->corePermissions          = $this->createMock(CorePermissions::class);

        $this->emailModel = new EmailModel(
            $this->ipLookupHelper,
            $this->themeHelper,
            $this->mailboxHelper,
            $this->mailHelper,
            $this->leadModel,
            $this->companyModel,
            $this->trackableModel,
            $this->userModel,
            $this->messageModel,
            $this->sendToContactModel,
            $this->deviceTrackerMock,
            $this->redirectRepositoryMock,
            $this->cacheStorageHelperMock,
            $this->contactTracker,
            $this->doNotContact,
            $this->statsCollectionHelper,
            $this->corePermissions
        );

        $this->emailModel->setTranslator($this->translator);
        $this->emailModel->setEntityManager($this->entityManager);
        $this->emailModel->setSecurity($this->corePermissions);
    }

    /**
     * Test that an array of contacts are sent emails according to A/B test weights.
     */
    public function testVariantEmailWeightsAreAppropriateForMultipleContacts(): void
    {
        $this->mailHelper->method('getMailer')->will($this->returnValue($this->mailHelper));
        $this->mailHelper->method('flushQueue')->will($this->returnValue(true));
        $this->mailHelper->method('addTo')->will($this->returnValue(true));
        $this->mailHelper->method('queue')->will($this->returnValue([true, []]));
        $this->mailHelper->method('setEmail')->will($this->returnValue(true));
        $this->translator->expects($this->any())
            ->method('hasId')
            ->will($this->returnValue(false));

        // Setup an email variant email
        $variantDate = new \DateTime();
        $this->emailEntity->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
        $this->emailEntity->method('getTemplate')
            ->will($this->returnValue(''));
        $this->emailEntity->method('getSentCount')
            ->will($this->returnValue(0));
        $this->emailEntity->method('getVariantSentCount')
            ->will($this->returnValue(0));
        $this->emailEntity->method('getVariantStartDate')
            ->will($this->returnValue($variantDate));
        $this->emailEntity->method('getTranslations')
            ->will($this->returnValue([]));
        $this->emailEntity->method('isPublished')
            ->will($this->returnValue(true));
        $this->emailEntity->method('isVariant')
            ->will($this->returnValue(true));

        $this->mailHelper->method('createEmailStat')
            ->will($this->returnCallback(
                function () {
                    $stat = new Stat();
                    $stat->setEmail($this->emailEntity);

                    return $stat;
                }
            ));

        $variantA = $this->createMock(Email::class);
        $variantA->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(2));
        $variantA->method('getTemplate')
            ->will($this->returnValue(''));
        $variantA->method('getSentCount')
            ->will($this->returnValue(0));
        $variantA->method('getVariantSentCount')
            ->will($this->returnValue(0));
        $variantA->method('getVariantStartDate')
            ->will($this->returnValue($variantDate));
        $variantA->method('getTranslations')
            ->will($this->returnValue([]));
        $variantA->method('isPublished')
            ->will($this->returnValue(true));
        $variantA->method('isVariant')
            ->will($this->returnValue(true));
        $variantA->method('getVariantSettings')
            ->will($this->returnValue(['weight' => '25']));

        $variantB = $this->createMock(Email::class);
        $variantB->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(3));
        $variantB->method('getTemplate')
            ->will($this->returnValue(''));
        $variantB->method('getSentCount')
            ->will($this->returnValue(0));
        $variantB->method('getVariantSentCount')
            ->will($this->returnValue(0));
        $variantB->method('getVariantStartDate')
            ->will($this->returnValue($variantDate));
        $variantB->method('getTranslations')
            ->will($this->returnValue([]));
        $variantB->method('isPublished')
            ->will($this->returnValue(true));
        $variantB->method('isVariant')
            ->will($this->returnValue(true));
        $variantB->method('getVariantSettings')
            ->will($this->returnValue(['weight' => '25']));

        $this->emailEntity->method('getVariantChildren')
            ->will($this->returnValue([$variantA, $variantB]));

        $this->emailRepository->method('getDoNotEmailList')
            ->will($this->returnValue([]));

        $this->frequencyRepository->method('getAppliedFrequencyRules')
            ->will($this->returnValue([]));

        $this->entityManager->expects($this->any())
            ->method('getRepository')
            ->will(
                $this->returnValueMap(
                    [
                        ['AutobornaLeadBundle:FrequencyRule', $this->frequencyRepository],
                        ['AutobornaEmailBundle:Email', $this->emailRepository],
                        ['AutobornaEmailBundle:Stat', $this->statRepository],
                    ]
                )
            );

        $this->companyRepository->method('getCompaniesForContacts')
            ->will($this->returnValue([]));

        $this->companyModel->method('getRepository')
            ->willReturn($this->companyRepository);

        $count    = 12;
        $contacts = [];
        while ($count > 0) {
            $contacts[] = [
                'id'        => $count,
                'email'     => "email{$count}@domain.com",
                'firstname' => "firstname{$count}",
                'lastname'  => "lastname{$count}",
            ];
            --$count;
        }

        $this->emailModel->sendEmail($this->emailEntity, $contacts);

        $emailSettings = $this->emailModel->getEmailSettings($this->emailEntity);

        // Sent counts should be as follows
        // ID 1 => 6 50%
        // ID 2 => 3 25%
        // ID 3 => 3 25%

        $counts = [];
        foreach ($emailSettings as $id => $details) {
            $counts[] = "$id:{$details['variantCount']}";
        }
        $counts = implode('; ', $counts);

        $this->assertEquals(6, $emailSettings[1]['variantCount'], $counts);
        $this->assertEquals(3, $emailSettings[2]['variantCount'], $counts);
        $this->assertEquals(3, $emailSettings[3]['variantCount'], $counts);
    }

    /**
     * Test that sending emails to contacts one at a time are according to A/B test weights.
     */
    public function testVariantEmailWeightsAreAppropriateForMultipleContactsSentOneAtATime(): void
    {
        $this->mailHelper->method('getMailer')->will($this->returnValue($this->mailHelper));
        $this->mailHelper->method('flushQueue')->will($this->returnValue(true));
        $this->mailHelper->method('addTo')->will($this->returnValue(true));
        $this->mailHelper->method('queue')->will($this->returnValue([true, []]));
        $this->mailHelper->method('setEmail')->will($this->returnValue(true));
        $this->translator->expects($this->any())
            ->method('hasId')
            ->will($this->returnValue(false));

        // Setup an email variant email
        $variantDate = new \DateTime();
        $this->emailEntity->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
        $this->emailEntity->method('getTemplate')->will($this->returnValue(''));
        $this->emailEntity->method('getSentCount')->will($this->returnValue(0));
        $this->emailEntity->method('getVariantSentCount')->will($this->returnValue(0));
        $this->emailEntity->method('getVariantStartDate')->will($this->returnValue($variantDate));
        $this->emailEntity->method('getTranslations')->will($this->returnValue([]));
        $this->emailEntity->method('isPublished')->will($this->returnValue(true));
        $this->emailEntity->method('isVariant')->will($this->returnValue(true));

        $this->mailHelper->method('createEmailStat')
            ->will($this->returnCallback(
                function () {
                    $stat = new Stat();
                    $stat->setEmail($this->emailEntity);

                    return $stat;
                }
            ));

        $variantA = $this->createMock(Email::class);
        $variantA->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(2));
        $variantA->method('getTemplate')
            ->will($this->returnValue(''));
        $variantA->method('getSentCount')
            ->will($this->returnValue(0));
        $variantA->method('getVariantSentCount')
            ->will($this->returnValue(0));
        $variantA->method('getVariantStartDate')
            ->will($this->returnValue($variantDate));
        $variantA->method('getTranslations')
            ->will($this->returnValue([]));
        $variantA->method('isPublished')
            ->will($this->returnValue(true));
        $variantA->method('isVariant')
            ->will($this->returnValue(true));
        $variantA->method('getVariantSettings')
            ->will($this->returnValue(['weight' => '25']));

        $variantB = $this->createMock(Email::class);
        $variantB->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(3));
        $variantB->method('getTemplate')
            ->will($this->returnValue(''));
        $variantB->method('getSentCount')
            ->will($this->returnValue(0));
        $variantB->method('getVariantSentCount')
            ->will($this->returnValue(0));
        $variantB->method('getVariantStartDate')
            ->will($this->returnValue($variantDate));
        $variantB->method('getTranslations')
            ->will($this->returnValue([]));
        $variantB->method('isPublished')
            ->will($this->returnValue(true));
        $variantB->method('isVariant')
            ->will($this->returnValue(true));
        $variantB->method('getVariantSettings')
            ->will($this->returnValue(['weight' => '25']));

        $this->emailEntity->method('getVariantChildren')
            ->will($this->returnValue([$variantA, $variantB]));

        $this->emailRepository->method('getDoNotEmailList')
            ->will($this->returnValue([]));

        $this->frequencyRepository->method('getAppliedFrequencyRules')
            ->will($this->returnValue([]));

        $this->entityManager->expects($this->any())
            ->method('getRepository')
            ->will(
                $this->returnValueMap(
                    [
                        ['AutobornaLeadBundle:FrequencyRule', $this->frequencyRepository],
                        ['AutobornaEmailBundle:Email', $this->emailRepository],
                        ['AutobornaEmailBundle:Stat', $this->statRepository],
                    ]
                )
            );

        $this->companyRepository->method('getCompaniesForContacts')
            ->will($this->returnValue([]));

        $this->companyModel->method('getRepository')
            ->willReturn($this->companyRepository);

        $count   = 12;
        $results = [];
        while ($count > 0) {
            $contact = [
                'id'        => $count,
                'email'     => "email{$count}@domain.com",
                'firstname' => "firstname{$count}",
                'lastname'  => "lastname{$count}",
            ];
            --$count;

            $results[] = $this->emailModel->sendEmail($this->emailEntity, [$contact]);
        }

        $emailSettings = $this->emailModel->getEmailSettings($this->emailEntity);

        // Sent counts should be as follows
        // ID 1 => 6 50%
        // ID 2 => 3 25%
        // ID 3 => 3 25%

        $counts = [];
        foreach ($emailSettings as $id => $details) {
            $counts[] = "$id:{$details['variantCount']}";
        }
        $counts = implode('; ', $counts);

        $this->assertEquals(6, $emailSettings[1]['variantCount'], $counts);
        $this->assertEquals(3, $emailSettings[2]['variantCount'], $counts);
        $this->assertEquals(3, $emailSettings[3]['variantCount'], $counts);
    }

    /**
     * Test that DoNotContact is honored.
     */
    public function testDoNotContactIsHonored(): void
    {
        $this->translator->expects($this->any())
            ->method('hasId')
            ->will($this->returnValue(false));

        $this->emailRepository->method('getDoNotEmailList')
            ->will($this->returnValue([1 => 'someone@domain.com']));

        $this->entityManager->expects($this->any())
            ->method('getRepository')
            ->will(
                $this->returnValueMap(
                    [
                        ['AutobornaEmailBundle:Email', $this->emailRepository],
                        ['AutobornaEmailBundle:Stat', $this->statRepository],
                        ['AutobornaLeadBundle:FrequencyRule', $this->frequencyRepository],
                    ]
                )
            );

        // If it makes it to the point of calling getContactCompanies then DNC failed
        $this->companyModel->expects($this->exactly(0))
            ->method('getRepository');

        $this->emailEntity->method('getId')
            ->will($this->returnValue(1));

        $this->assertTrue(0 === count($this->emailModel->sendEmail($this->emailEntity, [1 => ['id' => 1, 'email' => 'someone@domain.com']])));
    }

    /**
     * Test that message is queued for a frequency rule value.
     */
    public function testFrequencyRulesAreAppliedAndMessageGetsQueued(): void
    {
        $this->translator->expects($this->any())
            ->method('hasId')
            ->will($this->returnValue(false));

        $this->emailRepository->method('getDoNotEmailList')
            ->will($this->returnValue([]));
        $this->frequencyRepository->method('getAppliedFrequencyRules')
            ->will($this->returnValue([['lead_id' => 1, 'frequency_number' => 1, 'frequency_time' => 'DAY']]));

        $this->entityManager->expects($this->any())
            ->method('getRepository')
            ->will(
                $this->returnValueMap(
                    [
                        ['AutobornaEmailBundle:Email', $this->emailRepository],
                        ['AutobornaEmailBundle:Stat', $this->statRepository],
                        ['AutobornaLeadBundle:FrequencyRule', $this->frequencyRepository],
                        ['AutobornaChannelBundle:MessageQueue', $this->createMock(MessageRepository::class)],
                    ]
                )
            );
        $leadEntity = (new Lead())
            ->setEmail('someone@domain.com');

        $this->entityManager->expects($this->any())
            ->method('getReference')
            ->will(
                $this->returnValue($leadEntity)
            );

        $coreParametersHelper = $this->createMock(CoreParametersHelper::class);

        $messageModel = new MessageQueueModel($this->leadModel, $this->companyModel, $coreParametersHelper);
        $messageModel->setEntityManager($this->entityManager);
        $messageModel->setUserHelper($this->userHelper);
        $messageModel->setDispatcher($this->createMock(EventDispatcher::class));

        $emailModel = new EmailModel(
            $this->ipLookupHelper,
            $this->themeHelper,
            $this->mailboxHelper,
            $this->mailHelper,
            $this->leadModel,
            $this->companyModel,
            $this->trackableModel,
            $this->userModel,
            $messageModel,
            $this->sendToContactModel,
            $this->deviceTrackerMock,
            $this->redirectRepositoryMock,
            $this->cacheStorageHelperMock,
            $this->contactTracker,
            $this->doNotContact,
            $this->statsCollectionHelper,
            $this->corePermissions
        );

        $emailModel->setTranslator($this->translator);
        $emailModel->setEntityManager($this->entityManager);

        $this->emailEntity->method('getId')
            ->will($this->returnValue(1));

        $result = $emailModel->sendEmail(
            $this->emailEntity,
            [
                1 => [
                    'id'        => 1,
                    'email'     => 'someone@domain.com',
                    'firstname' => 'someone',
                    'lastname'  => 'someone',
                ],
            ],
            ['email_type' => 'marketing']
        );
        $this->assertTrue(0 === count($result), print_r($result, true));
    }

    public function testHitEmailSavesEmailStatAndDeviceStatInTwoTransactions(): void
    {
        $contact       = new Lead();
        $stat          = new Stat();
        $request       = new Request();
        $contactDevice = new LeadDevice();
        $ipAddress     = new IpAddress();

        $stat->setLead($contact);

        $this->ipLookupHelper->expects($this->once())
            ->method('getIpAddress')
            ->willReturn($ipAddress);

        $this->deviceTrackerMock->expects($this->once())
            ->method('createDeviceFromUserAgent')
            ->with($contact)
            ->willReturn($contactDevice);

        $this->entityManager->expects($this->exactly(2))
            ->method('persist')
            ->withConsecutive(
                [
                    $this->callback(function ($statDevice) {
                        $this->assertInstanceOf(Stat::class, $statDevice);

                        return true;
                    }),
                ],
                [
                    $this->callback(function ($statDevice) use ($stat, $ipAddress) {
                        $this->assertInstanceOf(StatDevice::class, $statDevice);
                        $this->assertSame($stat, $statDevice->getStat());
                        $this->assertSame($ipAddress, $statDevice->getIpAddress());

                        return true;
                    }),
                ]
            );

        $this->entityManager->expects($this->exactly(2))
            ->method('flush');

        $this->emailModel->setDispatcher($this->createMock(EventDispatcher::class));

        $this->emailModel->hitEmail($stat, $request);
    }

    public function testGetLookupResultsWithNameIsKey(): void
    {
        $this->emailModel->setUserHelper($this->userHelper);
        $this->entityManager->expects($this->once())
            ->method('getRepository')
            ->willReturn($this->emailRepository);

        $this->emailRepository->expects($this->once())
            ->method('getEmailList')
            ->with(
                '',
                0,
                0,
                null,
                false,
                null,
                [],
                null
            )
            ->willReturn([
                [
                    'id'       => 123,
                    'name'     => 'Email 123',
                    'language' => 'EN',
                ],
            ]);

        $this->assertSame(
            ['EN' => ['Email 123' => 123]],
            $this->emailModel->getLookupResults('email', '', 0, 0, ['name_is_key' => true])
        );
    }

    public function testGetLookupResultsWithWithDefaultOptions(): void
    {
        $this->emailModel->setUserHelper($this->userHelper);
        $this->entityManager->expects($this->once())
            ->method('getRepository')
            ->willReturn($this->emailRepository);

        $this->emailRepository->expects($this->once())
            ->method('getEmailList')
            ->with(
                '',
                0,
                0,
                null,
                false,
                null,
                [],
                null
            )
            ->willReturn([
                [
                    'id'       => 123,
                    'name'     => 'Email 123',
                    'language' => 'EN',
                ],
            ]);

        $this->assertSame(
            ['EN' => [123 => 'Email 123']],
            $this->emailModel->getLookupResults('email', '', 0, 0)
        );
    }

    public function testGetEmailListStatsOneSegment()
    {
        $list = $this->createMock(LeadList::class);
        $list->method('getName')->willReturn(self::SEGMENT_A);

        $lists = new ArrayCollection([$list]);

        $result = $this->getEmailListStats($lists);

        self::assertCount(1, $result['datasets']);
        self::assertEquals(self::SEGMENT_A, $result['datasets'][0]['label']);
    }

    public function testGetEmailListStatsTwoSegments()
    {
        $list = $this->createMock(LeadList::class);
        $list->method('getName')->willReturn(self::SEGMENT_A);

        $list2 = $this->createMock(LeadList::class);
        $list2->method('getName')->willReturn(self::SEGMENT_B);

        $lists = new ArrayCollection([$list, $list2]);

        $result = $this->getEmailListStats($lists);

        self::assertCount(3, $result['datasets']);
        self::assertEquals(self::SEGMENT_A, $result['datasets'][1]['label']);
        self::assertEquals(self::SEGMENT_B, $result['datasets'][2]['label']);
    }

    private function getEmailListStats(ArrayCollection $lists)
    {
        $trackableRepo    = $this->createMock(TrackableRepository::class);
        $doNotContactRepo = $this->createMock(DoNotContactRepository::class);

        $this->entityManager->expects($this->any())
            ->method('getRepository')
            ->will(
                $this->returnValueMap(
                    [
                        ['AutobornaEmailBundle:Stat', $this->statRepository],
                        ['AutobornaLeadBundle:DoNotContact', $doNotContactRepo],
                        ['AutobornaPageBundle:Trackable', $trackableRepo],
                    ]
                )
            );

        $this->emailEntity->method('getLists')->willReturn($lists);

        $connection   = $this->createMock(Connection::class);
        $this->entityManager->method('getConnection')->willReturn($connection);

        $dateFromObject = new \DateTime('now');
        $dateToObject   = new \DateTime('-1 month');

        $this->emailEntity->method('getLists')->willReturn($lists);

        return $this->emailModel->getEmailListStats($this->emailEntity, true, $dateFromObject, $dateToObject);
    }

    public function testGetBestHours()
    {
        $dbalMock = new DBALMocker($this);
        $dbalMock->setQueryResponse(
            [
                [
                    'hour'  => 0,
                    'count' => 0,
                ],
                [
                    'hour'  => 1,
                    'count' => 4,
                ],
                [
                    'hour'  => 2,
                    'count' => 10,
                ],
                [
                    'hour'  => 3,
                    'count' => 6,
                ],
            ]
        );
        $mockConnection = $dbalMock->getMockConnection();

        $this->entityManager->method('getConnection')->willReturn($mockConnection);
        $this->emailModel->setEntityManager($this->entityManager);

        $chartData = $this->emailModel->getBestHours(
            'date_read',
            new \DateTime(),
            new \DateTime()
        );

        $this->assertSame([0, 1, 2, 3], $chartData['labels']);
        $this->assertSame([0.0, 20.0, 50.0, 30.0], $chartData['datasets'][0]['data']);
    }

    public function testIsUpdatingTranslationChildren(): void
    {
        $email = new Email();
        $email->setEmailType('list');
        $email->addTranslationChild($child = new Email());
        $userHelper  = $this->createMock(UserHelper::class);
        $this->emailModel->setUserHelper($userHelper);
        $dispatcher = new EventDispatcher();
        $listener   = function (EmailEvent $event) use ($child) {
            $isChild = $event->getEmail() === $child;
            $this->assertSame($isChild, $this->emailModel->isUpdatingTranslationChildren());
        };
        $dispatcher->addListener(EmailEvents::EMAIL_PRE_SAVE, $listener);
        $dispatcher->addListener(EmailEvents::EMAIL_POST_SAVE, $listener);
        $this->emailModel->setDispatcher($dispatcher);
        $emailRepository = $this->createMock(EmailRepository::class);
        $this->entityManager->method('getRepository')->willReturn($emailRepository);
        $this->emailModel->saveEntity($email);
        $this->assertFalse($this->emailModel->isUpdatingTranslationChildren());
    }
}
