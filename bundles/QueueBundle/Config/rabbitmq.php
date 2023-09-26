<?php

$container->loadFromExtension(
    'old_sound_rabbit_mq',
    [
        'connections' => [
            'default' => [
                'host'               => '%autoborna.rabbitmq_host%',
                'port'               => '%autoborna.rabbitmq_port%',
                'user'               => '%autoborna.rabbitmq_user%',
                'password'           => '%autoborna.rabbitmq_password%',
                'vhost'              => '%autoborna.rabbitmq_vhost%',
                'lazy'               => true,
                'connection_timeout' => 3,
                'heartbeat'          => 2,
                'read_write_timeout' => 4,
            ],
        ],
        'producers' => [
            'autoborna' => [
                'class'            => 'Autoborna\QueueBundle\Helper\RabbitMqProducer',
                'connection'       => 'default',
                'exchange_options' => [
                    'name'    => 'autoborna',
                    'type'    => 'direct',
                    'durable' => true,
                ],
                'queue_options' => [
                    'name'        => 'email_hit',
                    'auto_delete' => false,
                    'durable'     => true,
                ],
            ],
        ],
        'consumers' => [
            'autoborna' => [
                'connection'       => 'default',
                'exchange_options' => [
                    'name'    => 'autoborna',
                    'type'    => 'direct',
                    'durable' => true,
                ],
                'queue_options' => [
                    'name'        => 'email_hit',
                    'auto_delete' => false,
                    'durable'     => true,
                ],
                'callback'               => 'autoborna.queue.helper.rabbitmq_consumer',
                'idle_timeout'           => '%autoborna.rabbitmq_idle_timeout%',
                'idle_timeout_exit_code' => '%autoborna.rabbitmq_idle_timeout_exit_code%',
            ],
        ],
    ]
);
