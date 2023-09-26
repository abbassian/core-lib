<?php

namespace Autoborna\LeadBundle\Tests\EventListener;

use Autoborna\LeadBundle\Entity\Lead;
use Autoborna\LeadBundle\EventListener\PointSubscriber;
use Autoborna\LeadBundle\Model\LeadModel;
use Autoborna\PointBundle\Entity\TriggerEvent;
use Autoborna\PointBundle\Event\TriggerExecutedEvent;
use PHPUnit\Framework\MockObject\MockObject;

class PointSubscriberTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var LeadModel|MockObject
     */
    private $leadModel;

    /**
     * @var PointSubscriber
     */
    private $subscriber;

    protected function setUp(): void
    {
        $this->leadModel  = $this->createMock(LeadModel::class);
        $this->subscriber = new PointSubscriber($this->leadModel);
    }

    public function testOnPointTriggerExecutedIfNotChangeTagsTyoe()
    {
        $triggerEvent = new TriggerEvent();
        $contact      = new Lead();
        $triggerEvent->setType('unknown.type');

        $this->leadModel->expects($this->never())
            ->method('modifyTags');

        $this->subscriber->onTriggerExecute(new TriggerExecutedEvent($triggerEvent, $contact));
    }

    public function testOnPointTriggerExecutedForChangeTagsTyoe()
    {
        $triggerEvent = new TriggerEvent();
        $contact      = new Lead();
        $triggerEvent->setType('lead.changetags');
        $triggerEvent->setProperties([
            'add_tags'    => ['tagA'],
            'remove_tags' => null,
        ]);

        $this->leadModel->expects($this->once())
            ->method('modifyTags')
            ->with($contact, ['tagA'], []);

        $this->subscriber->onTriggerExecute(new TriggerExecutedEvent($triggerEvent, $contact));
    }
}
