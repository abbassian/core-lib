<?php

namespace Autoborna\EmailBundle\Tests\MonitoredEmail\Search;

use Autoborna\EmailBundle\Entity\Email;
use Autoborna\EmailBundle\Entity\Stat;
use Autoborna\EmailBundle\Entity\StatRepository;
use Autoborna\EmailBundle\MonitoredEmail\Search\ContactFinder;
use Autoborna\EmailBundle\MonitoredEmail\Search\Result;
use Autoborna\LeadBundle\Entity\Lead;
use Autoborna\LeadBundle\Entity\LeadRepository;
use Monolog\Logger;

class ContactFinderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @testdox Contact should be found via contact email address
     *
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Search\ContactFinder::find()
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Search\ContactFinder::findByAddress()
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Search\Result::setStat()
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Search\Result::getStat()
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Search\Result::setContacts()
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Search\Result::getContacts()
     */
    public function testContactFoundByDelegationForAddress()
    {
        $lead = new Lead();
        $lead->setEmail('contact@email.com');

        $statRepository = $this->getMockBuilder(StatRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $statRepository->expects($this->never())
            ->method('findOneBy');

        $leadRepository = $this->getMockBuilder(LeadRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $leadRepository->expects($this->once())
            ->method('getContactsByEmail')
            ->willReturn([$lead]);

        $logger = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $finder = new ContactFinder($statRepository, $leadRepository, $logger);
        $result = $finder->find($lead->getEmail(), 'contact@test.com');

        $this->assertInstanceOf(Result::class, $result);

        $this->assertEquals($result->getContacts(), [$lead]);
    }

    /**
     * @testdox Contact should be found via a hash in to email address
     *
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Search\ContactFinder::find()
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Search\ContactFinder::findByHash()
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Processor\Address::parseAddressForStatHash()
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Search\Result::setStat()
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Search\Result::getStat()
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Search\Result::addContact()
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Search\Result::getContacts()
     */
    public function testContactFoundByDelegationForHash()
    {
        $lead = new Lead();
        $lead->setEmail('contact@email.com');

        $stat = new Stat();
        $stat->setLead($lead);

        $statRepository = $this->getMockBuilder(StatRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $statRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturnCallback(
                function ($hash) use ($stat) {
                    $stat->setTrackingHash($hash);

                    $email = new Email();
                    $stat->setEmail($email);

                    return $stat;
                }
            );

        $leadRepository = $this->getMockBuilder(LeadRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $leadRepository->expects($this->never())
            ->method('getContactsByEmail');

        $logger = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $finder = new ContactFinder($statRepository, $leadRepository, $logger);
        $result = $finder->find($lead->getEmail(), 'test+unsubscribe_123abc@test.com');

        $this->assertInstanceOf(Result::class, $result);

        $this->assertEquals($result->getStat(), $stat);
        $this->assertEquals($result->getContacts(), [$lead]);
    }
}
