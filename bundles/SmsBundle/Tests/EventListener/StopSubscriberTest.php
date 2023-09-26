<?php

namespace Autoborna\SmsBundle\Tests\EventListener;

use Autoborna\LeadBundle\Entity\DoNotContact;
use Autoborna\LeadBundle\Entity\Lead;
use Autoborna\LeadBundle\Model\DoNotContact as DoNotContactModel;
use Autoborna\SmsBundle\Event\ReplyEvent;
use Autoborna\SmsBundle\EventListener\StopSubscriber;

class StopSubscriberTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|DoNotContact
     */
    private $doNotContactModel;

    protected function setUp(): void
    {
        $this->doNotContactModel = $this->createMock(DoNotContactModel::class);
    }

    public function testLeadAddedToDNC()
    {
        $lead = new Lead();
        $lead->setId(1);
        $event = new ReplyEvent($lead, 'stop');

        $this->doNotContactModel->expects($this->once())
        ->method('addDncForContact')
        ->with(1, 'sms', DoNotContact::UNSUBSCRIBED);

        $this->StopSubscriber()->onReply($event);
    }

    /**
     * @return StopSubscriber
     */
    private function StopSubscriber()
    {
        return new StopSubscriber($this->doNotContactModel);
    }
}
