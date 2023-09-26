<?php

$firewalls = [
    'install' => [
        'pattern'   => '^/installer',
        'anonymous' => true,
        'context'   => 'autoborna',
        'security'  => false,
    ],
    'dev' => [
        'pattern'   => '^/(_(profiler|wdt)|css|images|js)/',
        'security'  => true,
        'anonymous' => true,
    ],
    'login' => [
        'pattern'   => '^/s/login$',
        'anonymous' => true,
        'context'   => 'autoborna',
    ],
    'sso_login' => [
        'pattern'            => '^/s/sso_login',
        'anonymous'          => true,
        'autoborna_plugin_auth' => true,
        'context'            => 'autoborna',
    ],
    'saml_login' => [
        'pattern'   => '^/s/saml/login$',
        'anonymous' => true,
        'context'   => 'autoborna',
    ],
    'saml_discovery' => [
        'pattern'   => '^/saml/discovery$',
        'anonymous' => true,
        'context'   => 'autoborna',
    ],
    'oauth2_token' => [
        'pattern'  => '^/oauth/v2/token',
        'security' => false,
    ],
    'oauth2_area' => [
        'pattern'    => '^/oauth/v2/authorize',
        'form_login' => [
            'provider'   => 'user_provider',
            'check_path' => '/oauth/v2/authorize_login_check',
            'login_path' => '/oauth/v2/authorize_login',
        ],
        'anonymous' => true,
    ],
    'api' => [
        'pattern'            => '^/api',
        'fos_oauth'          => true,
        'autoborna_plugin_auth' => true,
        'stateless'          => true,
        'http_basic'         => true,
    ],
    'main' => [
        'pattern'       => '^/s/',
        'light_saml_sp' => [
            'provider'        => 'user_provider',
            'success_handler' => 'autoborna.security.authentication_handler',
            'failure_handler' => 'autoborna.security.authentication_handler',
            'user_creator'    => 'autoborna.security.saml.user_creator',
            'username_mapper' => 'autoborna.security.saml.username_mapper',

            // Environment variables will overwrite these with the standard login URLs if SAML is disabled
            'login_path'      => '%env(MAUTIC_SAML_LOGIN_PATH)%', // '/s/saml/login',,
            'check_path'      => '%env(MAUTIC_SAML_LOGIN_CHECK_PATH)%', // '/s/saml/login_check',
        ],
        'simple_form' => [
            'authenticator'        => 'autoborna.user.form_authenticator',
            'csrf_token_generator' => 'security.csrf.token_manager',
            'success_handler'      => 'autoborna.security.authentication_handler',
            'failure_handler'      => 'autoborna.security.authentication_handler',
            'login_path'           => '/s/login',
            'check_path'           => '/s/login_check',
        ],
        'logout' => [
            'handlers' => [
                'autoborna.security.logout_handler',
            ],
            'path'   => '/s/logout',
            'target' => '/s/login',
        ],
        'remember_me' => [
            'secret'   => '%autoborna.rememberme_key%',
            'lifetime' => '%autoborna.rememberme_lifetime%',
            'path'     => '%autoborna.rememberme_path%',
            'domain'   => '%autoborna.rememberme_domain%',
        ],
        'fos_oauth'     => true,
        'context'       => 'autoborna',
    ],
    'public' => [
        'pattern'   => '^/',
        'anonymous' => true,
        'context'   => 'autoborna',
    ],
];

if (!$container->getParameter('autoborna.famework.csrf_protection')) {
    unset($firewalls['main']['simple_form']['csrf_token_generator']);
}

$container->loadFromExtension(
    'security',
    [
        'providers' => [
            'user_provider' => [
                'id' => 'autoborna.user.provider',
            ],
        ],
        'encoders' => [
            'Symfony\Component\Security\Core\User\User' => [
                'algorithm'  => 'bcrypt',
                'iterations' => 12,
            ],
            'Autoborna\UserBundle\Entity\User' => [
                'algorithm'  => 'bcrypt',
                'iterations' => 12,
            ],
        ],
        'role_hierarchy' => [
            'ROLE_ADMIN' => 'ROLE_USER',
        ],
        'firewalls'      => $firewalls,
        'access_control' => [
            ['path' => '^/api', 'roles' => 'IS_AUTHENTICATED_FULLY'],
            ['path' => '^/efconnect', 'roles' => 'IS_AUTHENTICATED_FULLY'],
            ['path' => '^/elfinder', 'roles' => 'IS_AUTHENTICATED_FULLY'],
        ],
    ]
);

$container->setParameter('autoborna.saml_idp_entity_id', '%env(MAUTIC_SAML_ENTITY_ID)%');
$container->loadFromExtension(
    'light_saml_symfony_bridge',
    [
        'own' => [
            'entity_id' => '%autoborna.saml_idp_entity_id%',
        ],
        'store' => [
            'id_state' => 'autoborna.security.saml.id_store',
        ],
    ]
);

$this->import('security_api.php');

// List config keys we do not want the user to change via the config UI
$restrictedConfigFields = [
    'db_driver',
    'db_host',
    'db_table_prefix',
    'db_name',
    'db_user',
    'db_password',
    'db_path',
    'db_port',
    'secret_key',
];

// List config keys that are dev mode only
if ('prod' == $container->getParameter('kernel.environment')) {
    $restrictedConfigFields = array_merge($restrictedConfigFields, ['transifex_username', 'transifex_password']);
}

$container->setParameter('autoborna.security.restrictedConfigFields', $restrictedConfigFields);
$container->setParameter('autoborna.security.restrictedConfigFields.displayMode', \Autoborna\ConfigBundle\Form\Helper\RestrictionHelper::MODE_REMOVE);

/*
 * Optional security parameters
 * autoborna.security.disableUpdates = disables remote checks for updates
 * autoborna.security.restrictedConfigFields.displayMode = accepts either remove or mask; mask will disable the input with a "Set by system" message
 */
$container->setParameter('autoborna.security.disableUpdates', false);
