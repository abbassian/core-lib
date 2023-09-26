<?php

namespace Autoborna\CalendarBundle;

/**
 * Class CalendarEvents.
 *
 * Events available for CalendarBundle
 */
final class CalendarEvents
{
    /**
     * The autoborna.calendar_on_generate event is thrown when generating a calendar view.
     *
     * The event listener receives a Autoborna\CalendarBundle\Event\CalendarGeneratorEvent instance.
     *
     * @var string
     */
    const CALENDAR_ON_GENERATE = 'autoborna.calendar_on_generate';

    /**
     * The autoborna.calendar_event_on_generate event is thrown when generating a calendar edit / new view.
     *
     * The event listener receives a Autoborna\CalendarBundle\Event\EventGeneratorEvent instance.
     *
     * @var string
     */
    const CALENDAR_EVENT_ON_GENERATE = 'autoborna.calendar_event_on_generate';
}
