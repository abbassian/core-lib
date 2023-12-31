<?php

declare(strict_types=1);

namespace Autoborna\CampaignBundle\Tests\Executioner\Dispatcher;

use Doctrine\Common\Collections\ArrayCollection;
use Autoborna\CampaignBundle\CampaignEvents;
use Autoborna\CampaignBundle\Entity\Campaign;
use Autoborna\CampaignBundle\Entity\Event;
use Autoborna\CampaignBundle\Entity\LeadEventLog;
use Autoborna\CampaignBundle\Event\CampaignExecutionEvent;
use Autoborna\CampaignBundle\Event\ExecutedBatchEvent;
use Autoborna\CampaignBundle\Event\ExecutedEvent;
use Autoborna\CampaignBundle\Event\FailedEvent;
use Autoborna\CampaignBundle\Event\PendingEvent;
use Autoborna\CampaignBundle\EventCollector\Accessor\Event\AbstractEventAccessor;
use Autoborna\CampaignBundle\Executioner\Dispatcher\LegacyEventDispatcher;
use Autoborna\CampaignBundle\Executioner\Helper\NotificationHelper;
use Autoborna\CampaignBundle\Executioner\Scheduler\EventScheduler;
use Autoborna\CoreBundle\Factory\AutobornaFactory;
use Autoborna\LeadBundle\Entity\Lead;
use Autoborna\LeadBundle\Tracker\ContactTracker;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class LegacyEventDispatcherTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var MockObject|EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var MockObject|EventScheduler
     */
    private $scheduler;

    /**
     * @var MockObject|NotificationHelper
     */
    private $notificationHelper;

    /**
     * @var MockObject|AutobornaFactory
     */
    private $autobornaFactory;

    /**
     * @var MockObject|ContactTracker
     */
    private $contactTracker;

    /**
     * @var MockObject|AbstractEventAccessor
     */
    private $config;

    /**
     * @var MockObject|PendingEvent
     */
    private $pendingEvent;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dispatcher         = $this->createMock(EventDispatcherInterface::class);
        $this->scheduler          = $this->createMock(EventScheduler::class);
        $this->notificationHelper = $this->createMock(NotificationHelper::class);
        $this->autobornaFactory      = $this->createMock(AutobornaFactory::class);
        $this->contactTracker     = $this->createMock(ContactTracker::class);
        $this->config             = $this->createMock(AbstractEventAccessor::class);
        $this->pendingEvent       = $this->createMock(PendingEvent::class);
    }

    public function testAllEventsAreFailedWithBadConfig(): void
    {
        $this->config->expects($this->once())
            ->method('getConfig')
            ->willReturn([]);

        $logs = new ArrayCollection([new LeadEventLog()]);

        $this->pendingEvent->expects($this->once())
            ->method('failAll');

        $this->getLegacyEventDispatcher()->dispatchCustomEvent($this->config, $logs, false, $this->pendingEvent, $this->autobornaFactory);
    }

    public function testPrimayLegacyEventsAreProcessed(): void
    {
        $this->config->expects($this->exactly(2))
            ->method('getConfig')
            ->willReturn(['eventName' => 'something']);

        $event    = new Event();
        $campaign = new Campaign();
        $event->setCampaign($campaign);
        $leadEventLog = new LeadEventLog();
        $leadEventLog->setEvent($event);
        $leadEventLog->setLead(new Lead());
        $logs = new ArrayCollection([$leadEventLog]);

        // BC default is to have pass
        $this->pendingEvent->expects($this->once())
            ->method('pass');

        $this->contactTracker->expects($this->exactly(2))
            ->method('setSystemContact');

        $this->dispatcher->expects($this->exactly(4))
            ->method('dispatch')
            ->withConsecutive(
                // Legacy custom event should dispatch
                ['something', $this->isInstanceOf(CampaignExecutionEvent::class)],
                // Legacy execution event should dispatch
                [CampaignEvents::ON_EVENT_EXECUTION, $this->isInstanceOf(CampaignExecutionEvent::class)],
                [CampaignEvents::ON_EVENT_EXECUTED, $this->isInstanceOf(ExecutedEvent::class)],
                [CampaignEvents::ON_EVENT_EXECUTED_BATCH, $this->isInstanceOf(ExecutedBatchEvent::class)]
            );

        $this->getLegacyEventDispatcher()->dispatchCustomEvent($this->config, $logs, false, $this->pendingEvent);
    }

    public function testPrimaryCallbackIsProcessed(): void
    {
        $this->config->expects($this->exactly(2))
            ->method('getConfig')
            ->willReturn(['callback' => [self::class, 'bogusCallback']]);

        $event    = new Event();
        $campaign = new Campaign();
        $event->setCampaign($campaign);
        $leadEventLog = new LeadEventLog();
        $leadEventLog->setEvent($event);
        $leadEventLog->setLead(new Lead());
        $logs = new ArrayCollection([$leadEventLog]);

        // BC default is to have pass
        $this->pendingEvent->expects($this->once())
            ->method('pass');

        $this->contactTracker->expects($this->exactly(2))
            ->method('setSystemContact');

        // Legacy execution event should dispatch
        $this->dispatcher->expects($this->exactly(3))
            ->method('dispatch')
            ->withConsecutive(
                [CampaignEvents::ON_EVENT_EXECUTION, $this->isInstanceOf(CampaignExecutionEvent::class)],
                [CampaignEvents::ON_EVENT_EXECUTED, $this->isInstanceOf(ExecutedEvent::class)],
                [CampaignEvents::ON_EVENT_EXECUTED_BATCH, $this->isInstanceOf(ExecutedBatchEvent::class)]
            );

        $this->getLegacyEventDispatcher()->dispatchCustomEvent($this->config, $logs, false, $this->pendingEvent);
    }

    public function testArrayResultAppendedToMetadata(): void
    {
        $this->config->expects($this->exactly(2))
            ->method('getConfig')
            ->willReturn(['eventName' => 'something']);

        $event    = new Event();
        $campaign = new Campaign();
        $event->setCampaign($campaign);
        $leadEventLog = new LeadEventLog();
        $leadEventLog->setEvent($event);
        $leadEventLog->setLead(new Lead());
        $leadEventLog->setMetadata(['bar' => 'foo']);

        $logs = new ArrayCollection([$leadEventLog]);

        // BC default is to have pass
        $this->pendingEvent->expects($this->once())
            ->method('pass');

        $this->contactTracker->expects($this->exactly(2))
            ->method('setSystemContact');

        // Legacy custom event should dispatch
        $this->dispatcher->expects($this->exactly(4))
            ->method('dispatch')
            ->withConsecutive(
                ['something', $this->isInstanceOf(CampaignExecutionEvent::class)],
                [CampaignEvents::ON_EVENT_EXECUTION, $this->isInstanceOf(CampaignExecutionEvent::class)],
                [CampaignEvents::ON_EVENT_EXECUTED, $this->isInstanceOf(ExecutedEvent::class)],
                [CampaignEvents::ON_EVENT_EXECUTED_BATCH, $this->isInstanceOf(ExecutedBatchEvent::class)]
            )
            ->willReturnOnConsecutiveCalls(
                $this->returnCallback(
                    function (string $eventName, CampaignExecutionEvent $event) {
                        $event->setResult(['foo' => 'bar']);
                    }
                )
            );

        $this->getLegacyEventDispatcher()->dispatchCustomEvent($this->config, $logs, false, $this->pendingEvent);

        $this->assertEquals(['bar' => 'foo', 'foo' => 'bar'], $leadEventLog->getMetadata());
    }

    public function testFailedResultAsFalseIsProcessed(): void
    {
        $this->config->expects($this->exactly(2))
            ->method('getConfig')
            ->willReturn(['eventName' => 'something']);

        $lead     = new Lead();
        $event    = new Event();
        $campaign = new Campaign();
        $event->setCampaign($campaign);
        $leadEventLog = new LeadEventLog();
        $leadEventLog->setEvent($event);
        $leadEventLog->setLead($lead);
        $leadEventLog->setMetadata(['bar' => 'foo']);

        $logs = new ArrayCollection([$leadEventLog]);

        // Should fail because we're returning false
        $this->pendingEvent->expects($this->once())
            ->method('fail');

        $this->contactTracker->expects($this->exactly(2))
            ->method('setSystemContact');

        // Legacy custom event should dispatch
        $this->dispatcher->expects($this->exactly(3))
            ->method('dispatch')
            ->withConsecutive(
                ['something', $this->isInstanceOf(CampaignExecutionEvent::class)],
                [CampaignEvents::ON_EVENT_EXECUTION, $this->isInstanceOf(CampaignExecutionEvent::class)],
                [CampaignEvents::ON_EVENT_FAILED, $this->isInstanceOf(FailedEvent::class)]
            )
            ->willReturnOnConsecutiveCalls(
                $this->returnCallback(
                    function ($eventName, $event) {
                        $event->setResult(false);
                    }
                )
            );

        $this->scheduler->expects($this->once())
            ->method('rescheduleFailures');

        $this->notificationHelper->expects($this->once())
            ->method('notifyOfFailure')
            ->with($lead, $event);

        $this->getLegacyEventDispatcher()->dispatchCustomEvent($this->config, $logs, false, $this->pendingEvent);
    }

    public function testFailedResultAsArrayIsProcessed(): void
    {
        $this->config->expects($this->exactly(2))
            ->method('getConfig')
            ->willReturn(['eventName' => 'something']);

        $event    = new Event();
        $campaign = new Campaign();
        $event->setCampaign($campaign);
        $leadEventLog = new LeadEventLog();
        $leadEventLog->setEvent($event);
        $leadEventLog->setLead(new Lead());

        $logs = new ArrayCollection([$leadEventLog]);

        // Should fail because we're returning false
        $this->pendingEvent->expects($this->once())
            ->method('fail');

        $this->contactTracker->expects($this->exactly(2))
            ->method('setSystemContact');

        // Legacy custom event should dispatch
        $this->dispatcher->expects($this->exactly(3))
            ->method('dispatch')
            ->withConsecutive(
                ['something', $this->isInstanceOf(CampaignExecutionEvent::class)],
                [CampaignEvents::ON_EVENT_EXECUTION, $this->isInstanceOf(CampaignExecutionEvent::class)],
                [CampaignEvents::ON_EVENT_FAILED, $this->isInstanceOf(FailedEvent::class)]
            )
            ->willReturnOnConsecutiveCalls(
                $this->returnCallback(
                    function ($eventName, CampaignExecutionEvent $event) {
                        $event->setResult(['result' => false, 'foo' => 'bar']);
                    }
                )
            );

        $this->scheduler->expects($this->once())
            ->method('rescheduleFailures');

        $this->getLegacyEventDispatcher()->dispatchCustomEvent($this->config, $logs, false, $this->pendingEvent);
    }

    public function testPassWithErrorIsHandled(): void
    {
        $this->config->expects($this->exactly(2))
            ->method('getConfig')
            ->willReturn(['eventName' => 'something']);

        $event    = new Event();
        $campaign = new Campaign();
        $event->setCampaign($campaign);
        $leadEventLog = new LeadEventLog();
        $leadEventLog->setEvent($event);
        $leadEventLog->setLead(new Lead());
        $leadEventLog->setMetadata(['bar' => 'foo']);

        $logs = new ArrayCollection([$leadEventLog]);

        // Should pass but with an error logged
        $this->pendingEvent->expects($this->once())
            ->method('passWithError');

        $this->contactTracker->expects($this->exactly(2))
            ->method('setSystemContact');

        // Legacy custom event should dispatch
        $this->dispatcher->method('dispatch')
            ->withConsecutive(['something', $this->isInstanceOf(CampaignExecutionEvent::class)])
            ->willReturnOnConsecutiveCalls(
                $this->returnCallback(
                    function ($eventName, CampaignExecutionEvent $event) {
                        $event->setResult(['failed' => 1, 'reason' => 'because']);
                    }
                )
            );

        $this->scheduler->expects($this->never())
            ->method('rescheduleFailure');

        $this->getLegacyEventDispatcher()->dispatchCustomEvent($this->config, $logs, false, $this->pendingEvent);
    }

    public function testLogIsPassed(): void
    {
        $this->config->expects($this->exactly(2))
            ->method('getConfig')
            ->willReturn(['eventName' => 'something']);

        $event    = new Event();
        $campaign = new Campaign();
        $event->setCampaign($campaign);
        $leadEventLog = new LeadEventLog();
        $leadEventLog->setEvent($event);
        $leadEventLog->setLead(new Lead());
        $leadEventLog->setMetadata(['bar' => 'foo']);

        $logs = new ArrayCollection([$leadEventLog]);

        // Should fail because we're returning false
        $this->pendingEvent->expects($this->once())
            ->method('pass');

        $this->contactTracker->expects($this->exactly(2))
            ->method('setSystemContact');

        // Should pass
        $this->dispatcher->method('dispatch')
            ->withConsecutive(['something', $this->isInstanceOf(CampaignExecutionEvent::class)])
            ->willReturnOnConsecutiveCalls(
                $this->returnCallback(
                    function ($eventName, CampaignExecutionEvent $event) {
                        $event->setResult(true);
                    }
                )
            );

        $this->scheduler->expects($this->never())
            ->method('rescheduleFailure');

        $this->getLegacyEventDispatcher()->dispatchCustomEvent($this->config, $logs, false, $this->pendingEvent);
    }

    public function testLegacyEventDispatchedForConvertedBatchActions(): void
    {
        $this->config->expects($this->exactly(1))
            ->method('getConfig')
            ->willReturn(['eventName' => 'something']);

        $event    = new Event();
        $campaign = new Campaign();
        $event->setCampaign($campaign);
        $leadEventLog = new LeadEventLog();
        $leadEventLog->setEvent($event);
        $leadEventLog->setLead(new Lead());
        $leadEventLog->setMetadata(['bar' => 'foo']);

        $logs = new ArrayCollection([$leadEventLog]);

        // Should never be called
        $this->pendingEvent->expects($this->never())
            ->method('pass');

        $this->contactTracker->expects($this->exactly(2))
            ->method('setSystemContact');

        $this->dispatcher->method('dispatch')
            ->withConsecutive(['something', $this->isInstanceOf(CampaignExecutionEvent::class)])
            ->willReturnOnConsecutiveCalls(
                $this->returnCallback(
                    function ($eventName, CampaignExecutionEvent $event) {
                        $event->setResult(true);
                    }
                )
            );

        $this->getLegacyEventDispatcher()->dispatchCustomEvent($this->config, $logs, true, $this->pendingEvent);
    }

    private function getLegacyEventDispatcher(): LegacyEventDispatcher
    {
        return new LegacyEventDispatcher(
            $this->dispatcher,
            $this->scheduler,
            new NullLogger(),
            $this->notificationHelper,
            $this->autobornaFactory,
            $this->contactTracker
        );
    }

    public static function bogusCallback(): bool
    {
        return true;
    }
}
