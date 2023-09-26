<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Tests\Unit\Sync\SyncDataExchange\Internal;

use Autoborna\IntegrationsBundle\Event\InternalObjectEvent;
use Autoborna\IntegrationsBundle\IntegrationEvents;
use Autoborna\IntegrationsBundle\Sync\Exception\ObjectNotFoundException;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Internal\Object\Contact;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Internal\ObjectProvider;
use Autoborna\LeadBundle\Entity\Lead;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ObjectProviderTest extends TestCase
{
    /**
     * @var EventDispatcherInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $dispatcher;

    /**
     * @var ObjectProvider
     */
    private $objectProvider;

    protected function setUp(): void
    {
        $this->dispatcher     = $this->createMock(EventDispatcherInterface::class);
        $this->objectProvider = new ObjectProvider($this->dispatcher);
    }

    public function testGetObjectByNameIfItDoesNotExist(): void
    {
        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with(
                IntegrationEvents::INTEGRATION_COLLECT_INTERNAL_OBJECTS,
                $this->isInstanceOf(InternalObjectEvent::class)
            );

        $this->expectException(ObjectNotFoundException::class);
        $this->objectProvider->getObjectByName('Unicorn');
    }

    public function testGetObjectByNameIfItExists(): void
    {
        $contact = new Contact();
        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with(
                IntegrationEvents::INTEGRATION_COLLECT_INTERNAL_OBJECTS,
                $this->callback(function (InternalObjectEvent $e) use ($contact) {
                    // Fake a subscriber.
                    $e->addObject($contact);

                    return true;
                })
            );

        $this->assertSame($contact, $this->objectProvider->getObjectByName(Contact::NAME));
    }

    public function testGetObjectByEntityNameIfItDoesNotExist(): void
    {
        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with(
                IntegrationEvents::INTEGRATION_COLLECT_INTERNAL_OBJECTS,
                $this->isInstanceOf(InternalObjectEvent::class)
            );

        $this->expectException(ObjectNotFoundException::class);
        $this->objectProvider->getObjectByEntityName('Unicorn');
    }

    public function testGetObjectByEntityNameIfItExists(): void
    {
        $contact = new Contact();
        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with(
                IntegrationEvents::INTEGRATION_COLLECT_INTERNAL_OBJECTS,
                $this->callback(function (InternalObjectEvent $e) use ($contact) {
                    // Fake a subscriber.
                    $e->addObject($contact);

                    return true;
                })
            );

        $this->assertSame($contact, $this->objectProvider->getObjectByEntityName(Lead::class));
    }
}
