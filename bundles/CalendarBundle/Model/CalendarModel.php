<?php

namespace Autoborna\CalendarBundle\Model;

use Autoborna\CalendarBundle\CalendarEvents;
use Autoborna\CalendarBundle\Event\CalendarGeneratorEvent;
use Autoborna\CalendarBundle\Event\EventGeneratorEvent;
use Autoborna\CoreBundle\Model\FormModel;

/**
 * Class CalendarModel.
 */
class CalendarModel extends FormModel
{
    /**
     * Collects data for the calendar display.
     *
     * @param array $dates Associative array containing a 'start_date' and 'end_date' key
     *
     * @return array
     */
    public function getCalendarEvents(array $dates)
    {
        $event = new CalendarGeneratorEvent($dates);
        $this->dispatcher->dispatch(CalendarEvents::CALENDAR_ON_GENERATE, $event);

        return $event->getEvents();
    }

    /**
     * Collects data for the calendar display.
     *
     * @param string $bundle
     * @param int    $id
     *
     * @return array
     */
    public function editCalendarEvent($bundle, $id)
    {
        $event = new EventGeneratorEvent($bundle, $id);
        $this->dispatcher->dispatch(CalendarEvents::CALENDAR_EVENT_ON_GENERATE, $event);

        return $event;
    }
}
