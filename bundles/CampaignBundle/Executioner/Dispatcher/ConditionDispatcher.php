<?php

namespace Autoborna\CampaignBundle\Executioner\Dispatcher;

use Autoborna\CampaignBundle\CampaignEvents;
use Autoborna\CampaignBundle\Entity\LeadEventLog;
use Autoborna\CampaignBundle\Event\ConditionEvent;
use Autoborna\CampaignBundle\EventCollector\Accessor\Event\ConditionAccessor;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ConditionDispatcher
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * ConditionDispatcher constructor.
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return ConditionEvent
     */
    public function dispatchEvent(ConditionAccessor $config, LeadEventLog $log)
    {
        $event = new ConditionEvent($config, $log);
        $this->dispatcher->dispatch($config->getEventName(), $event);
        $this->dispatcher->dispatch(CampaignEvents::ON_EVENT_CONDITION_EVALUATION, $event);

        return $event;
    }
}
