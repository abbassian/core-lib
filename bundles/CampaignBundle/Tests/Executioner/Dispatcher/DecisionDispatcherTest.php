<?php

declare(strict_types=1);

namespace Autoborna\CampaignBundle\Tests\Executioner\Dispatcher;

use Doctrine\Common\Collections\ArrayCollection;
use Autoborna\CampaignBundle\CampaignEvents;
use Autoborna\CampaignBundle\Entity\LeadEventLog;
use Autoborna\CampaignBundle\Event\DecisionEvent;
use Autoborna\CampaignBundle\Event\DecisionResultsEvent;
use Autoborna\CampaignBundle\EventCollector\Accessor\Event\DecisionAccessor;
use Autoborna\CampaignBundle\Executioner\Dispatcher\DecisionDispatcher;
use Autoborna\CampaignBundle\Executioner\Dispatcher\LegacyEventDispatcher;
use Autoborna\CampaignBundle\Executioner\Result\EvaluatedContacts;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DecisionDispatcherTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var MockObject|EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var MockObject|LegacyEventDispatcher
     */
    private $legacyDispatcher;

    /**
     * @var MockObject|DecisionAccessor
     */
    private $config;

    /**
     * @var DecisionDispatcher
     */
    private $decisionDispatcher;

    protected function setUp(): void
    {
        $this->dispatcher         = $this->createMock(EventDispatcherInterface::class);
        $this->legacyDispatcher   = $this->createMock(LegacyEventDispatcher::class);
        $this->config             = $this->createMock(DecisionAccessor::class);
        $this->decisionDispatcher = new DecisionDispatcher($this->dispatcher, $this->legacyDispatcher);
    }

    public function testDecisionEventIsDispatched(): void
    {
        $this->config->expects($this->once())
            ->method('getEventName')
            ->willReturn('something');

        $this->legacyDispatcher->expects($this->never())
            ->method('dispatchDecisionEvent');

        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with('something', $this->isInstanceOf(DecisionEvent::class));

        $this->decisionDispatcher->dispatchRealTimeEvent($this->config, new LeadEventLog(), null);
    }

    public function testDecisionEvaluationEventIsDispatched(): void
    {
        $this->config->expects($this->never())
            ->method('getEventName');

        $this->legacyDispatcher->expects($this->once())
            ->method('dispatchDecisionEvent');

        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with(CampaignEvents::ON_EVENT_DECISION_EVALUATION, $this->isInstanceOf(DecisionEvent::class));

        $this->decisionDispatcher->dispatchEvaluationEvent($this->config, new LeadEventLog());
    }

    public function testDecisionResultsEventIsDispatched(): void
    {
        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with(CampaignEvents::ON_EVENT_DECISION_EVALUATION_RESULTS, $this->isInstanceOf(DecisionResultsEvent::class));

        $this->decisionDispatcher->dispatchDecisionResultsEvent($this->config, new ArrayCollection([new LeadEventLog()]), new EvaluatedContacts());
    }
}
