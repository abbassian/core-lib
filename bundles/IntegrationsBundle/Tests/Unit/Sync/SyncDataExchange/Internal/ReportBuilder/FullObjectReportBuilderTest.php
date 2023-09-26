<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Tests\Unit\Sync\SyncDataExchange\Internal\ReportBuilder;

use Autoborna\IntegrationsBundle\Event\InternalObjectFindEvent;
use Autoborna\IntegrationsBundle\IntegrationEvents;
use Autoborna\IntegrationsBundle\Sync\DAO\Sync\InputOptionsDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Sync\Report\FieldDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Sync\Request\ObjectDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Sync\Request\RequestDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Value\NormalizedValueDAO;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Internal\Object\Company;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Internal\Object\Contact;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Internal\ObjectProvider;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Internal\ReportBuilder\FieldBuilder;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Internal\ReportBuilder\FullObjectReportBuilder;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\AutobornaSyncDataExchange;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FullObjectReportBuilderTest extends TestCase
{
    private const INTEGRATION_NAME = 'Test';

    /**
     * @var ObjectProvider|\PHPUnit\Framework\MockObject\MockObject
     */
    private $objectProvider;

    /**
     * @var EventDispatcherInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $dispatcher;

    /**
     * @var FieldBuilder|\PHPUnit\Framework\MockObject\MockObject
     */
    private $fieldBuilder;

    /**
     * @var FullObjectReportBuilder
     */
    private $reportBuilder;

    protected function setUp(): void
    {
        $this->objectProvider = $this->createMock(ObjectProvider::class);
        $this->dispatcher     = $this->createMock(EventDispatcherInterface::class);
        $this->fieldBuilder   = $this->createMock(FieldBuilder::class);
        $this->reportBuilder  = new FullObjectReportBuilder(
            $this->fieldBuilder,
            $this->objectProvider,
            $this->dispatcher
        );
    }

    public function testBuildingContactReport(): void
    {
        $requestDAO    = new RequestDAO(self::INTEGRATION_NAME, 1, new InputOptionsDAO(['integration' => self::INTEGRATION_NAME]));
        $fromDateTime  = new \DateTimeImmutable('2018-10-08 00:00:00');
        $toDateTime    = new \DateTimeImmutable('2018-10-08 00:01:00');
        $requestObject = new ObjectDAO(Contact::NAME, $fromDateTime, $toDateTime);
        $requestObject->addField('email');
        $requestDAO->addObject($requestObject);

        $this->fieldBuilder->expects($this->once())
            ->method('buildObjectField')
            ->with('email', $this->anything(), $requestObject, AutobornaSyncDataExchange::NAME)
            ->willReturn(
                new FieldDAO('email', new NormalizedValueDAO(NormalizedValueDAO::EMAIL_TYPE, 'test@test.com'))
            );

        $internalObject = new Contact();

        $this->objectProvider->expects($this->once())
            ->method('getObjectByName')
            ->with(Contact::NAME)
            ->willReturn($internalObject);

        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with(
                IntegrationEvents::INTEGRATION_FIND_INTERNAL_RECORDS,
                $this->callback(function (InternalObjectFindEvent $event) use ($internalObject, $fromDateTime, $toDateTime) {
                    $this->assertSame($internalObject, $event->getObject());
                    $this->assertSame($fromDateTime, $event->getDateRange()->getFromDate());
                    $this->assertSame($toDateTime, $event->getDateRange()->getToDate());
                    $this->assertSame(0, $event->getStart());
                    $this->assertSame(200, $event->getLimit());

                    // Mock a subscriber:
                    $event->setFoundObjects([
                        [
                            'id'            => 1,
                            'email'         => 'test@test.com',
                            'date_modified' => '2018-10-08 00:30:00',
                        ],
                    ]);

                    return true;
                })
            );

        $report  = $this->reportBuilder->buildReport($requestDAO);
        $objects = $report->getObjects(Contact::NAME);

        $this->assertTrue(isset($objects[1]));
        $this->assertEquals('test@test.com', $objects[1]->getField('email')->getValue()->getNormalizedValue());
    }

    public function testBuildingCompanyReport(): void
    {
        $requestDAO    = new RequestDAO(self::INTEGRATION_NAME, 1, new InputOptionsDAO(['integration' => self::INTEGRATION_NAME]));
        $fromDateTime  = new \DateTimeImmutable('2018-10-08 00:00:00');
        $toDateTime    = new \DateTimeImmutable('2018-10-08 00:01:00');
        $requestObject = new ObjectDAO(AutobornaSyncDataExchange::OBJECT_COMPANY, $fromDateTime, $toDateTime);
        $requestObject->addField('email');
        $requestDAO->addObject($requestObject);

        $this->fieldBuilder->expects($this->once())
            ->method('buildObjectField')
            ->with('email', $this->anything(), $requestObject, AutobornaSyncDataExchange::NAME)
            ->willReturn(
                new FieldDAO('email', new NormalizedValueDAO(NormalizedValueDAO::EMAIL_TYPE, 'test@test.com'))
            );

        $internalObject = new Company();

        $this->objectProvider->expects($this->once())
            ->method('getObjectByName')
            ->with(Company::NAME)
            ->willReturn($internalObject);

        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with(
                IntegrationEvents::INTEGRATION_FIND_INTERNAL_RECORDS,
                $this->callback(function (InternalObjectFindEvent $event) use ($internalObject, $fromDateTime, $toDateTime) {
                    $this->assertSame($internalObject, $event->getObject());
                    $this->assertSame($fromDateTime, $event->getDateRange()->getFromDate());
                    $this->assertSame($toDateTime, $event->getDateRange()->getToDate());
                    $this->assertSame(0, $event->getStart());
                    $this->assertSame(200, $event->getLimit());

                    // Mock a subscriber:
                    $event->setFoundObjects([
                        [
                            'id'            => 1,
                            'email'         => 'test@test.com',
                            'date_modified' => '2018-10-08 00:30:00',
                        ],
                    ]);

                    return true;
                })
            );

        $report  = $this->reportBuilder->buildReport($requestDAO);
        $objects = $report->getObjects(AutobornaSyncDataExchange::OBJECT_COMPANY);

        $this->assertTrue(isset($objects[1]));
        $this->assertEquals('test@test.com', $objects[1]->getField('email')->getValue()->getNormalizedValue());
    }
}
