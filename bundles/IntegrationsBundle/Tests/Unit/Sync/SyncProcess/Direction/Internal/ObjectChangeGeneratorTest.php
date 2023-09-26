<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Tests\Unit\Sync\SyncProcess\Direction\Internal;

use Autoborna\IntegrationsBundle\Sync\DAO\Mapping\MappingManualDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Mapping\ObjectMappingDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Sync\InformationChangeRequestDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Sync\Report\FieldDAO as ReportFieldDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Sync\Report\ObjectDAO as ReportObjectDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Sync\Report\ReportDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Value\NormalizedValueDAO;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Helper\FieldHelper;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Internal\Object\Contact;
use Autoborna\IntegrationsBundle\Sync\SyncJudge\SyncJudgeInterface;
use Autoborna\IntegrationsBundle\Sync\SyncProcess\Direction\Helper\ValueHelper;
use Autoborna\IntegrationsBundle\Sync\SyncProcess\Direction\Internal\ObjectChangeGenerator;
use PHPUnit\Framework\TestCase;

class ObjectChangeGeneratorTest extends TestCase
{
    /**
     * @var SyncJudgeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $syncJudge;

    /**
     * @var ValueHelper|\PHPUnit\Framework\MockObject\MockObject
     */
    private $valueHelper;

    /**
     * @var FieldHelper|\PHPUnit\Framework\MockObject\MockObject
     */
    private $fieldHelper;

    protected function setUp(): void
    {
        $this->syncJudge   = $this->createMock(SyncJudgeInterface::class);
        $this->valueHelper = $this->createMock(ValueHelper::class);
        $this->fieldHelper = $this->createMock(FieldHelper::class);
    }

    public function testFieldsAreAddedToObjectChangeAndIntegrationFirstNameWins(): void
    {
        $this->valueHelper->method('getValueForAutoborna')
            ->willReturnCallback(
                function (NormalizedValueDAO $normalizedValueDAO, string $fieldState, string $syncDirection) {
                    return $normalizedValueDAO;
                }
            );

        $integration = 'Test';
        $objectName  = 'Contact';

        $mappingManual = $this->getMappingManual($integration, $objectName);
        $syncReport    = $this->getIntegrationSyncReport($integration, $objectName);

        $internalReportObject = new ReportObjectDAO(Contact::NAME, 1);
        $internalReportObject->addField(new ReportFieldDAO('email', new NormalizedValueDAO(NormalizedValueDAO::EMAIL_TYPE, 'test@test.com')));
        $internalReportObject->addField(new ReportFieldDAO('firstname', new NormalizedValueDAO(NormalizedValueDAO::TEXT_TYPE, 'Bob')));

        $this->syncJudge->expects($this->exactly(2))
            ->method('adjudicate')
            ->willReturnCallback(
                function ($mode, InformationChangeRequestDAO $internalInformationChangeRequest, InformationChangeRequestDAO $integrationInformationChangeRequest) {
                    return $integrationInformationChangeRequest;
                }
            );

        $objectChangeDAO       = $this->getObjectGenerator()->getSyncObjectChange(
            $syncReport,
            $mappingManual,
            $mappingManual->getObjectMapping(Contact::NAME, $objectName),
            $internalReportObject,
            $syncReport->getObject($objectName, 2)
        );

        $this->assertEquals($integration, $objectChangeDAO->getIntegration());

        // object and object ID should be Autoborna's (from the Autoborna's POV)
        $this->assertEquals(Contact::NAME, $objectChangeDAO->getObject());
        $this->assertEquals(1, $objectChangeDAO->getObjectId());

        // mapped object and ID should be the integrations
        $this->assertEquals($objectName, $objectChangeDAO->getMappedObject());
        $this->assertEquals(2, $objectChangeDAO->getMappedObjectId());

        // Email should be a required field
        $requiredFields = $objectChangeDAO->getRequiredFields();
        $this->assertTrue(isset($requiredFields['email']));

        // Both fields should be included
        $fields = $objectChangeDAO->getFields();
        $this->assertTrue(isset($fields['email']) && isset($fields['firstname']));

        // First name is presumed to be changed
        $changedFields = $objectChangeDAO->getChangedFields();
        $this->assertTrue(isset($changedFields['firstname']));

        // First name should have changed to Robert because the sync judge returned the integration's information change request
        $this->assertEquals('Robert', $changedFields['firstname']->getValue()->getNormalizedValue());
    }

    public function testFieldsAreAddedToObjectChangeAndInternalFirstNameWins(): void
    {
        $this->valueHelper->method('getValueForAutoborna')
            ->willReturnCallback(
                function (NormalizedValueDAO $normalizedValueDAO, string $fieldState, string $syncDirection) {
                    return $normalizedValueDAO;
                }
            );

        $integration = 'Test';
        $objectName  = 'Contact';

        $mappingManual = $this->getMappingManual($integration, $objectName);
        $syncReport    = $this->getIntegrationSyncReport($integration, $objectName);

        $internalReportObject = new ReportObjectDAO(Contact::NAME, 1);
        $internalReportObject->addField(new ReportFieldDAO('email', new NormalizedValueDAO(NormalizedValueDAO::EMAIL_TYPE, 'test@test.com')));
        $internalReportObject->addField(new ReportFieldDAO('firstname', new NormalizedValueDAO(NormalizedValueDAO::TEXT_TYPE, 'Bob')));

        $this->syncJudge->expects($this->exactly(2))
            ->method('adjudicate')
            ->willReturnCallback(
                function ($mode, InformationChangeRequestDAO $internalInformationChangeRequest, InformationChangeRequestDAO $integrationInformationChangeRequest) {
                    return $internalInformationChangeRequest;
                }
            );

        $objectChangeDAO       = $this->getObjectGenerator()->getSyncObjectChange(
            $syncReport,
            $mappingManual,
            $mappingManual->getObjectMapping(Contact::NAME, $objectName),
            $internalReportObject,
            $syncReport->getObject($objectName, 2)
        );

        $this->assertEquals($integration, $objectChangeDAO->getIntegration());

        // object and object ID should be Autoborna's (from the Autoborna's POV)
        $this->assertEquals(Contact::NAME, $objectChangeDAO->getObject());
        $this->assertEquals(1, $objectChangeDAO->getObjectId());

        // mapped object and ID should be the integrations
        $this->assertEquals($objectName, $objectChangeDAO->getMappedObject());
        $this->assertEquals(2, $objectChangeDAO->getMappedObjectId());

        // Email should be a required field
        $requiredFields = $objectChangeDAO->getRequiredFields();
        $this->assertTrue(isset($requiredFields['email']));

        // Both fields should be included
        $fields = $objectChangeDAO->getFields();
        $this->assertTrue(isset($fields['email']) && isset($fields['firstname']));

        // First name is presumed to be changed
        $changedFields = $objectChangeDAO->getChangedFields();
        $this->assertTrue(isset($changedFields['firstname']));

        // First name should have changed to Robert because the sync judge returned the integration's information change request
        $this->assertEquals('Bob', $changedFields['firstname']->getValue()->getNormalizedValue());
    }

    /**
     * @return MappingManualDAO
     */
    private function getMappingManual(string $integration, string $objectName)
    {
        $mappingManual = new MappingManualDAO($integration);
        $objectMapping = new ObjectMappingDAO(Contact::NAME, $objectName);
        $objectMapping->addFieldMapping('email', 'email', ObjectMappingDAO::SYNC_BIDIRECTIONALLY, true);
        $objectMapping->addFieldMapping('firstname', 'first_name');
        $mappingManual->addObjectMapping($objectMapping);

        return $mappingManual;
    }

    /**
     * @return ReportDAO
     */
    private function getIntegrationSyncReport(string $integration, string $objectName)
    {
        $syncReport   = new ReportDAO($integration);
        $reportObject = new ReportObjectDAO($objectName, 2);
        $reportObject->addField(new ReportFieldDAO('email', new NormalizedValueDAO(NormalizedValueDAO::EMAIL_TYPE, 'test@test.com'), ReportFieldDAO::FIELD_REQUIRED));
        $reportObject->addField(new ReportFieldDAO('first_name', new NormalizedValueDAO(NormalizedValueDAO::TEXT_TYPE, 'Robert')));

        $syncReport->addObject($reportObject);

        return $syncReport;
    }

    /**
     * @return ObjectChangeGenerator
     */
    private function getObjectGenerator()
    {
        return new ObjectChangeGenerator($this->syncJudge, $this->valueHelper, $this->fieldHelper);
    }
}
