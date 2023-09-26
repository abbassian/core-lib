<?php

$loader->import('config.php');

if (file_exists(__DIR__.'/security_local.php')) {
    $loader->import('security_local.php');
} else {
    $loader->import('security.php');
}

/*
$container->loadFromExtension("framework", array(
    "validation" => array(
        "cache" => "apc"
    )
));

$container->loadFromExtension("doctrine", array(
    "orm" => array(
        "metadata_cache_driver" => "apc",
        "result_cache_driver"   => "apc",
        "query_cache_driver"    => "apc"
    )
));
*/

$container->loadFromExtension('monolog', [
    'channels' => [
        'autoborna',
    ],
    'handlers' => [
        'main' => [
            'type'         => 'fingers_crossed',
            'buffer_size'  => '200',
            'action_level' => 'error',
            'handler'      => 'nested',
            'channels'     => [
                '!autoborna',
            ],
        ],
        'nested' => [
            'type'      => 'rotating_file',
            'path'      => '%kernel.logs_dir%/%kernel.environment%.php',
            'level'     => 'error',
            'max_files' => 7,
        ],
        'autoborna' => [
            'type'      => 'service',
            'id'        => 'autoborna.monolog.handler',
            'channels'  => [
                'autoborna',
            ],
        ],
    ],
]);

//Twig Configuration
$container->loadFromExtension('twig', [
    'cache'            => '%env(resolve:MAUTIC_TWIG_CACHE_DIR)%',
    'auto_reload'      => true,
    'strict_variables' => true,
    'paths'            => [
        '%kernel.root_dir%/bundles' => 'bundles',
    ],
    'form_themes' => [
        // Can be found at bundles/CoreBundle/Resources/views/autoborna_form_layout.html.twig
        '@AutobornaCore/FormTheme/autoborna_form_layout.html.twig',
    ],
]);

// Allow overriding config without a requiring a full bundle or hacks
if (file_exists(__DIR__.'/config_override.php')) {
    $loader->import('config_override.php');
}

// Allow local settings without committing to git such as swift mailer delivery address overrides
if (file_exists(__DIR__.'/config_local.php')) {
    $loader->import('config_local.php');
}
