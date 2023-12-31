<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Tests\Unit\Sync\Notification\Handler;

use Autoborna\IntegrationsBundle\Sync\Exception\HandlerNotSupportedException;
use Autoborna\IntegrationsBundle\Sync\Notification\Handler\HandlerContainer;
use Autoborna\IntegrationsBundle\Sync\Notification\Handler\HandlerInterface;
use PHPUnit\Framework\TestCase;

class HandlerContainerTest extends TestCase
{
    public function testExceptionThrownIfIntegrationNotFound(): void
    {
        $this->expectException(HandlerNotSupportedException::class);

        $handler = new HandlerContainer();
        $handler->getHandler('foo', 'bar');
    }

    public function testExceptionThrownIfObjectNotFound(): void
    {
        $this->expectException(HandlerNotSupportedException::class);

        $handler = new HandlerContainer();

        $mockHandler = $this->createMock(HandlerInterface::class);
        $mockHandler->method('getIntegration')
            ->willReturn('foo');
        $mockHandler->method('getSupportedObject')
            ->willReturn('bogus');

        $handler->registerHandler($mockHandler);

        $handler->getHandler('foo', 'bar');
    }

    public function testHandlerIsRegistered(): void
    {
        $handler = new HandlerContainer();

        $mockHandler = $this->createMock(HandlerInterface::class);
        $mockHandler->method('getIntegration')
            ->willReturn('foo');
        $mockHandler->method('getSupportedObject')
            ->willReturn('bar');

        $handler->registerHandler($mockHandler);

        $returnedHandler = $handler->getHandler('foo', 'bar');

        $this->assertEquals($mockHandler, $returnedHandler);
    }
}
