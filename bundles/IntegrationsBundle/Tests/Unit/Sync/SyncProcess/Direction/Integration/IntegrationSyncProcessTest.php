<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Tests\Unit\Sync\SyncProcess\Direction\Integration;

use Autoborna\IntegrationsBundle\Sync\DAO\Mapping\MappingManualDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Mapping\ObjectMappingDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Sync\InputOptionsDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Sync\Order\FieldDAO as OrderFieldDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Sync\Order\ObjectChangeDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Sync\Report\FieldDAO as ReportFieldDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Sync\Report\ObjectDAO as ReportObjectDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Sync\Report\ReportDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Sync\Request\ObjectDAO as RequestObjectDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Sync\Request\RequestDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Value\NormalizedValueDAO;
use Autoborna\IntegrationsBundle\Sync\Helper\MappingHelper;
use Autoborna\IntegrationsBundle\Sync\Helper\SyncDateHelper;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Internal\Object\Contact;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\AutobornaSyncDataExchange;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\SyncDataExchangeInterface;
use Autoborna\IntegrationsBundle\Sync\SyncProcess\Direction\Integration\IntegrationSyncProcess;
use Autoborna\IntegrationsBundle\Sync\SyncProcess\Direction\Integration\ObjectChangeGenerator;
use PHPUnit\Framework\TestCase;

class IntegrationSyncProcessTest extends TestCase
{
    private const INTEGRATION_NAME = 'Test';

    /**
     * @var SyncDateHelper|\PHPUnit\Framework\MockObject\MockObject
     */
    private $syncDateHelper;

    /**
     * @var MappingHelper|\PHPUnit\Framework\MockObject\MockObject
     */
    private $mappingHelper;

    /**
     * @var ObjectChangeGenerator|\PHPUnit\Framework\MockObject\MockObject
     */
    private $objectChangeGenerator;

    /**
     * @var SyncDataExchangeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $syncDataExchange;

    protected function setUp(): void
    {
        $this->syncDateHelper        = $this->createMock(SyncDateHelper::class);
        $this->mappingHelper         = $this->createMock(MappingHelper::class);
        $this->objectChangeGenerator = $this->createMock(ObjectChangeGenerator::class);
        $this->syncDataExchange      = $this->createMock(SyncDataExchangeInterface::class);
    }

    public function testThatIntegrationGetSyncReportIsCalledBasedOnRequest(): void
    {
        $objectName    = 'Contact';
        $mappingManual = new MappingManualDAO(self::INTEGRATION_NAME);
        $objectMapping = new ObjectMappingDAO(Contact::NAME, $objectName);
        $objectMapping->addFieldMapping('email', 'email', ObjectMappingDAO::SYNC_BIDIRECTIONALLY, true);
        $objectMapping->addFieldMapping('firstname', 'first_name');
        $mappingManual->addObjectMapping($objectMapping);

        $fromSyncDateTime = new \DateTimeImmutable();
        $this->syncDateHelper->expects($this->once())
            ->method('getSyncFromDateTime')
            ->with(self::INTEGRATION_NAME, $objectName)
            ->willReturn($fromSyncDateTime);

        $toSyncDateTime   = new \DateTimeImmutable();
        $this->syncDateHelper->expects($this->once())
            ->method('getSyncToDateTime')
            ->willReturn($toSyncDateTime);

        $this->syncDateHelper->expects($this->once())
            ->method('getLastSyncDateForObject')
            ->with(self::INTEGRATION_NAME, $objectName)
            ->willReturn(null);

        // SyncDateExchangeInterface::getSyncReport should sync because an object was added to the report
        $this->syncDataExchange->expects($this->once())
            ->method('getSyncReport')
            ->willReturnCallback(
                function (RequestDAO $requestDAO) use ($objectName) {
                    $requestObjects = $requestDAO->getObjects();
                    $this->assertCount(1, $requestObjects);

                    /** @var RequestObjectDAO $requestObject */
                    $requestObject = $requestObjects[0];
                    $this->assertEquals(['email'], $requestObject->getRequiredFields());
                    $this->assertEquals(['email', 'first_name'], $requestObject->getFields());
                    $this->assertEquals($objectName, $requestObject->getObject());

                    return new ReportDAO(self::INTEGRATION_NAME);
                }
            );

        $this->getSyncProcess($mappingManual)->getSyncReport(1);
    }

    public function testThatIntegrationGetSyncReportIsNotCalledBasedOnRequest(): void
    {
        $objectName    = 'Contact';
        $mappingManual = new MappingManualDAO(self::INTEGRATION_NAME);

        $this->syncDateHelper->expects($this->never())
            ->method('getSyncFromDateTime')
            ->with(self::INTEGRATION_NAME, $objectName);

        // SyncDateExchangeInterface::getSyncReport should sync because an object was added to the report
        $this->syncDataExchange->expects($this->never())
            ->method('getSyncReport');

        $report = $this->getSyncProcess($mappingManual)->getSyncReport(1);

        $this->assertEquals(self::INTEGRATION_NAME, $report->getIntegration());
    }

    public function testOrderIsBuiltBasedOnMapping(): void
    {
        $objectName    = 'Contact';
        $mappingManual = new MappingManualDAO(self::INTEGRATION_NAME);
        $objectMapping = new ObjectMappingDAO(Contact::NAME, $objectName);
        $objectMapping->addFieldMapping('email', 'email', ObjectMappingDAO::SYNC_BIDIRECTIONALLY, true);
        $objectMapping->addFieldMapping('firstname', 'first_name');
        $mappingManual->addObjectMapping($objectMapping);

        $toSyncDateTime = new \DateTimeImmutable();
        $this->syncDateHelper->expects($this->once())
            ->method('getSyncDateTime')
            ->willReturn($toSyncDateTime);

        $syncReport = new ReportDAO(AutobornaSyncDataExchange::NAME);
        $objectDAO  = new ReportObjectDAO(Contact::NAME, 1);
        $objectDAO->addField(new ReportFieldDAO('email', new NormalizedValueDAO(NormalizedValueDAO::EMAIL_TYPE, 'test@test.com')));
        $objectDAO->addField(new ReportFieldDAO('firstname', new NormalizedValueDAO(NormalizedValueDAO::TEXT_TYPE, 'Bob')));
        $syncReport->addObject($objectDAO);

        // It should search for an integration object mapped to an internal object
        $this->mappingHelper->expects($this->once())
            ->method('findIntegrationObject')
            ->with(self::INTEGRATION_NAME, $objectName, $objectDAO)
            ->willReturn(
                new ReportObjectDAO($objectName, 2)
            );

        $objectChangeDAO = new ObjectChangeDAO(self::INTEGRATION_NAME, $objectName, 2, Contact::NAME, 1);
        $objectChangeDAO->addField(new OrderFieldDAO('email', new NormalizedValueDAO(NormalizedValueDAO::EMAIL_TYPE, 'test@test.com')));
        $objectChangeDAO->addField(new OrderFieldDAO('first_name', new NormalizedValueDAO(NormalizedValueDAO::TEXT_TYPE, 'Bob')));
        $this->objectChangeGenerator->expects($this->once())
            ->method('getSyncObjectChange')
            ->willReturn($objectChangeDAO);

        $syncOrder = $this->getSyncProcess($mappingManual)->getSyncOrder($syncReport);

        // The change should have been added to the order as an identified object
        $this->assertEquals([$objectName => [2 => $objectChangeDAO]], $syncOrder->getIdentifiedObjects());
    }

    /**
     * @return IntegrationSyncProcess
     */
    private function getSyncProcess(MappingManualDAO $mappingManualDAO)
    {
        $syncProcess = new IntegrationSyncProcess($this->syncDateHelper, $this->mappingHelper, $this->objectChangeGenerator);

        $syncProcess->setupSync(new InputOptionsDAO(['integration' => self::INTEGRATION_NAME]), $mappingManualDAO, $this->syncDataExchange);

        return $syncProcess;
    }
}
