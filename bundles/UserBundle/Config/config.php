<?php

return [
    'menu' => [
        'admin' => [
            'autoborna.user.users' => [
                'access'    => 'user:users:view',
                'route'     => 'autoborna_user_index',
                'iconClass' => 'fa-users',
            ],
            'autoborna.user.roles' => [
                'access'    => 'user:roles:view',
                'route'     => 'autoborna_role_index',
                'iconClass' => 'fa-lock',
            ],
        ],
    ],

    'routes' => [
        'main' => [
            'login' => [
                'path'       => '/login',
                'controller' => 'AutobornaUserBundle:Security:login',
            ],
            'autoborna_user_logincheck' => [
                'path'       => '/login_check',
                'controller' => 'AutobornaUserBundle:Security:loginCheck',
            ],
            'autoborna_user_logout' => [
                'path' => '/logout',
            ],
            'autoborna_sso_login' => [
                'path'       => '/sso_login/{integration}',
                'controller' => 'AutobornaUserBundle:Security:ssoLogin',
            ],
            'autoborna_sso_login_check' => [
                'path'       => '/sso_login_check/{integration}',
                'controller' => 'AutobornaUserBundle:Security:ssoLoginCheck',
            ],
            'lightsaml_sp.login' => [
                'path'       => '/saml/login',
                'controller' => 'LightSamlSpBundle:Default:login',
            ],
            'lightsaml_sp.login_check' => [
                'path' => '/saml/login_check',
            ],
            'autoborna_user_index' => [
                'path'       => '/users/{page}',
                'controller' => 'AutobornaUserBundle:User:index',
            ],
            'autoborna_user_action' => [
                'path'       => '/users/{objectAction}/{objectId}',
                'controller' => 'AutobornaUserBundle:User:execute',
            ],
            'autoborna_role_index' => [
                'path'       => '/roles/{page}',
                'controller' => 'AutobornaUserBundle:Role:index',
            ],
            'autoborna_role_action' => [
                'path'       => '/roles/{objectAction}/{objectId}',
                'controller' => 'AutobornaUserBundle:Role:execute',
            ],
            'autoborna_user_account' => [
                'path'       => '/account',
                'controller' => 'AutobornaUserBundle:Profile:index',
            ],
        ],

        'api' => [
            'autoborna_api_usersstandard' => [
                'standard_entity' => true,
                'name'            => 'users',
                'path'            => '/users',
                'controller'      => 'AutobornaUserBundle:Api\UserApi',
            ],
            'autoborna_api_getself' => [
                'path'       => '/users/self',
                'controller' => 'AutobornaUserBundle:Api\UserApi:getSelf',
            ],
            'autoborna_api_checkpermission' => [
                'path'       => '/users/{id}/permissioncheck',
                'controller' => 'AutobornaUserBundle:Api\UserApi:isGranted',
                'method'     => 'POST',
            ],
            'autoborna_api_getuserroles' => [
                'path'       => '/users/list/roles',
                'controller' => 'AutobornaUserBundle:Api\UserApi:getRoles',
            ],
            'autoborna_api_rolesstandard' => [
                'standard_entity' => true,
                'name'            => 'roles',
                'path'            => '/roles',
                'controller'      => 'AutobornaUserBundle:Api\RoleApi',
            ],
        ],
        'public' => [
            'autoborna_user_passwordreset' => [
                'path'       => '/passwordreset',
                'controller' => 'AutobornaUserBundle:Public:passwordReset',
            ],
            'autoborna_user_passwordresetconfirm' => [
                'path'       => '/passwordresetconfirm',
                'controller' => 'AutobornaUserBundle:Public:passwordResetConfirm',
            ],
            'lightsaml_sp.metadata' => [
                'path'       => '/saml/metadata.xml',
                'controller' => 'LightSamlSpBundle:Default:metadata',
            ],
            'lightsaml_sp.discovery' => [
                'path'       => '/saml/discovery',
                'controller' => 'LightSamlSpBundle:Default:discovery',
            ],
        ],
    ],

    'services' => [
        'events' => [
            'autoborna.user.subscriber' => [
                'class'     => \Autoborna\UserBundle\EventListener\UserSubscriber::class,
                'arguments' => [
                    'autoborna.helper.ip_lookup',
                    'autoborna.core.model.auditlog',
                ],
            ],
            'autoborna.user.search.subscriber' => [
                'class'     => \Autoborna\UserBundle\EventListener\SearchSubscriber::class,
                'arguments' => [
                    'autoborna.user.model.user',
                    'autoborna.user.model.role',
                    'autoborna.security',
                    'autoborna.helper.templating',
                ],
            ],
            'autoborna.user.config.subscriber' => [
                'class' => \Autoborna\UserBundle\EventListener\ConfigSubscriber::class,
            ],
            'autoborna.user.route.subscriber' => [
                'class'     => \Autoborna\UserBundle\EventListener\SAMLSubscriber::class,
                'arguments' => [
                    'autoborna.helper.core_parameters',
                    'router',
                ],
            ],
            'autoborna.user.security_subscriber' => [
                'class'     => \Autoborna\UserBundle\EventListener\SecuritySubscriber::class,
                'arguments' => [
                    'autoborna.helper.ip_lookup',
                    'autoborna.core.model.auditlog',
                ],
            ],
        ],
        'forms' => [
            'autoborna.form.type.user' => [
                'class'     => \Autoborna\UserBundle\Form\Type\UserType::class,
                'arguments' => [
                    'translator',
                    'autoborna.user.model.user',
                    'autoborna.helper.language',
                ],
            ],
            'autoborna.form.type.role' => [
                'class' => \Autoborna\UserBundle\Form\Type\RoleType::class,
            ],
            'autoborna.form.type.permissions' => [
                'class' => \Autoborna\UserBundle\Form\Type\PermissionsType::class,
            ],
            'autoborna.form.type.permissionlist' => [
                'class' => \Autoborna\UserBundle\Form\Type\PermissionListType::class,
            ],
            'autoborna.form.type.passwordreset' => [
                'class' => \Autoborna\UserBundle\Form\Type\PasswordResetType::class,
            ],
            'autoborna.form.type.passwordresetconfirm' => [
                'class' => \Autoborna\UserBundle\Form\Type\PasswordResetConfirmType::class,
            ],
            'autoborna.form.type.user_list' => [
                'class'     => \Autoborna\UserBundle\Form\Type\UserListType::class,
                'arguments' => 'autoborna.user.model.user',
            ],
            'autoborna.form.type.role_list' => [
                'class'     => \Autoborna\UserBundle\Form\Type\RoleListType::class,
                'arguments' => 'autoborna.user.model.role',
            ],
            'autoborna.form.type.userconfig' => [
                'class'     => \Autoborna\UserBundle\Form\Type\ConfigType::class,
                'arguments' => [
                    'autoborna.helper.core_parameters',
                    'translator',
                ],
            ],
        ],
        'other' => [
            // Authentication
            'autoborna.user.manager' => [
                'class'     => 'Doctrine\ORM\EntityManager',
                'arguments' => 'Autoborna\UserBundle\Entity\User',
                'factory'   => ['@doctrine', 'getManagerForClass'],
            ],
            'autoborna.user.repository' => [
                'class'     => 'Autoborna\UserBundle\Entity\UserRepository',
                'arguments' => 'Autoborna\UserBundle\Entity\User',
                'factory'   => ['@autoborna.user.manager', 'getRepository'],
            ],
            'autoborna.user.token.repository' => [
                'class'     => 'Autoborna\UserBundle\Entity\UserTokenRepository',
                'arguments' => 'Autoborna\UserBundle\Entity\UserToken',
                'factory'   => ['@doctrine', 'getRepository'],
            ],
            'autoborna.permission.manager' => [
                'class'     => 'Doctrine\ORM\EntityManager',
                'arguments' => 'Autoborna\UserBundle\Entity\Permission',
                'factory'   => ['@doctrine', 'getManagerForClass'],
            ],
            'autoborna.permission.repository' => [
                'class'     => 'Autoborna\UserBundle\Entity\PermissionRepository',
                'arguments' => 'Autoborna\UserBundle\Entity\Permission',
                'factory'   => ['@autoborna.permission.manager', 'getRepository'],
            ],
            'autoborna.user.form_authenticator' => [
                'class'     => 'Autoborna\UserBundle\Security\Authenticator\FormAuthenticator',
                'arguments' => [
                    'autoborna.helper.integration',
                    'security.password_encoder',
                    'event_dispatcher',
                    'request_stack',
                ],
            ],
            'autoborna.user.preauth_authenticator' => [
                'class'     => 'Autoborna\UserBundle\Security\Authenticator\PreAuthAuthenticator',
                'arguments' => [
                    'autoborna.helper.integration',
                    'event_dispatcher',
                    'request_stack',
                    '', // providerKey
                    '', // User provider
                ],
                'public' => false,
            ],
            'autoborna.user.provider' => [
                'class'     => 'Autoborna\UserBundle\Security\Provider\UserProvider',
                'arguments' => [
                    'autoborna.user.repository',
                    'autoborna.permission.repository',
                    'session',
                    'event_dispatcher',
                    'security.password_encoder',
                ],
            ],
            'autoborna.security.authentication_listener' => [
                'class'     => 'Autoborna\UserBundle\Security\Firewall\AuthenticationListener',
                'arguments' => [
                    'autoborna.security.authentication_handler',
                    'security.token_storage',
                    'security.authentication.manager',
                    'monolog.logger',
                    'event_dispatcher',
                    '', // providerKey
                    'autoborna.permission.repository',
                    'doctrine.orm.default_entity_manager',
                ],
                'public' => false,
            ],
            'autoborna.security.authentication_handler' => [
                'class'     => \Autoborna\UserBundle\Security\Authentication\AuthenticationHandler::class,
                'arguments' => [
                    'router',
                ],
            ],
            'autoborna.security.logout_handler' => [
                'class'     => 'Autoborna\UserBundle\Security\Authentication\LogoutHandler',
                'arguments' => [
                    'autoborna.user.model.user',
                    'event_dispatcher',
                    'autoborna.helper.user',
                ],
            ],

            // SAML
            'autoborna.security.saml.credential_store' => [
                'class'     => \Autoborna\UserBundle\Security\SAML\Store\CredentialsStore::class,
                'arguments' => [
                    'autoborna.helper.core_parameters',
                    '%autoborna.saml_idp_entity_id%',
                ],
                'tag'       => 'lightsaml.own_credential_store',
            ],

            'autoborna.security.saml.trust_store' => [
                'class'     => \Autoborna\UserBundle\Security\SAML\Store\TrustOptionsStore::class,
                'arguments' => [
                    'autoborna.helper.core_parameters',
                    '%autoborna.saml_idp_entity_id%',
                ],
                'tag'       => 'lightsaml.trust_options_store',
            ],

            'autoborna.security.saml.entity_descriptor_store' => [
                'class'     => \Autoborna\UserBundle\Security\SAML\Store\EntityDescriptorStore::class,
                'arguments' => [
                    'autoborna.helper.core_parameters',
                ],
                'tag'       => 'lightsaml.idp_entity_store',
            ],

            'autoborna.security.saml.id_store' => [
                'class'     => \Autoborna\UserBundle\Security\SAML\Store\IdStore::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                    'lightsaml.system.time_provider',
                ],
            ],

            'autoborna.security.saml.username_mapper' => [
                'class'     => \Autoborna\UserBundle\Security\SAML\User\UserMapper::class,
                'arguments' => [
                    [
                        'email'     => '%autoborna.saml_idp_email_attribute%',
                        'username'  => '%autoborna.saml_idp_username_attribute%',
                        'firstname' => '%autoborna.saml_idp_firstname_attribute%',
                        'lastname'  => '%autoborna.saml_idp_lastname_attribute%',
                    ],
                ],
            ],

            'autoborna.security.saml.user_creator' => [
                'class'     => \Autoborna\UserBundle\Security\SAML\User\UserCreator::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                    'autoborna.security.saml.username_mapper',
                    'autoborna.user.model.user',
                    'security.password_encoder',
                    '%autoborna.saml_idp_default_role%',
                ],
            ],
        ],
        'models' => [
            'autoborna.user.model.role' => [
                'class' => 'Autoborna\UserBundle\Model\RoleModel',
            ],
            'autoborna.user.model.user' => [
                'class'     => 'Autoborna\UserBundle\Model\UserModel',
                'arguments' => [
                    'autoborna.helper.mailer',
                    'autoborna.user.model.user_token_service',
                ],
            ],
            'autoborna.user.model.user_token_service' => [
                'class'     => \Autoborna\UserBundle\Model\UserToken\UserTokenService::class,
                'arguments' => [
                    'autoborna.helper.random',
                    'autoborna.user.repository.user_token',
                ],
            ],
        ],
        'repositories' => [
            'autoborna.user.repository.user_token' => [
                'class'     => \Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Autoborna\UserBundle\Entity\UserToken::class,
                ],
            ],
        ],
        'fixtures' => [
            'autoborna.user.fixture.role' => [
                'class'     => \Autoborna\UserBundle\DataFixtures\ORM\LoadRoleData::class,
                'tag'       => \Doctrine\Bundle\FixturesBundle\DependencyInjection\CompilerPass\FixturesCompilerPass::FIXTURE_TAG,
                'arguments' => ['autoborna.user.model.role'],
            ],
            'autoborna.user.fixture.user' => [
                'class'     => \Autoborna\UserBundle\DataFixtures\ORM\LoadUserData::class,
                'tag'       => \Doctrine\Bundle\FixturesBundle\DependencyInjection\CompilerPass\FixturesCompilerPass::FIXTURE_TAG,
                'arguments' => ['security.password_encoder'],
            ],
        ],
    ],
    'parameters' => [
        'saml_idp_metadata'            => '',
        'saml_idp_entity_id'           => '',
        'saml_idp_own_certificate'     => '',
        'saml_idp_own_private_key'     => '',
        'saml_idp_own_password'        => '',
        'saml_idp_email_attribute'     => '',
        'saml_idp_username_attribute'  => '',
        'saml_idp_firstname_attribute' => '',
        'saml_idp_lastname_attribute'  => '',
        'saml_idp_default_role'        => '',
    ],
];
