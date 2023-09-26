<?php

namespace Autoborna\CampaignBundle\EventCollector;

use Autoborna\CampaignBundle\CampaignEvents;
use Autoborna\CampaignBundle\Entity\Event;
use Autoborna\CampaignBundle\Event\CampaignBuilderEvent;
use Autoborna\CampaignBundle\EventCollector\Accessor\Event\AbstractEventAccessor;
use Autoborna\CampaignBundle\EventCollector\Accessor\EventAccessor;
use Autoborna\CampaignBundle\EventCollector\Builder\ConnectionBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Translation\TranslatorInterface;

class EventCollector
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var array
     */
    private $eventsArray = [];

    /**
     * @var EventAccessor
     */
    private $events;

    /**
     * EventCollector constructor.
     */
    public function __construct(TranslatorInterface $translator, EventDispatcherInterface $dispatcher)
    {
        $this->translator = $translator;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return EventAccessor
     */
    public function getEvents()
    {
        if (empty($this->eventsArray)) {
            $this->buildEventList();
        }

        if (empty($this->events)) {
            $this->events = new EventAccessor($this->eventsArray);
        }

        return $this->events;
    }

    /**
     * @return AbstractEventAccessor
     */
    public function getEventConfig(Event $event)
    {
        return $this->getEvents()->getEvent($event->getEventType(), $event->getType());
    }

    /**
     * Deprecated support for pre 2.13.
     *
     * @deprecated 2.13.0 to be removed in 3.0
     *
     * @param string|null $type
     *
     * @return array|mixed
     */
    public function getEventsArray($type = null)
    {
        if (empty($this->eventsArray)) {
            $this->buildEventList();
        }

        if (null !== $type) {
            if (!isset($this->events[$type])) {
                throw new \InvalidArgumentException("$type not found as array key");
            }

            return $this->eventsArray[$type];
        }

        return $this->eventsArray;
    }

    private function buildEventList()
    {
        //build them
        $event  = new CampaignBuilderEvent($this->translator);
        $this->dispatcher->dispatch(CampaignEvents::CAMPAIGN_ON_BUILD, $event);

        $this->eventsArray[Event::TYPE_ACTION]    = $event->getActions();
        $this->eventsArray[Event::TYPE_CONDITION] = $event->getConditions();
        $this->eventsArray[Event::TYPE_DECISION]  = $event->getDecisions();

        $this->eventsArray['connectionRestrictions'] = ConnectionBuilder::buildRestrictionsArray($this->eventsArray);
    }
}
