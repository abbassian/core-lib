<?php

namespace Autoborna\PluginBundle\Tests\Integration;

use Doctrine\ORM\EntityManager;
use Autoborna\CoreBundle\Helper\CacheStorageHelper;
use Autoborna\CoreBundle\Helper\EncryptionHelper;
use Autoborna\CoreBundle\Helper\PathsHelper;
use Autoborna\CoreBundle\Model\NotificationModel;
use Autoborna\LeadBundle\Model\CompanyModel;
use Autoborna\LeadBundle\Model\DoNotContact;
use Autoborna\LeadBundle\Model\FieldModel;
use Autoborna\LeadBundle\Model\LeadModel;
use Autoborna\PluginBundle\Model\IntegrationEntityModel;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Router;
use Symfony\Component\Translation\TranslatorInterface;

class AbstractIntegrationTestCase extends TestCase
{
    /**
     * @var EventDispatcherInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $dispatcher;

    /**
     * @var CacheStorageHelper|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $cache;

    /**
     * @var EntityManager|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $em;

    /**
     * @var Session|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $session;

    /**
     * @var RequestStack|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $request;

    /**
     * @var Router|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $router;

    /**
     * @var TranslatorInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $translator;

    /**
     * @var Logger|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $logger;

    /**
     * @var EncryptionHelper|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $encryptionHelper;

    /**
     * @var LeadModel|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $leadModel;

    /**
     * @var CompanyModel|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $companyModel;

    /**
     * @var PathsHelper|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $pathsHelper;

    /**
     * @var NotificationModel|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $notificationModel;

    /**
     * @var FieldModel|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $fieldModel;

    /**
     * @var IntegrationEntityModel|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $integrationEntityModel;

    /**
     * @var DoNotContact|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $doNotContact;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dispatcher             = $this->createMock(EventDispatcherInterface::class);
        $this->cache                  = $this->createMock(CacheStorageHelper::class);
        $this->em                     = $this->createMock(EntityManager::class);
        $this->session                = $this->createMock(Session::class);
        $this->request                = $this->createMock(RequestStack::class);
        $this->router                 = $this->createMock(Router::class);
        $this->translator             = $this->createMock(TranslatorInterface::class);
        $this->logger                 = $this->createMock(Logger::class);
        $this->encryptionHelper       = $this->createMock(EncryptionHelper::class);
        $this->leadModel              = $this->createMock(LeadModel::class);
        $this->companyModel           = $this->createMock(CompanyModel::class);
        $this->pathsHelper            = $this->createMock(PathsHelper::class);
        $this->notificationModel      = $this->createMock(NotificationModel::class);
        $this->fieldModel             = $this->createMock(FieldModel::class);
        $this->integrationEntityModel = $this->createMock(IntegrationEntityModel::class);
        $this->doNotContact           = $this->createMock(DoNotContact::class);
    }
}
