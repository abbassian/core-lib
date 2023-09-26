<?php

declare(strict_types=1);

namespace Autoborna\LeadBundle\Tests\EventListener;

use Autoborna\ConfigBundle\ConfigEvents;
use Autoborna\ConfigBundle\Event\ConfigBuilderEvent;
use Autoborna\LeadBundle\EventListener\ConfigSubscriber;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ConfigSubscriberTest extends TestCase
{
    private ConfigSubscriber $configSubscriber;

    /**
     * @var ConfigBuilderEvent&MockObject
     */
    private $configBuilderEvent;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configSubscriber   = new ConfigSubscriber();
        $this->configBuilderEvent = $this->createMock(ConfigBuilderEvent::class);
    }

    public function testSubscribedEvents(): void
    {
        $subscribedEvents = ConfigSubscriber::getSubscribedEvents();
        $this->assertArrayHasKey(ConfigEvents::CONFIG_ON_GENERATE, $subscribedEvents);
    }

    public function testThatWeAreAddingFormsToTheConfig(): void
    {
        $leadConfig = [
            'bundle'     => 'LeadBundle',
            'formAlias'  => 'leadconfig',
            'formType'   => 'Autoborna\\LeadBundle\\Form\\Type\\ConfigType',
            'formTheme'  => 'AutobornaLeadBundle:FormTheme\\Config',
            'parameters' => null,
        ];

        $segmentConfig = [
            'bundle'     => 'LeadBundle',
            'formAlias'  => 'segment_config',
            'formType'   => 'Autoborna\\LeadBundle\\Form\\Type\\SegmentConfigType',
            'formTheme'  => 'AutobornaLeadBundle:FormTheme\\Config',
            'parameters' => null,
        ];

        $this->configBuilderEvent
            ->expects($this->exactly(2))
            ->method('addForm')
            ->withConsecutive([$leadConfig], [$segmentConfig]);

        $this->configSubscriber->onConfigGenerate($this->configBuilderEvent);
    }
}
