<?php

namespace Autoborna\EmailBundle\Tests\MonitoredEmail\Processor;

use Autoborna\CoreBundle\Translation\Translator;
use Autoborna\EmailBundle\Entity\Email;
use Autoborna\EmailBundle\Entity\Stat;
use Autoborna\EmailBundle\Entity\StatRepository;
use Autoborna\EmailBundle\MonitoredEmail\Message;
use Autoborna\EmailBundle\MonitoredEmail\Processor\Bounce;
use Autoborna\EmailBundle\MonitoredEmail\Search\ContactFinder;
use Autoborna\EmailBundle\MonitoredEmail\Search\Result;
use Autoborna\EmailBundle\Tests\MonitoredEmail\Transport\TestTransport;
use Autoborna\LeadBundle\Entity\Lead;
use Autoborna\LeadBundle\Model\DoNotContact;
use Autoborna\LeadBundle\Model\LeadModel;
use Monolog\Logger;

class BounceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @testdox Test that the transport interface processes the message appropriately
     *
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Processor\Bounce::process()
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Processor\Bounce::updateStat()
     * @covers  \Autoborna\EmailBundle\Swiftmailer\Transport\BounceProcessorInterface::processBounce()
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Search\Result::setStat()
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Search\Result::getStat()
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Search\Result::setContacts()
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Search\Result::getContacts()
     */
    public function testProcessorInterfaceProcessesMessage()
    {
        $transport     = new TestTransport(new \Swift_Events_SimpleEventDispatcher());
        $contactFinder = $this->getMockBuilder(ContactFinder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $contactFinder->method('find')
            ->willReturnCallback(
                function ($email, $bounceAddress) {
                    $stat = new Stat();

                    $lead = new Lead();
                    $lead->setEmail($email);
                    $stat->setLead($lead);

                    $email = new Email();
                    $stat->setEmail($email);

                    $result = new Result();
                    $result->setStat($stat);
                    $result->setContacts(
                        [
                            $lead,
                        ]
                    );

                    return $result;
                }
            );

        $statRepo = $this->getMockBuilder(StatRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $statRepo->expects($this->once())
            ->method('saveEntity');

        $leadModel = $this->getMockBuilder(LeadModel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $translator = $this->getMockBuilder(Translator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $logger = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $doNotContact = $this->createMock(DoNotContact::class);

        $bouncer = new Bounce($transport, $contactFinder, $statRepo, $leadModel, $translator, $logger, $doNotContact);

        $message = new Message();
        $this->assertTrue($bouncer->process($message));
    }

    /**
     * @testdox Test that the message is processed appropriately
     *
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Processor\Bounce::process()
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Processor\Bounce::updateStat()
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Search\Result::setStat()
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Search\Result::getStat()
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Search\Result::setContacts()
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Search\Result::getContacts()
     */
    public function testContactIsFoundFromMessageAndDncRecordAdded()
    {
        $transport     = new \Swift_Transport_NullTransport(new \Swift_Events_SimpleEventDispatcher());
        $contactFinder = $this->getMockBuilder(ContactFinder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $contactFinder->method('find')
            ->willReturnCallback(
                function ($email, $bounceAddress) {
                    $stat = new Stat();

                    $lead = new Lead();
                    $lead->setEmail($email);
                    $stat->setLead($lead);

                    $email = new Email();
                    $stat->setEmail($email);

                    $result = new Result();
                    $result->setStat($stat);
                    $result->setContacts(
                        [
                            $lead,
                        ]
                    );

                    return $result;
                }
            );

        $statRepo = $this->getMockBuilder(StatRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $statRepo->expects($this->once())
            ->method('saveEntity');

        $leadModel = $this->getMockBuilder(LeadModel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $translator = $this->getMockBuilder(Translator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $logger = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $doNotContact = $this->createMock(DoNotContact::class);

        $bouncer = new Bounce($transport, $contactFinder, $statRepo, $leadModel, $translator, $logger, $doNotContact);

        $message            = new Message();
        $message->to        = ['contact+bounce_123abc@test.com' => null];
        $message->dsnReport = <<<'DSN'
Original-Recipient: sdfgsdfg@seznan.cz
Final-Recipient: rfc822;sdfgsdfg@seznan.cz
Action: failed
Status: 5.4.4
Diagnostic-Code: DNS; Host not found
DSN;

        $this->assertTrue($bouncer->process($message));
    }
}
