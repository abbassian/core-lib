<?php

use Doctrine\Bundle\FixturesBundle\DependencyInjection\CompilerPass\FixturesCompilerPass;
use AutobornaPlugin\AutobornaCrmBundle\Tests\Pipedrive\Mock\Client;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Dotenv\Dotenv;

/** @var \Symfony\Component\DependencyInjection\ContainerBuilder $container */

/*
 * @copyright   2014 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
$loader->import('config.php');

// Load environment variables from .env.test file
$env     = new Dotenv();
$root    = __DIR__.'/../../';
$envFile = file_exists($root.'.env') ? $root.'.env' : $root.'.env.dist';

$env->load($envFile);

// Define some constants from .env
defined('MAUTIC_TABLE_PREFIX') || define('MAUTIC_TABLE_PREFIX', getenv('MAUTIC_DB_PREFIX') ?: '');
defined('MAUTIC_ENV') || define('MAUTIC_ENV', getenv('MAUTIC_ENV') ?: 'test');

$container->loadFromExtension('framework', [
    'test'    => true,
    'session' => [
        'storage_id' => 'session.storage.filesystem',
    ],
    'profiler' => [
        'collect' => false,
    ],
    'translator' => [
        'enabled' => true,
    ],
    'csrf_protection' => [
        'enabled' => true,
    ],
]);

$container->setParameter('autoborna.famework.csrf_protection', true);

$container
    ->register('autoborna_integration.pipedrive.guzzle.client', Client::class)
    ->setPublic(true);

$container->loadFromExtension('web_profiler', [
    'toolbar'             => false,
    'intercept_redirects' => false,
]);

$container->loadFromExtension('swiftmailer', [
    'disable_delivery' => true,
]);

$container->loadFromExtension('doctrine', [
    'dbal' => [
        'default_connection' => 'default',
        'connections'        => [
            'default' => [
                'driver'   => 'pdo_mysql',
                'host'     => getenv('DB_HOST') ?: '%autoborna.db_host%',
                'port'     => getenv('DB_PORT') ?: '%autoborna.db_port%',
                'dbname'   => getenv('DB_NAME') ?: '%autoborna.db_name%',
                'user'     => getenv('DB_USER') ?: '%autoborna.db_user%',
                'password' => getenv('DB_PASSWD') ?: '%autoborna.db_password%',
                'charset'  => 'utf8mb4',
                // Prevent Doctrine from crapping out with "unsupported type" errors due to it examining all tables in the database and not just Autoborna's
                'mapping_types' => [
                    'enum'  => 'string',
                    'point' => 'string',
                    'bit'   => 'string',
                ],
            ],
        ],
    ],
]);

// Ensure the autoborna.db_table_prefix is set to our phpunit configuration.
$container->setParameter('autoborna.db_table_prefix', MAUTIC_TABLE_PREFIX);

$container->loadFromExtension('monolog', [
    'channels' => [
        'autoborna',
    ],
    'handlers' => [
        'main' => [
            'formatter' => 'autoborna.monolog.fulltrace.formatter',
            'type'      => 'rotating_file',
            'path'      => '%kernel.logs_dir%/%kernel.environment%.php',
            'level'     => getenv('MAUTIC_DEBUG_LEVEL') ?: 'error',
            'channels'  => [
                '!autoborna',
            ],
            'max_files' => 7,
        ],
        'console' => [
            'type'   => 'console',
            'bubble' => false,
        ],
        'autoborna' => [
            'formatter' => 'autoborna.monolog.fulltrace.formatter',
            'type'      => 'rotating_file',
            'path'      => '%kernel.logs_dir%/autoborna_%kernel.environment%.php',
            'level'     => getenv('MAUTIC_DEBUG_LEVEL') ?: 'error',
            'channels'  => [
                'autoborna',
            ],
            'max_files' => 7,
        ],
    ],
]);

$container->loadFromExtension('liip_test_fixtures', [
    'cache_db' => [
        'sqlite' => 'liip_functional_test.services_database_backup.sqlite',
    ],
    'keep_database_and_schema' => true,
]);

// Enable api by default
$container->setParameter('autoborna.api_enabled', true);
$container->setParameter('autoborna.api_enable_basic_auth', true);

$loader->import('security_test.php');

// Allow overriding config without a requiring a full bundle or hacks
if (file_exists(__DIR__.'/config_override.php')) {
    $loader->import('config_override.php');
}

// Add required parameters
$container->setParameter('autoborna.secret_key', '68c7e75470c02cba06dd543431411e0de94e04fdf2b3a2eac05957060edb66d0');
$container->setParameter('autoborna.security.disableUpdates', true);
$container->setParameter('autoborna.rss_notification_url', null);
$container->setParameter('autoborna.batch_sleep_time', 0);

// Turn off creating of indexes in lead field fixtures
$container->register('autoborna.install.fixture.lead_field', \Autoborna\InstallBundle\InstallFixtures\ORM\LeadFieldData::class)
    ->addArgument(false)
    ->addTag(FixturesCompilerPass::FIXTURE_TAG)
    ->setPublic(true);
$container->register('autoborna.lead.fixture.contact_field', \Autoborna\LeadBundle\DataFixtures\ORM\LoadLeadFieldData::class)
    ->addArgument(false)
    ->addTag(FixturesCompilerPass::FIXTURE_TAG)
    ->setPublic(true);

// Use static namespace for token manager
$container->register('security.csrf.token_manager', \Symfony\Component\Security\Csrf\CsrfTokenManager::class)
    ->addArgument(new Reference('security.csrf.token_generator'))
    ->addArgument(new Reference('security.csrf.token_storage'))
    ->addArgument('test')
    ->setPublic(true);

// HTTP client mock handler providing response queue
$container->register('autoborna.http.client.mock_handler', \GuzzleHttp\Handler\MockHandler::class)
    ->setClass('\GuzzleHttp\Handler\MockHandler');

// Stub Guzzle HTTP client to prevent accidental request to third parties
$container->register('autoborna.http.client', \GuzzleHttp\Client::class)
    ->setPublic(true)
    ->setFactory('\Autoborna\CoreBundle\Test\Guzzle\ClientFactory::stub')
    ->addArgument(new Reference('autoborna.http.client.mock_handler'));
