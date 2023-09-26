<?php

$container->loadFromExtension(
    'leezy_pheanstalk',
    [
        'pheanstalks' => [
            'primary' => [
                'server'  => '%autoborna.beanstalkd_host%',
                'port'    => '%autoborna.beanstalkd_port%',
                'timeout' => '%autoborna.beanstalkd_timeout%',
                'default' => true,
            ],
        ],
    ]
);
