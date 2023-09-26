<?php

namespace Autoborna\CoreBundle\Tests\Unit\Factory;

use Autoborna\CoreBundle\Factory\ModelFactory;
use Autoborna\PointBundle\Model\TriggerModel;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ModelFactoryTest extends TestCase
{
    /**
     * @var MockObject|ContainerInterface
     */
    private $container;

    /**
     * @var ModelFactory
     */
    private $factory;

    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->factory   = new ModelFactory($this->container);
    }

    public function testModelKeyIsLowerCaseToMatchServiceKeys()
    {
        $pointTriggerModel = $this->createMock(TriggerModel::class);
        $modelName         = 'point.triggerEvent';
        $containerKey      = 'autoborna.point.model.triggerEvent';

        $this->container->expects($this->once())
            ->method('has')
            ->with($containerKey)
            ->willReturn(true);

        $this->container->expects($this->once())
            ->method('get')
            ->with($containerKey)
            ->willReturn($pointTriggerModel);

        $givenPointTriggerModel = $this->factory->getModel($modelName);

        $this->assertInstanceOf(TriggerModel::class, $givenPointTriggerModel);
    }
}
