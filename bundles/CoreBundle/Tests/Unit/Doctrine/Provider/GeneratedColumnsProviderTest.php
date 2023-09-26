<?php

declare(strict_types=1);

namespace Autoborna\CoreBundle\Tests\Unit\Doctrine\Provider;

use Autoborna\CoreBundle\CoreEvents;
use Autoborna\CoreBundle\Doctrine\GeneratedColumn\GeneratedColumn;
use Autoborna\CoreBundle\Doctrine\GeneratedColumn\GeneratedColumnsInterface;
use Autoborna\CoreBundle\Doctrine\Provider\GeneratedColumnsProvider;
use Autoborna\CoreBundle\Doctrine\Provider\VersionProviderInterface;
use Autoborna\CoreBundle\Event\GeneratedColumnsEvent;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class GeneratedColumnsProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var MockObject|VersionProviderInterface
     */
    private $versionProvider;

    /**
     * @var MockObject|EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var GeneratedColumnsProvider
     */
    private $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->versionProvider = $this->createMock(VersionProviderInterface::class);
        $this->dispatcher      = $this->createMock(EventDispatcherInterface::class);
        $this->provider        = new GeneratedColumnsProvider($this->versionProvider, $this->dispatcher);

        $this->dispatcher->method('hasListeners')->willReturn(true);
    }

    public function testGetGeneratedColumnsIfNotSupported(): void
    {
        $notSupportedMySqlVersion = '5.7.13';

        $this->versionProvider->expects($this->once())
            ->method('getVersion')
            ->willReturn($notSupportedMySqlVersion);

        $this->dispatcher->expects($this->never())
            ->method('dispatch');

        $generatedColumns = $this->provider->getGeneratedColumns();

        $this->assertInstanceOf(GeneratedColumnsInterface::class, $generatedColumns);
        $this->assertCount(0, $generatedColumns);
    }

    public function testGetGeneratedColumnsIfSupported(): void
    {
        $supportedMySqlVersion = '5.7.14';

        $this->versionProvider->method('getVersion')
            ->willReturn($supportedMySqlVersion);

        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with(
                CoreEvents::ON_GENERATED_COLUMNS_BUILD,
                $this->callback(
                    // Emulate a subscriber.
                    function (GeneratedColumnsEvent $event) {
                        $event->addGeneratedColumn(new GeneratedColumn('page_hits', 'generated_hit_date', 'DATE', 'not important'));

                        return true;
                    }
                )
            );

        $generatedColumns = $this->provider->getGeneratedColumns();
        $this->assertInstanceOf(GeneratedColumnsInterface::class, $generatedColumns);

        /** @var GeneratedColumn $generatedColumn */
        $generatedColumn = $generatedColumns->current();
        $this->assertSame(MAUTIC_TABLE_PREFIX.'page_hits', $generatedColumn->getTableName());

        // Ensure that the cache works and dispatcher is called only once
        $generatedColumns = $this->provider->getGeneratedColumns();

        $this->assertCount(1, $generatedColumns);
    }
}
