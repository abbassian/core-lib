<?php

namespace Autoborna\UserBundle\Tests\Security\SAML\Store;

use Doctrine\Persistence\ObjectManager;
use LightSaml\Provider\TimeProvider\TimeProviderInterface;
use Autoborna\UserBundle\Entity\IdEntry;
use Autoborna\UserBundle\Security\SAML\Store\IdStore;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class IdStoreTest extends TestCase
{
    /**
     * @var ObjectManager|MockObject
     */
    private $manager;

    /**
     * @var TimeProviderInterface|MockObject
     */
    private $timeProvider;

    /**
     * @var IdStore
     */
    private $store;

    protected function setUp(): void
    {
        $this->manager      = $this->createMock(ObjectManager::class);
        $this->timeProvider = $this->createMock(TimeProviderInterface::class);
        $this->store        = new IdStore($this->manager, $this->timeProvider);
    }

    public function testNewIdEntryCreatedIfEntityIdNotFound()
    {
        $expiry = new \DateTime('+5 minutes');
        $this->manager->expects($this->once())
            ->method('persist')
            ->willReturnCallback(function (IdEntry $idEntry) use ($expiry) {
                $this->assertEquals('foobar', $idEntry->getEntityId());
                $this->assertEquals('abc', $idEntry->getId());
                $this->assertEquals($expiry->getTimestamp(), $idEntry->getExpiryTime()->getTimestamp());
            });

        $this->store->set('foobar', 'abc', $expiry);
    }

    public function testIdEntryUpdatedIfEntityIdFound()
    {
        $expiry  = new \DateTime('+5 minutes');
        $idEntry = new IdEntry();
        $idEntry->setEntityId('foobar');
        $idEntry->setId('abc');
        $idEntry->setExpiryTime($expiry);

        $this->manager->expects($this->once())
            ->method('find')
            ->willReturn($idEntry);

        $this->manager->expects($this->once())
            ->method('persist')
            ->with($idEntry);

        $this->store->set('foobar', 'abc', $expiry);
    }

    public function testIdEntryIsFoundAndNotExpired()
    {
        $expiry  = new \DateTime('+5 minutes');
        $idEntry = new IdEntry();
        $idEntry->setEntityId('foobar');
        $idEntry->setId('abc');
        $idEntry->setExpiryTime($expiry);

        $this->manager->expects($this->once())
            ->method('find')
            ->willReturn($idEntry);

        $this->assertTrue($this->store->has('foobar', 'abc'));
    }

    public function testIdEntryIsFoundButIsExpired()
    {
        $this->timeProvider->expects($this->once())
            ->method('getTimestamp')
            ->willReturn(time());

        $expiry  = new \DateTime('-5 minutes');
        $idEntry = new IdEntry();
        $idEntry->setEntityId('foobar');
        $idEntry->setId('abc');
        $idEntry->setExpiryTime($expiry);

        $this->manager->expects($this->once())
            ->method('find')
            ->willReturn($idEntry);

        $this->assertFalse($this->store->has('foobar', 'abc'));
    }

    public function testIdEntryIsNotFound()
    {
        $this->timeProvider->expects($this->never())
            ->method('getTimestamp');

        $this->manager->expects($this->once())
            ->method('find')
            ->willReturn(null);

        $this->assertFalse($this->store->has('foobar', 'abc'));
    }
}
