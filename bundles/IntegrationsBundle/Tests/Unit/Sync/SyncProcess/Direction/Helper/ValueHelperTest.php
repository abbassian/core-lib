<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Tests\Unit\Sync\SyncProcess\Direction\Helper;

use Autoborna\IntegrationsBundle\Exception\InvalidValueException;
use Autoborna\IntegrationsBundle\Sync\DAO\Mapping\ObjectMappingDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Sync\Report\FieldDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Value\NormalizedValueDAO;
use Autoborna\IntegrationsBundle\Sync\SyncProcess\Direction\Helper\ValueHelper;
use PHPUnit\Framework\TestCase;

class ValueHelperTest extends TestCase
{
    public function testExceptionForMissingRequiredIntegrationValue(): void
    {
        $this->expectException(InvalidValueException::class);

        $normalizedValueDAO = new NormalizedValueDAO(NormalizedValueDAO::STRING_TYPE, '');

        $this->getValueHelper()->getValueForIntegration(
            $normalizedValueDAO,
            FieldDAO::FIELD_REQUIRED,
            ObjectMappingDAO::SYNC_TO_INTEGRATION
        );
    }

    public function testNoExceptionForMissingNonRequiredIntegrationValue(): void
    {
        $normalizedValueDAO = new NormalizedValueDAO(NormalizedValueDAO::STRING_TYPE, '');

        $newValue = $this->getValueHelper()->getValueForIntegration(
            $normalizedValueDAO,
            FieldDAO::FIELD_CHANGED,
            ObjectMappingDAO::SYNC_TO_MAUTIC
        );

        $this->assertEquals(
            '',
            $newValue->getNormalizedValue()
        );
    }

    public function testNoExceptionForMissingOppositeSyncIntegrationValue(): void
    {
        $normalizedValueDAO = new NormalizedValueDAO(NormalizedValueDAO::STRING_TYPE, '');

        $newValue = $this->getValueHelper()->getValueForIntegration(
            $normalizedValueDAO,
            FieldDAO::FIELD_CHANGED,
            ObjectMappingDAO::SYNC_TO_INTEGRATION
        );

        $this->assertEquals(
            '',
            $newValue->getNormalizedValue()
        );
    }

    public function testExceptionForMissingRequiredAutobornaValue(): void
    {
        $this->expectException(InvalidValueException::class);

        $normalizedValueDAO = new NormalizedValueDAO(NormalizedValueDAO::STRING_TYPE, '');

        $this->getValueHelper()->getValueForAutoborna(
            $normalizedValueDAO,
            FieldDAO::FIELD_REQUIRED,
            ObjectMappingDAO::SYNC_TO_MAUTIC
        );
    }

    public function testNoExceptionForMissingNonRequiredInternalValue(): void
    {
        $normalizedValueDAO = new NormalizedValueDAO(NormalizedValueDAO::STRING_TYPE, '');

        $newValue = $this->getValueHelper()->getValueForAutoborna(
            $normalizedValueDAO,
            FieldDAO::FIELD_CHANGED,
            ObjectMappingDAO::SYNC_TO_INTEGRATION
        );

        $this->assertEquals(
            '',
            $newValue->getNormalizedValue()
        );
    }

    public function testNoExceptionForMissingOppositeSyncInternalnValue(): void
    {
        $normalizedValueDAO = new NormalizedValueDAO(NormalizedValueDAO::STRING_TYPE, '');

        $newValue = $this->getValueHelper()->getValueForAutoborna(
            $normalizedValueDAO,
            FieldDAO::FIELD_CHANGED,
            ObjectMappingDAO::SYNC_TO_MAUTIC
        );

        $this->assertEquals(
            '',
            $newValue->getNormalizedValue()
        );
    }

    /**
     * @return ValueHelper
     */
    private function getValueHelper()
    {
        return new ValueHelper();
    }
}
