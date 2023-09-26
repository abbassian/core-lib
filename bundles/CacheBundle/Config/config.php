<?php

declare(strict_types=1);

return [
    'routes'   => [
        'main'   => [],
        'public' => [],
        'api'    => [],
    ],
    'menu'     => [],
    'services' => [
        'events'    => [
            'autoborna.cache.clear_cache_subscriber' => [
                'class'     => \Autoborna\CacheBundle\EventListener\CacheClearSubscriber::class,
                'tags'      => ['kernel.cache_clearer'],
                'arguments' => [
                    'autoborna.cache.provider',
                    'monolog.logger.autoborna',
                ],
            ],
        ],
        'forms'     => [],
        'helpers'   => [],
        'menus'     => [],
        'other'     => [
            'autoborna.cache.provider'           => [
                'class'     => \Autoborna\CacheBundle\Cache\CacheProvider::class,
                'arguments' => [
                    'autoborna.helper.core_parameters',
                    'service_container',
                ],
            ],
            'autoborna.cache.adapter.filesystem' => [
                'class'     => \Autoborna\CacheBundle\Cache\Adapter\FilesystemTagAwareAdapter::class,
                'arguments' => [
                    '%autoborna.cache_prefix%',
                    '%autoborna.cache_lifetime%',
                ],
                'tag'       => 'autoborna.cache.adapter',
            ],
            'autoborna.cache.adapter.memcached'  => [
                'class'     => \Autoborna\CacheBundle\Cache\Adapter\MemcachedTagAwareAdapter::class,
                'arguments' => [
                    '%autoborna.cache_adapter_memcached%',
                    '%autoborna.cache_prefix%',
                    '%autoborna.cache_lifetime%',
                ],
                'tag'       => 'autoborna.cache.adapter',
            ],
            'autoborna.cache.adapter.redis'      => [
                'class'     => \Autoborna\CacheBundle\Cache\Adapter\RedisTagAwareAdapter::class,
                'arguments' => [
                    '%autoborna.cache_adapter_redis%',
                    '%autoborna.cache_prefix%',
                    '%autoborna.cache_lifetime%',
                ],
                'tag'       => 'autoborna.cache.adapter',
            ],
        ],
        'models'    => [],
        'validator' => [],
    ],

    'parameters' => [
        'cache_adapter'           => 'autoborna.cache.adapter.filesystem',
        'cache_prefix'            => '',
        'cache_lifetime'          => 86400,
        'cache_adapter_memcached' => [
            'servers' => ['memcached://localhost'],
            'options' => [
                'compression'          => true,
                'libketama_compatible' => true,
                'serializer'           => 'igbinary',
            ],
        ],
        'cache_adapter_redis'     => [
            'dsn'     => 'redis://localhost',
            'options' => [
                'lazy'           => false,
                'persistent'     => 0,
                'persistent_id'  => null,
                'timeout'        => 30,
                'read_timeout'   => 0,
                'retry_interval' => 0,
            ],
        ],
    ],
];
