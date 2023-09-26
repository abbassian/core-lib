<?php

namespace Autoborna\LeadBundle\Tests;

use Autoborna\CoreBundle\Helper\UserHelper;
use Autoborna\CoreBundle\Model\NotificationModel;
use Autoborna\CoreBundle\Tests\CommonMocks;
use Autoborna\LeadBundle\Entity\Import;
use Autoborna\LeadBundle\Entity\ImportRepository;
use Autoborna\LeadBundle\Entity\LeadEventLogRepository;
use Autoborna\LeadBundle\Model\CompanyModel;
use Autoborna\LeadBundle\Model\ImportModel;
use Autoborna\LeadBundle\Model\LeadModel;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\EventDispatcher\EventDispatcher;

abstract class StandardImportTestHelper extends CommonMocks
{
    protected $eventEntities = [];
    protected static $csvPath;
    protected static $largeCsvPath;

    protected static $initialList = [
        ['email', 'firstname', 'lastname'],
        ['john@doe.email', 'John', 'Doe'],
        ['bad.@doe.email', 'Bad', 'Doe'],
        ['donald@doe.email', 'Don', 'Doe'],
        [''],
        ['ella@doe.email', 'Ella', 'Doe'],
    ];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        static::generateSmallCSV();
        static::generateLargeCSV();
    }

    public static function tearDownAfterClass(): void
    {
        if (file_exists(self::$csvPath)) {
            unlink(self::$csvPath);
        }

        if (file_exists(self::$largeCsvPath)) {
            unlink(self::$largeCsvPath);
        }

        parent::tearDownAfterClass();
    }

    public static function generateSmallCSV()
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'autoborna_import_test_');
        $file    = fopen($tmpFile, 'w');

        foreach (self::$initialList as $line) {
            fputcsv($file, $line);
        }

        fclose($file);
        self::$csvPath = $tmpFile;
    }

    public static function generateLargeCSV()
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'autoborna_import_large_test_');
        $file    = fopen($tmpFile, 'w');
        fputcsv($file, ['email', 'firstname', 'lastname']);
        $counter = 510;
        while ($counter) {
            fputcsv($file, [uniqid().'@gmail.com', uniqid(), uniqid()]);

            --$counter;
        }

        fclose($file);
        self::$largeCsvPath = $tmpFile;
    }

    public function setUp(): void
    {
        defined('MAUTIC_ENV') or define('MAUTIC_ENV', 'test');

        $this->eventEntities = [];
    }

    /**
     * @return Import&MockObject
     */
    protected function initImportEntity(array $methods = null)
    {
        /** @var Import&MockObject $entity */
        $entity = $this->getMockBuilder(Import::class)
            ->setMethods($methods)
            ->getMock();

        $entity->setFilePath(self::$csvPath)
            ->setLineCount(count(self::$initialList))
            ->setHeaders(self::$initialList[0])
            ->setParserConfig(
                [
                    'batchlimit' => 10,
                    'delimiter'  => ',',
                    'enclosure'  => '"',
                    'escape'     => '/',
                ]
            );

        return $entity;
    }

    /**
     * Initialize the ImportModel object.
     *
     * @return ImportModel
     */
    protected function initImportModel()
    {
        $translator           = $this->getTranslatorMock();
        $pathsHelper          = $this->getPathsHelperMock();
        $entityManager        = $this->getEntityManagerMock();
        $coreParametersHelper = $this->getCoreParametersHelperMock();

        /** @var MockObject&UserHelper */
        $userHelper = $this->createMock(UserHelper::class);

        /** @var MockObject&LeadEventLogRepository */
        $logRepository = $this->createMock(LeadEventLogRepository::class);

        /** @var MockObject&ImportRepository */
        $importRepository = $this->createMock(ImportRepository::class);

        $importRepository->method('getValue')
            ->willReturn(true);

        $entityManager->expects($this->any())
            ->method('getRepository')
            ->will(
                $this->returnValueMap(
                    [
                        ['AutobornaLeadBundle:LeadEventLog', $logRepository],
                        ['AutobornaLeadBundle:Import', $importRepository],
                    ]
                )
            );

        $entityManager->expects($this->any())
            ->method('isOpen')
            ->willReturn(true);

        /** @var MockObject&LeadModel */
        $leadModel = $this->createMock(LeadModel::class);

        $leadModel->setEntityManager($entityManager);

        $leadModel->expects($this->any())
            ->method('getEventLogRepository')
            ->will($this->returnValue($logRepository));

        /** @var MockObject&CompanyModel */
        $companyModel = $this->createMock(CompanyModel::class);

        $companyModel->setEntityManager($entityManager);

        /** @var MockObject&NotificationModel */
        $notificationModel = $this->createMock(NotificationModel::class);

        $notificationModel->setEntityManager($entityManager);

        $importModel = new ImportModel($pathsHelper, $leadModel, $notificationModel, $coreParametersHelper, $companyModel);
        $importModel->setEntityManager($entityManager);
        $importModel->setTranslator($translator);
        $importModel->setUserHelper($userHelper);
        $importModel->setDispatcher(new EventDispatcher());

        return $importModel;
    }
}
