<?php

namespace Autoborna\SmsBundle\Tests\Sms;

use Exception;
use Autoborna\CoreBundle\Test\AutobornaMysqlTestCase;
use Autoborna\LeadBundle\Entity\Lead;
use Autoborna\SmsBundle\Integration\Twilio\TwilioTransport;
use Autoborna\SmsBundle\Sms\TransportChain;
use Autoborna\SmsBundle\Sms\TransportInterface;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionClass;

class TransportChainTest extends AutobornaMysqlTestCase
{
    /**
     * @var TransportChain|MockObject
     */
    private $transportChain;

    /**
     * @var TransportInterface|MockObject
     */
    private $twilioTransport;

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on
     * @param string $methodName Method name to call
     * @param array  $parameters array of parameters to pass into method
     *
     * @throws \ReflectionException
     *
     * @return mixed method return
     */
    public function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new ReflectionClass(get_class($object));
        $method     = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->transportChain = new TransportChain(
            'autoborna.test.twilio.mock',
            self::$container->get('autoborna.helper.integration')
        );

        $this->twilioTransport = $this->createMock(TwilioTransport::class);

        $this->twilioTransport
            ->method('sendSMS')
            ->will($this->returnValue('lol'));
    }

    public function testAddTransport()
    {
        $count = count($this->transportChain->getTransports());

        $this->transportChain->addTransport('autoborna.transport.test', self::$container->get('autoborna.sms.twilio.transport'), 'autoborna.transport.test', 'Twilio');

        $this->assertCount($count + 1, $this->transportChain->getTransports());
    }

    public function testSendSms()
    {
        $this->testAddTransport();

        $this->transportChain->addTransport('autoborna.test.twilio.mock', $this->twilioTransport, 'autoborna.test.twilio.mock', 'Twilio');

        $lead = new Lead();
        $lead->setMobile('+123456789');

        try {
            $this->transportChain->sendSms($lead, 'Yeah');
        } catch (Exception $e) {
            $message = $e->getMessage();
            $this->assertEquals('Primary SMS transport is not enabled', $message);
        }
    }
}
