<?php

namespace Autoborna\CampaignBundle\Executioner\Dispatcher;

use Doctrine\Common\Collections\ArrayCollection;
use Autoborna\CampaignBundle\CampaignEvents;
use Autoborna\CampaignBundle\Entity\LeadEventLog;
use Autoborna\CampaignBundle\Event\DecisionEvent;
use Autoborna\CampaignBundle\Event\DecisionResultsEvent;
use Autoborna\CampaignBundle\EventCollector\Accessor\Event\DecisionAccessor;
use Autoborna\CampaignBundle\Executioner\Result\EvaluatedContacts;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DecisionDispatcher
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var LegacyEventDispatcher
     */
    private $legacyDispatcher;

    /**
     * DecisionDispatcher constructor.
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        LegacyEventDispatcher $legacyDispatcher
    ) {
        $this->dispatcher       = $dispatcher;
        $this->legacyDispatcher = $legacyDispatcher;
    }

    /**
     * @param mixed $passthrough
     *
     * @return DecisionEvent
     */
    public function dispatchRealTimeEvent(DecisionAccessor $config, LeadEventLog $log, $passthrough)
    {
        $event = new DecisionEvent($config, $log, $passthrough);
        $this->dispatcher->dispatch($config->getEventName(), $event);

        return $event;
    }

    /**
     * @return DecisionEvent
     */
    public function dispatchEvaluationEvent(DecisionAccessor $config, LeadEventLog $log)
    {
        $event = new DecisionEvent($config, $log);

        $this->dispatcher->dispatch(CampaignEvents::ON_EVENT_DECISION_EVALUATION, $event);
        $this->legacyDispatcher->dispatchDecisionEvent($event);

        return $event;
    }

    public function dispatchDecisionResultsEvent(DecisionAccessor $config, ArrayCollection $logs, EvaluatedContacts $evaluatedContacts)
    {
        if (!$logs->count()) {
            return;
        }

        $this->dispatcher->dispatch(
            CampaignEvents::ON_EVENT_DECISION_EVALUATION_RESULTS,
            new DecisionResultsEvent($config, $logs, $evaluatedContacts)
        );
    }
}
