<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Tests\Unit\Sync\Notification\Helper;

use Autoborna\IntegrationsBundle\Event\InternalObjectOwnerEvent;
use Autoborna\IntegrationsBundle\IntegrationEvents;
use Autoborna\IntegrationsBundle\Sync\Exception\ObjectNotFoundException;
use Autoborna\IntegrationsBundle\Sync\Exception\ObjectNotSupportedException;
use Autoborna\IntegrationsBundle\Sync\Notification\Helper\OwnerProvider;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Internal\Object\Contact;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Internal\ObjectProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class OwnerProviderTest extends TestCase
{
    /**
     * @var ObjectProvider|\PHPUnit\Framework\MockObject\MockObject
     */
    private $objectProvider;

    /**
     * @var EventDispatcherInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $dispatcher;

    /**
     * @var OwnerProvider
     */
    private $ownerProvider;

    protected function setUp(): void
    {
        $this->objectProvider = $this->createMock(ObjectProvider::class);
        $this->dispatcher     = $this->createMock(EventDispatcherInterface::class);
        $this->ownerProvider  = new OwnerProvider($this->dispatcher, $this->objectProvider);
    }

    public function testGetOwnersForObjectIdsWithoutIds(): void
    {
        $this->objectProvider->expects($this->never())
            ->method('getObjectByName');

        $this->assertSame([], $this->ownerProvider->getOwnersForObjectIds(Contact::NAME, []));
    }

    public function testGetOwnersForObjectIdsWithUnknownObject(): void
    {
        $this->objectProvider->expects($this->once())
            ->method('getObjectByName')
            ->with('Unicorn')
            ->willThrowException(new ObjectNotFoundException('Unicorn'));

        $this->expectException(ObjectNotSupportedException::class);

        $this->ownerProvider->getOwnersForObjectIds('Unicorn', [123]);
    }

    public function testGetOwnersForObjectIdsWithKnownObject(): void
    {
        $internalObject = new Contact();
        $this->objectProvider->expects($this->once())
            ->method('getObjectByName')
            ->with(Contact::NAME)
            ->willReturn($internalObject);

        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with(
                IntegrationEvents::INTEGRATION_FIND_OWNER_IDS,
                $this->callback(function (InternalObjectOwnerEvent $event) use ($internalObject) {
                    $this->assertSame($internalObject, $event->getObject());
                    $this->assertSame([123], $event->getObjectIds());

                    // Simulate a subscriber. Format: [object_id => owner_id].
                    $event->setOwners([$event->getObjectIds()[0] => 456]);

                    return true;
                })
            );

        $this->assertSame([123 => 456], $this->ownerProvider->getOwnersForObjectIds(Contact::NAME, [123]));
    }
}
