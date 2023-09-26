<?php

namespace Autoborna\CampaignBundle\Executioner\Event;

use Doctrine\Common\Collections\ArrayCollection;
use Autoborna\CampaignBundle\Entity\Event;
use Autoborna\CampaignBundle\Entity\LeadEventLog;
use Autoborna\CampaignBundle\EventCollector\Accessor\Event\AbstractEventAccessor;
use Autoborna\CampaignBundle\Executioner\Dispatcher\ActionDispatcher;
use Autoborna\CampaignBundle\Executioner\Exception\CannotProcessEventException;
use Autoborna\CampaignBundle\Executioner\Logger\EventLogger;
use Autoborna\CampaignBundle\Executioner\Result\EvaluatedContacts;

class ActionExecutioner implements EventInterface
{
    const TYPE = 'action';

    /**
     * @var ActionDispatcher
     */
    private $dispatcher;

    /**
     * @var EventLogger
     */
    private $eventLogger;

    /**
     * ActionExecutioner constructor.
     */
    public function __construct(ActionDispatcher $dispatcher, EventLogger $eventLogger)
    {
        $this->dispatcher         = $dispatcher;
        $this->eventLogger        = $eventLogger;
    }

    /**
     * @return EvaluatedContacts
     *
     * @throws CannotProcessEventException
     * @throws \Autoborna\CampaignBundle\Executioner\Dispatcher\Exception\LogNotProcessedException
     * @throws \Autoborna\CampaignBundle\Executioner\Dispatcher\Exception\LogPassedAndFailedException
     */
    public function execute(AbstractEventAccessor $config, ArrayCollection $logs)
    {
        /** @var LeadEventLog $firstLog */
        if (!$firstLog = $logs->first()) {
            return new EvaluatedContacts();
        }

        $event = $firstLog->getEvent();

        if (Event::TYPE_ACTION !== $event->getEventType()) {
            throw new CannotProcessEventException('Cannot process event ID '.$event->getId().' as an action.');
        }

        // Execute to process the batch of contacts
        $pendingEvent = $this->dispatcher->dispatchEvent($config, $event, $logs);

        /** @var ArrayCollection $contacts */
        $passed = $this->eventLogger->extractContactsFromLogs($pendingEvent->getSuccessful());
        $failed = $this->eventLogger->extractContactsFromLogs($pendingEvent->getFailures());

        return new EvaluatedContacts($passed, $failed);
    }
}
