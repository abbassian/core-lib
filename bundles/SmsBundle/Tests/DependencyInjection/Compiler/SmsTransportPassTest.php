<?php

namespace Autoborna\SmsBundle\Tests\DependencyInjection\Compiler;

use Autoborna\PluginBundle\Helper\IntegrationHelper;
use Autoborna\SmsBundle\DependencyInjection\Compiler\SmsTransportPass;
use Autoborna\SmsBundle\Sms\TransportChain;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SmsTransportPassTest extends TestCase
{
    public function testProcess()
    {
        $container = new ContainerBuilder();
        $container->addCompilerPass(new SmsTransportPass());
        $container
            ->register('foo')
            ->setPublic(true)
            ->setAbstract(true)
            ->addTag('autoborna.sms_transport', ['alias'=>'fakeAliasDefault', 'integrationAlias' => 'fakeIntegrationDefault']);

        $container
            ->register('chocolate')
            ->setPublic(true)
            ->setAbstract(true);

        $container
            ->register('bar')
            ->setPublic(true)
            ->setAbstract(true)
            ->addTag('autoborna.sms_transport');

        $transport = $this->getMockBuilder(TransportChain::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['addTransport'])
            ->getMock();

        $container
            ->register('autoborna.sms.transport_chain')
            ->setClass(get_class($transport))
            ->setArguments(['foo', $this->createMock(IntegrationHelper::class)])
            ->setShared(false)
            ->setSynthetic(true)
            ->setAbstract(true);

        $pass = new SmsTransportPass();
        $pass->process($container);

        $this->assertEquals(2, count($container->findTaggedServiceIds('autoborna.sms_transport')));

        $methodCalls = $container->getDefinition('autoborna.sms.transport_chain')->getMethodCalls();
        $this->assertCount(count($methodCalls), $container->findTaggedServiceIds('autoborna.sms_transport'));

        // Translation string
        $this->assertEquals('fakeAliasDefault', $methodCalls[0][1][2]);
        // Integration name/alias
        $this->assertEquals('fakeIntegrationDefault', $methodCalls[0][1][3]);

        // Translation string is set as service ID by default
        $this->assertEquals('bar', $methodCalls[1][1][2]);
        // Integration name/alias is set to service ID by default
        $this->assertEquals('bar', $methodCalls[1][1][3]);
    }
}
