<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Tests\Unit\Event;

use Autoborna\IntegrationsBundle\Event\ConfigSaveEvent;
use Autoborna\PluginBundle\Entity\Integration;
use PHPUnit\Framework\TestCase;

class ConfigSaveEventTest extends TestCase
{
    public function testGetters()
    {
        $name        = 'name';
        $integration = $this->createMock(Integration::class);
        $event       = new ConfigSaveEvent($integration);

        $integration->expects(self::once())
            ->method('getName')
            ->willReturn($name);

        self::assertSame($integration, $event->getIntegrationConfiguration());
        self::assertSame($name, $event->getIntegration());
    }
}
