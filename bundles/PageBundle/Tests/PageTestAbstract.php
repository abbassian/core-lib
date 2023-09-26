<?php

namespace Autoborna\PageBundle\Tests;

use Doctrine\ORM\EntityManager;
use Autoborna\CoreBundle\Helper\CookieHelper;
use Autoborna\CoreBundle\Helper\CoreParametersHelper;
use Autoborna\CoreBundle\Helper\IpLookupHelper;
use Autoborna\CoreBundle\Helper\UrlHelper;
use Autoborna\CoreBundle\Helper\UserHelper;
use Autoborna\CoreBundle\Translation\Translator;
use Autoborna\LeadBundle\Model\CompanyModel;
use Autoborna\LeadBundle\Model\FieldModel;
use Autoborna\LeadBundle\Model\LeadModel;
use Autoborna\LeadBundle\Tracker\ContactTracker;
use Autoborna\LeadBundle\Tracker\DeviceTracker;
use Autoborna\PageBundle\Entity\HitRepository;
use Autoborna\PageBundle\Entity\PageRepository;
use Autoborna\PageBundle\Model\PageModel;
use Autoborna\PageBundle\Model\RedirectModel;
use Autoborna\PageBundle\Model\TrackableModel;
use Autoborna\QueueBundle\Queue\QueueService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

class PageTestAbstract extends WebTestCase
{
    protected static $mockId   = 123;
    protected static $mockName = 'Mock test name';
    protected $mockTrackingId;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->mockTrackingId = hash('sha1', uniqid(mt_rand(), true));
    }

    /**
     * @return PageModel
     */
    protected function getPageModel($transliterationEnabled = true)
    {
        $cookieHelper = $this
            ->getMockBuilder(CookieHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $router = self::$container->get('router');

        $ipLookupHelper = $this
            ->getMockBuilder(IpLookupHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $leadModel = $this
            ->getMockBuilder(LeadModel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $leadFieldModel = $this
            ->getMockBuilder(FieldModel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $redirectModel = $this->getRedirectModel();

        $companyModel = $this
            ->getMockBuilder(CompanyModel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $trackableModel = $this
            ->getMockBuilder(TrackableModel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dispatcher = $this
            ->getMockBuilder(EventDispatcher::class)
            ->disableOriginalConstructor()
            ->getMock();

        $translator = $this
            ->getMockBuilder(Translator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $entityManager = $this
            ->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $pageRepository = $this
            ->getMockBuilder(PageRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $coreParametersHelper = $this
            ->getMockBuilder(CoreParametersHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $hitRepository = $this->createMock(HitRepository::class);
        $userHelper    = $this->createMock(UserHelper::class);

        $queueService = $this
            ->getMockBuilder(QueueService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $contactTracker = $this->createMock(ContactTracker::class);

        $contactTracker->expects($this
            ->any())
            ->method('getContact')
            ->willReturn($this
                ->returnValue(['id' => self::$mockId, 'name' => self::$mockName])
            );

        $queueService->expects($this
            ->any())
            ->method('isQueueEnabled')
            ->will(
                $this->returnValue(false)
            );

        $entityManager->expects($this
            ->any())
            ->method('getRepository')
            ->will(
                $this->returnValueMap(
                    [
                        ['AutobornaPageBundle:Page', $pageRepository],
                        ['AutobornaPageBundle:Hit', $hitRepository],
                    ]
                )
            );

        $coreParametersHelper->expects($this->any())
                ->method('get')
                ->with('transliterate_page_title')
                ->willReturn($transliterationEnabled);

        $deviceTrackerMock = $this->createMock(DeviceTracker::class);

        $pageModel = new PageModel(
            $cookieHelper,
            $ipLookupHelper,
            $leadModel,
            $leadFieldModel,
            $redirectModel,
            $trackableModel,
            $queueService,
            $companyModel,
            $deviceTrackerMock,
            $contactTracker,
            $coreParametersHelper
        );

        $pageModel->setDispatcher($dispatcher);
        $pageModel->setTranslator($translator);
        $pageModel->setEntityManager($entityManager);
        $pageModel->setRouter($router);
        $pageModel->setUserHelper($userHelper);

        return $pageModel;
    }

    /**
     * @return RedirectModel
     */
    protected function getRedirectModel()
    {
        $urlHelper = $this
            ->getMockBuilder(UrlHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockRedirectModel = $this->getMockBuilder('Autoborna\PageBundle\Model\RedirectModel')
            ->setConstructorArgs([$urlHelper])
            ->setMethods(['createRedirectEntity', 'generateRedirectUrl'])
            ->getMock();

        $mockRedirect = $this->getMockBuilder('Autoborna\PageBundle\Entity\Redirect')
            ->getMock();

        $mockRedirectModel->expects($this->any())
            ->method('createRedirectEntity')
            ->willReturn($mockRedirect);

        $mockRedirectModel->expects($this->any())
            ->method('generateRedirectUrl')
            ->willReturn('http://some-url.com');

        return $mockRedirectModel;
    }
}
