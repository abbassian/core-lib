<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Tests\Unit\Sync\DAO;

use DateTimeImmutable;
use DateTimeZone;
use Autoborna\IntegrationsBundle\Exception\InvalidValueException;
use Autoborna\IntegrationsBundle\Sync\DAO\Sync\InputOptionsDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Sync\ObjectIdsDAO;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Internal\Object\Contact;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\AutobornaSyncDataExchange;
use PHPUnit\Framework\TestCase;

class InputOptionsDAOTest extends TestCase
{
    public function testWorkflowFromCliWithAllValuesSet(): void
    {
        $inputOptionsDAO = new InputOptionsDAO(
            [
                'integration'           => 'Magento',
                'first-time-sync'       => true,
                'disable-push'          => false,
                'disable-pull'          => true,
                'disable-activity-push' => true,
                'autoborna-object-id'      => ['contact:12', 'contact:13', 'company:45'],
                'integration-object-id' => ['Lead:hfskjdhf', 'Lead:hfskjdhr'],
                'start-datetime'        => '2019-09-12T12:01:20',
                'end-datetime'          => '2019-10-12T12:01:20',
                'option'                => ['custom1:1', 'custom2:2'],
            ]
        );

        $this->assertSame('Magento', $inputOptionsDAO->getIntegration());
        $this->assertTrue($inputOptionsDAO->isFirstTimeSync());
        $this->assertFalse($inputOptionsDAO->pullIsEnabled());
        $this->assertTrue($inputOptionsDAO->pushIsEnabled());
        $this->assertFalse($inputOptionsDAO->activityPushIsEnabled());
        $this->assertSame(['12', '13'], $inputOptionsDAO->getAutobornaObjectIds()->getObjectIdsFor(Contact::NAME));
        $this->assertSame(['45'], $inputOptionsDAO->getAutobornaObjectIds()->getObjectIdsFor(AutobornaSyncDataExchange::OBJECT_COMPANY));
        $this->assertSame(['hfskjdhf', 'hfskjdhr'], $inputOptionsDAO->getIntegrationObjectIds()->getObjectIdsFor('Lead'));
        $this->assertSame('2019-09-12T12:01:20+00:00', $inputOptionsDAO->getStartDateTime()->format(DATE_ATOM));
        $this->assertSame('2019-10-12T12:01:20+00:00', $inputOptionsDAO->getEndDateTime()->format(DATE_ATOM));
        $this->assertSame(['custom1' => '1', 'custom2' => '2'], $inputOptionsDAO->getOptions());
    }

    public function testWorkflowFromCliWithNoValuesSet(): void
    {
        $this->expectException(InvalidValueException::class);
        new InputOptionsDAO([]);
    }

    public function testWorkflowFromCliWithOnlyIntegrationValuesSet(): void
    {
        $inputOptionsDAO = new InputOptionsDAO(['integration' => 'Magento']);
        $this->assertSame('Magento', $inputOptionsDAO->getIntegration());
        $this->assertFalse($inputOptionsDAO->isFirstTimeSync());
        $this->assertTrue($inputOptionsDAO->pullIsEnabled());
        $this->assertTrue($inputOptionsDAO->pushIsEnabled());
        $this->assertTrue($inputOptionsDAO->activityPushIsEnabled());
        $this->assertNull($inputOptionsDAO->getAutobornaObjectIds());
        $this->assertNull($inputOptionsDAO->getIntegrationObjectIds());
        $this->assertNull($inputOptionsDAO->getStartDateTime());
        $this->assertNull($inputOptionsDAO->getEndDateTime());
        $this->assertEmpty($inputOptionsDAO->getOptions());
    }

    public function testWorkflowFromServiceWithAllValuesSet(): void
    {
        $autobornaObjectIds      = new ObjectIdsDAO();
        $integrationObjectIds = new ObjectIdsDAO();
        $start                = new DateTimeImmutable('2019-09-12T12:01:20', new DateTimeZone('UTC'));
        $end                  = new DateTimeImmutable('2019-10-12T12:01:20', new DateTimeZone('UTC'));
        $options              = ['custom1' => 1, 'custom2' => 2];
        $inputOptionsDAO      = new InputOptionsDAO(
            [
                'integration'           => 'Magento',
                'first-time-sync'       => true,
                'disable-push'          => false,
                'disable-pull'          => true,
                'disable-activity-push' => false,
                'autoborna-object-id'      => $autobornaObjectIds,
                'integration-object-id' => $integrationObjectIds,
                'start-datetime'        => $start,
                'end-datetime'          => $end,
                'options'               => $options,
            ]
        );

        $this->assertSame('Magento', $inputOptionsDAO->getIntegration());
        $this->assertTrue($inputOptionsDAO->isFirstTimeSync());
        $this->assertFalse($inputOptionsDAO->pullIsEnabled());
        $this->assertTrue($inputOptionsDAO->pushIsEnabled());
        $this->assertTrue($inputOptionsDAO->activityPushIsEnabled());
        $this->assertSame($autobornaObjectIds, $inputOptionsDAO->getAutobornaObjectIds());
        $this->assertSame($integrationObjectIds, $inputOptionsDAO->getIntegrationObjectIds());
        $this->assertSame($start, $inputOptionsDAO->getStartDateTime());
        $this->assertSame($end, $inputOptionsDAO->getEndDateTime());
        $this->assertSame($options, $inputOptionsDAO->getOptions());
    }
}
