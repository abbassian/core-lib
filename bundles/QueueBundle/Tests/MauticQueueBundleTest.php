<?php

namespace Autoborna\QueueBundle\Tests;

use Leezy\PheanstalkBundle\DependencyInjection\LeezyPheanstalkExtension;
use Autoborna\QueueBundle\AutobornaQueueBundle;
use Autoborna\QueueBundle\Queue\QueueProtocol;
use OldSound\RabbitMqBundle\DependencyInjection\Compiler\RegisterPartsPass;
use OldSound\RabbitMqBundle\DependencyInjection\OldSoundRabbitMqExtension;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AutobornaQueueBundleTest extends TestCase
{
    /**
     * @var ContainerBuilder|MockObject
     */
    private $container;

    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerBuilder::class);
    }

    public function testCompilerPassIgnoredIfQueueIsDisabled()
    {
        $this->container->expects($this->never())
            ->method('addCompilerPass');

        $this->container->expects($this->never())
            ->method('loadFromExtension');

        $bundle = new AutobornaQueueBundle('');

        $bundle->build($this->container);
    }

    public function testRabbitMqIsLoaded()
    {
        $this->container->expects($this->once())
            ->method('addCompilerPass')
            ->with($this->isInstanceOf(RegisterPartsPass::class));

        $this->container->expects($this->once())
            ->method('loadFromExtension')
            ->with('old_sound_rabbit_mq', $this->anything());

        $bundle = new AutobornaQueueBundle(QueueProtocol::RABBITMQ);

        $bundle->build($this->container);

        $extension = $bundle->getContainerExtension();
        $this->assertInstanceOf(OldSoundRabbitMqExtension::class, $extension);
    }

    public function testBeanstalkdIsLoaded()
    {
        $this->container->expects($this->never())
            ->method('addCompilerPass');

        $this->container->expects($this->once())
            ->method('loadFromExtension')
            ->with('leezy_pheanstalk', $this->anything());

        $bundle = new AutobornaQueueBundle(QueueProtocol::BEANSTALKD);

        $bundle->build($this->container);

        $extension = $bundle->getContainerExtension();
        $this->assertInstanceOf(LeezyPheanstalkExtension::class, $extension);
    }

    public function testNothingIsLoadedIfQueueIsNotRecongized()
    {
        $this->container->expects($this->never())
            ->method('addCompilerPass');

        $this->container->expects($this->never())
            ->method('loadFromExtension');

        $bundle = new AutobornaQueueBundle('foobar');

        $bundle->build($this->container);
    }
}
