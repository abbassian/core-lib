<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Tests\Unit\Sync\SyncDataExchange;

use Autoborna\IntegrationsBundle\Entity\FieldChangeRepository;
use Autoborna\IntegrationsBundle\Sync\DAO\Mapping\MappingManualDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Sync\InputOptionsDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Sync\Report\FieldDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Sync\Report\ObjectDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Sync\Request\RequestDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Value\NormalizedValueDAO;
use Autoborna\IntegrationsBundle\Sync\Helper\MappingHelper;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Helper\FieldHelper;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Internal\Executioner\OrderExecutioner;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Internal\ReportBuilder\FullObjectReportBuilder;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Internal\ReportBuilder\PartialObjectReportBuilder;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\AutobornaSyncDataExchange;
use Autoborna\LeadBundle\Entity\Lead;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AutobornaSyncDataExchangeTest extends TestCase
{
    /**
     * @var MockObject|FieldChangeRepository
     */
    private $fieldChangeRepository;

    /**
     * @var MockObject|FieldHelper
     */
    private $fieldHelper;

    /**
     * @var MockObject|MappingHelper
     */
    private $mappingHelper;

    /**
     * @var MockObject|FullObjectReportBuilder
     */
    private $fullObjectReportBuilder;

    /**
     * @var MockObject|PartialObjectReportBuilder
     */
    private $partialObjectReportBuilder;

    /**
     * @var MockObject|OrderExecutioner
     */
    private $orderExecutioner;

    /**
     * @var AutobornaSyncDataExchange
     */
    private $autobornaSyncDataExchange;

    protected function setUp(): void
    {
        $this->fieldChangeRepository      = $this->createMock(FieldChangeRepository::class);
        $this->fieldHelper                = $this->createMock(FieldHelper::class);
        $this->mappingHelper              = $this->createMock(MappingHelper::class);
        $this->fullObjectReportBuilder    = $this->createMock(FullObjectReportBuilder::class);
        $this->partialObjectReportBuilder = $this->createMock(PartialObjectReportBuilder::class);
        $this->orderExecutioner           = $this->createMock(OrderExecutioner::class);

        $this->autobornaSyncDataExchange = new AutobornaSyncDataExchange(
            $this->fieldChangeRepository,
            $this->fieldHelper,
            $this->mappingHelper,
            $this->fullObjectReportBuilder,
            $this->partialObjectReportBuilder,
            $this->orderExecutioner
        );
    }

    public function testFirstTimeSyncUsesFullObjectBuilder(): void
    {
        $inputOptionsDAO = new InputOptionsDAO(
            [
                'integration'     => 'foobar',
                'first-time-sync' => true,
            ]
        );

        $requestDAO = new RequestDAO('foobar', 1, $inputOptionsDAO);

        $this->fullObjectReportBuilder->expects($this->once())
            ->method('buildReport')
            ->with($requestDAO);

        $this->partialObjectReportBuilder->expects($this->never())
            ->method('buildReport')
            ->with($requestDAO);

        $this->autobornaSyncDataExchange->getSyncReport($requestDAO);
    }

    public function testSyncingSpecificAutobornaIdsUseFullObjectBuilder(): void
    {
        $inputOptionsDAO = new InputOptionsDAO(
            [
                'integration'      => 'foobar',
                'autoborna-object-id' => [1, 2, 3],
            ]
        );

        $requestDAO = new RequestDAO('foobar', 1, $inputOptionsDAO);

        $this->fullObjectReportBuilder->expects($this->once())
            ->method('buildReport')
            ->with($requestDAO);

        $this->partialObjectReportBuilder->expects($this->never())
            ->method('buildReport')
            ->with($requestDAO);

        $this->autobornaSyncDataExchange->getSyncReport($requestDAO);
    }

    public function testUseOfPartialObjectBuilder(): void
    {
        $inputOptionsDAO = new InputOptionsDAO(
            [
                'integration' => 'foobar',
            ]
        );

        $requestDAO = new RequestDAO('foobar', 1, $inputOptionsDAO);

        $this->fullObjectReportBuilder->expects($this->never())
            ->method('buildReport')
            ->with($requestDAO);

        $this->partialObjectReportBuilder->expects($this->once())
            ->method('buildReport')
            ->with($requestDAO);

        $this->autobornaSyncDataExchange->getSyncReport($requestDAO);
    }

    public function testGetConflictedInternalObjectWithNoObjectId(): void
    {
        $mappingManualDao     = new MappingManualDAO('IntegrationA');
        $integrationObjectDao = new ObjectDAO('Lead', 'some-SF-ID');

        $this->mappingHelper->expects($this->once())
            ->method('findAutobornaObject')
            ->with($mappingManualDao, 'lead', $integrationObjectDao)
            ->willReturn(new ObjectDAO('lead', null));

        // No need to make the DB query when ID is null.
        $this->fieldChangeRepository->expects($this->never())
            ->method('findChangesForObject');

        $internalObjectDao = $this->autobornaSyncDataExchange->getConflictedInternalObject($mappingManualDao, 'lead', $integrationObjectDao);

        Assert::assertSame('lead', $internalObjectDao->getObject());
        Assert::assertNull($internalObjectDao->getObjectId());
    }

    public function testGetConflictedInternalObjectWithObjectId(): void
    {
        $mappingManualDao     = new MappingManualDAO('IntegrationA');
        $integrationObjectDao = new ObjectDAO('Lead', 'some-SF-ID');
        $fieldChange          = [
            'modified_at'  => '2020-08-25 17:20:00',
            'column_type'  => 'text',
            'column_value' => 'some-field-value',
            'column_name'  => 'some-field-name',
        ];

        $this->mappingHelper->expects($this->once())
            ->method('findAutobornaObject')
            ->with($mappingManualDao, 'lead', $integrationObjectDao)
            ->willReturn(new ObjectDAO('lead', 123));

        $this->mappingHelper->method('getAutobornaEntityClassName')
            ->with('lead')
            ->willReturn(Lead::class);

        $this->fieldHelper->method('getFieldChangeObject')
            ->with($fieldChange)
            ->willReturn(new FieldDAO('some-field-name', new NormalizedValueDAO('type', 'some-field-value')));

        $this->fieldChangeRepository->expects($this->once())
            ->method('findChangesForObject')
            ->with('IntegrationA', Lead::class, 123)
            ->willReturn([$fieldChange]);

        $internalObjectDao = $this->autobornaSyncDataExchange->getConflictedInternalObject($mappingManualDao, 'lead', $integrationObjectDao);

        Assert::assertSame('lead', $internalObjectDao->getObject());
        Assert::assertSame(123, $internalObjectDao->getObjectId());
        Assert::assertCount(1, $internalObjectDao->getFields());
    }
}
