<?php

namespace Autoborna\EmailBundle\Tests\MonitoredEmail\Processor;

use Autoborna\CoreBundle\Translation\Translator;
use Autoborna\EmailBundle\Entity\Email;
use Autoborna\EmailBundle\Entity\Stat;
use Autoborna\EmailBundle\MonitoredEmail\Message;
use Autoborna\EmailBundle\MonitoredEmail\Processor\Unsubscribe;
use Autoborna\EmailBundle\MonitoredEmail\Search\ContactFinder;
use Autoborna\EmailBundle\MonitoredEmail\Search\Result;
use Autoborna\EmailBundle\Tests\MonitoredEmail\Transport\TestTransport;
use Autoborna\LeadBundle\Entity\Lead;
use Autoborna\LeadBundle\Model\DoNotContact;
use Monolog\Logger;

class UnsubscribeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @testdox Test that the transport interface processes the message appropriately
     *
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Processor\Unsubscribe::process()
     * @covers  \Autoborna\EmailBundle\Swiftmailer\Transport\UnsubscriptionProcessorInterface::processUnsubscription()
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
                function ($email) {
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

        $translator = $this->getMockBuilder(Translator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $logger = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $doNotContact = $this->getMockBuilder(DoNotContact::class)
            ->disableOriginalConstructor()
            ->getMock();

        $processor = new Unsubscribe($transport, $contactFinder, $translator, $logger, $doNotContact);

        $message = new Message();
        $this->assertTrue($processor->process($message));
    }

    /**
     * @testdox Test that the message is processed appropriately
     *
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Processor\Unsubscribe::process()
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
                function ($email) {
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

        $translator = $this->getMockBuilder(Translator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $logger = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $doNotContact = $this->getMockBuilder(DoNotContact::class)
            ->disableOriginalConstructor()
            ->getMock();

        $processor = new Unsubscribe($transport, $contactFinder, $translator, $logger, $doNotContact);

        $message     = new Message();
        $message->to = ['contact+unsubscribe_123abc@test.com' => null];
        $this->assertTrue($processor->process($message));
    }
}
