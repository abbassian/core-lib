<?php

namespace Autoborna\EmailBundle\Tests\EventListener;

use Autoborna\LeadBundle\Entity\Lead;
use Autoborna\LeadBundle\Event\ContactIdentificationEvent;
use Autoborna\SmsBundle\Entity\Sms;
use Autoborna\SmsBundle\Entity\Stat;
use Autoborna\SmsBundle\Entity\StatRepository;
use Autoborna\SmsBundle\EventListener\TrackingSubscriber;

class TrackingSubscriberTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|StatRepository
     */
    private $statRepository;

    protected function setUp(): void
    {
        $this->statRepository = $this->createMock(StatRepository::class);
    }

    public function testIdentifyContactByStat()
    {
        $ct = [
                'lead'    => 2,
                'channel' => [
                    'sms' => 1,
                ],
                'stat'    => 'abc123',
        ];

        $sms = $this->createMock(Sms::class);
        $sms->method('getId')
            ->willReturn(1);

        $lead = $this->createMock(Lead::class);
        $lead->method('getId')
            ->willReturn(2);

        $stat = new Stat();
        $stat->setSms($sms);
        $stat->setLead($lead);

        $this->statRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['trackingHash' => 'abc123'])
            ->willReturn($stat);

        $event = new ContactIdentificationEvent($ct);

        $this->getSubscriber()->onIdentifyContact($event);

        $this->assertEquals($lead->getId(), $event->getIdentifiedContact()->getId());
    }

    public function testChannelMismatchDoesNotIdentify()
    {
        $ct = [
            'lead'    => 2,
            'channel' => [
                'email' => 1,
            ],
            'stat'    => 'abc123',
        ];

        $event = new ContactIdentificationEvent($ct);

        $this->getSubscriber()->onIdentifyContact($event);

        $this->assertNull($event->getIdentifiedContact());
    }

    public function testChannelIdMismatchDoesNotIdentify()
    {
        $ct = [
            'lead'    => 2,
            'channel' => [
                'sms' => 2,
            ],
            'stat'    => 'abc123',
        ];

        $sms = $this->createMock(Sms::class);
        $sms->method('getId')
            ->willReturn(1);

        $lead = $this->createMock(Lead::class);
        $lead->method('getId')
            ->willReturn(2);

        $stat = new Stat();
        $stat->setSms($sms);
        $stat->setLead($lead);

        $this->statRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['trackingHash' => 'abc123'])
            ->willReturn($stat);

        $event = new ContactIdentificationEvent($ct);

        $this->getSubscriber()->onIdentifyContact($event);

        $this->assertNull($event->getIdentifiedContact());
    }

    public function testStatEmptyLeadDoesNotIdentify()
    {
        $ct = [
            'lead'    => 2,
            'channel' => [
                'sms' => 2,
            ],
            'stat'    => 'abc123',
        ];

        $sms = $this->createMock(Sms::class);
        $sms->method('getId')
            ->willReturn(1);

        $stat = new Stat();
        $stat->setSms($sms);

        $this->statRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['trackingHash' => 'abc123'])
            ->willReturn($stat);

        $event = new ContactIdentificationEvent($ct);

        $this->getSubscriber()->onIdentifyContact($event);

        $this->assertNull($event->getIdentifiedContact());
    }

    /**
     * @return TrackingSubscriber
     */
    private function getSubscriber()
    {
        return new TrackingSubscriber($this->statRepository);
    }
}
