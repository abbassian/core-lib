<?php

return [
    'routes'   => [
        'main' => [
            'autoborna_core_ajax' => [
                'path'       => '/ajax',
                'controller' => 'AutobornaCoreBundle:Ajax:delegateAjax',
            ],
        ],
    ],
    'menu'     => [
        'main' => [
            'autoborna.core.components' => [
                'id'        => 'autoborna_components_root',
                'iconClass' => 'fa-puzzle-piece',
                'priority'  => 60,
            ],
        ],
    ],
    'services' => [
        'helpers'  => [
            'autoborna.helper.bundle' => [
                'class'     => 'Autoborna\CoreBundle\Helper\BundleHelper',
                'arguments' => [
                    '%autoborna.bundles%',
                    '%autoborna.plugin.bundles%',
                ],
            ],
        ],
        'other'    => [
            'autoborna.http.client' => [
                'class' => GuzzleHttp\Client::class,
            ],
        ],
        'fixtures' => [
            'autoborna.test.fixture' => [
                'class'    => 'Foo\Bar\NonExisting',
                'optional' => true,
            ],
        ],
    ],

    'ip_lookup_services' => [
        'extreme-ip' => [
            'display_name' => 'Extreme-IP',
            'class'        => 'Autoborna\CoreBundle\IpLookup\ExtremeIpLookup',
        ],
    ],

    'parameters' => [
        'log_path'      => '%kernel.root_dir%/../var/logs',
        'max_log_files' => 7,
        'image_path'    => 'media/images',
        'bool_value'    => false,
        'null_value'    => null,
        'array_value'   => [],
    ],
];
