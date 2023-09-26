<?php

return [
    'routes' => [
        'main' => [
            'autoborna_dashboard_index' => [
                'path'       => '/dashboard',
                'controller' => 'AutobornaDashboardBundle:Dashboard:index',
            ],
            'autoborna_dashboard_widget' => [
                'path'       => '/dashboard/widget/{widgetId}',
                'controller' => 'AutobornaDashboardBundle:Dashboard:widget',
            ],
            'autoborna_dashboard_action' => [
                'path'       => '/dashboard/{objectAction}/{objectId}',
                'controller' => 'AutobornaDashboardBundle:Dashboard:execute',
            ],
        ],
        'api' => [
            'autoborna_widget_types' => [
                'path'       => '/data',
                'controller' => 'AutobornaDashboardBundle:Api\WidgetApi:getTypes',
            ],
            'autoborna_widget_data' => [
                'path'       => '/data/{type}',
                'controller' => 'AutobornaDashboardBundle:Api\WidgetApi:getData',
            ],
        ],
    ],

    'menu' => [
        'main' => [
            'priority' => 100,
            'items'    => [
                'autoborna.dashboard.menu.index' => [
                    'route'     => 'autoborna_dashboard_index',
                    'iconClass' => 'fa-th-large',
                ],
            ],
        ],
    ],
    'services' => [
        'forms' => [
            'autoborna.dashboard.form.type.widget' => [
                'class'     => 'Autoborna\DashboardBundle\Form\Type\WidgetType',
                'arguments' => [
                    'event_dispatcher',
                    'autoborna.security',
                ],
            ],
        ],
        'models' => [
            'autoborna.dashboard.model.dashboard' => [
                'class'     => 'Autoborna\DashboardBundle\Model\DashboardModel',
                'arguments' => [
                    'autoborna.helper.core_parameters',
                    'autoborna.helper.paths',
                    'symfony.filesystem',
                ],
            ],
        ],
        'other' => [
            'autoborna.dashboard.widget' => [
                'class'     => \Autoborna\DashboardBundle\Dashboard\Widget::class,
                'arguments' => [
                    'autoborna.dashboard.model.dashboard',
                    'autoborna.helper.user',
                    'session',
                ],
            ],
        ],
    ],
    'parameters' => [
        'dashboard_import_dir'      => '%kernel.root_dir%/../media/dashboards',
        'dashboard_import_user_dir' => null,
    ],
];
