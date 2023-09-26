<?php

return [
    'routes' => [
        'main' => [
            'autoborna_calendar_index' => [
                'path'       => '/calendar',
                'controller' => 'AutobornaCalendarBundle:Default:index',
            ],
            'autoborna_calendar_action' => [
                'path'       => '/calendar/{objectAction}',
                'controller' => 'AutobornaCalendarBundle:Default:execute',
            ],
        ],
    ],
    'services' => [
        'models' => [
            'autoborna.calendar.model.calendar' => [
                'class' => 'Autoborna\CalendarBundle\Model\CalendarModel',
            ],
        ],
    ],
    'menu' => [
        'main' => [
            'priority' => 90,
            'items'    => [
                'autoborna.calendar.menu.index' => [
                    'route'     => 'autoborna_calendar_index',
                    'iconClass' => 'fa-calendar',
                ],
            ],
        ],
    ],
];
