<?php

return [
    'services' => [
        'other' => [
            'autoborna.stats.aggregate.collector' => [
                'class'     => \Autoborna\StatsBundle\Aggregate\Collector::class,
                'arguments' => [
                    'event_dispatcher',
                ],
            ],
        ],
    ],
];
