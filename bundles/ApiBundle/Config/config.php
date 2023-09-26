<?php

return [
    'routes' => [
        'public' => [
            // OAuth2
            'fos_oauth_server_token' => [
                'path'       => '/oauth/v2/token',
                'controller' => 'fos_oauth_server.controller.token:tokenAction',
                'method'     => 'GET|POST',
            ],
            'fos_oauth_server_authorize' => [
                'path'       => '/oauth/v2/authorize',
                'controller' => 'AutobornaApiBundle:oAuth2/Authorize:authorize',
                'method'     => 'GET|POST',
            ],
            'autoborna_oauth2_server_auth_login' => [
                'path'       => '/oauth/v2/authorize_login',
                'controller' => 'AutobornaApiBundle:oAuth2/Security:login',
                'method'     => 'GET|POST',
            ],
            'autoborna_oauth2_server_auth_login_check' => [
                'path'       => '/oauth/v2/authorize_login_check',
                'controller' => 'AutobornaApiBundle:oAuth2/Security:loginCheck',
                'method'     => 'GET|POST',
            ],
        ],
        'main' => [
            // Clients
            'autoborna_client_index' => [
                'path'       => '/credentials/{page}',
                'controller' => 'AutobornaApiBundle:Client:index',
            ],
            'autoborna_client_action' => [
                'path'       => '/credentials/{objectAction}/{objectId}',
                'controller' => 'AutobornaApiBundle:Client:execute',
            ],
        ],
    ],

    'menu' => [
        'admin' => [
            'items' => [
                'autoborna.api.client.menu.index' => [
                    'route'     => 'autoborna_client_index',
                    'iconClass' => 'fa-puzzle-piece',
                    'access'    => 'api:clients:view',
                    'checks'    => [
                        'parameters' => [
                            'api_enabled' => true,
                        ],
                    ],
                ],
            ],
        ],
    ],

    'services' => [
        'controllers' => [
            'autoborna.api.oauth2.authorize_controller' => [
                'class'     => \Autoborna\ApiBundle\Controller\oAuth2\AuthorizeController::class,
                'arguments' => [
                    'request_stack',
                    'fos_oauth_server.authorize.form',
                    'fos_oauth_server.authorize.form.handler.default',
                    'fos_oauth_server.server',
                    'templating',
                    'security.token_storage',
                    'router',
                    'fos_oauth_server.client_manager.default',
                    'event_dispatcher',
                    'session',
                ],
            ],
        ],
        'events' => [
            'autoborna.api.subscriber' => [
                'class'     => \Autoborna\ApiBundle\EventListener\ApiSubscriber::class,
                'arguments' => [
                    'autoborna.helper.core_parameters',
                    'translator',
                ],
            ],
            'autoborna.api.client.subscriber' => [
                'class'     => \Autoborna\ApiBundle\EventListener\ClientSubscriber::class,
                'arguments' => [
                    'autoborna.helper.ip_lookup',
                    'autoborna.core.model.auditlog',
                ],
            ],
            'autoborna.api.configbundle.subscriber' => [
                'class' => \Autoborna\ApiBundle\EventListener\ConfigSubscriber::class,
            ],
            'autoborna.api.search.subscriber' => [
                'class'     => \Autoborna\ApiBundle\EventListener\SearchSubscriber::class,
                'arguments' => [
                    'autoborna.api.model.client',
                    'autoborna.security',
                    'autoborna.helper.templating',
                ],
            ],
            'autoborna.api.rate_limit_generate_key.subscriber' => [
              'class'     => \Autoborna\ApiBundle\EventListener\RateLimitGenerateKeySubscriber::class,
              'arguments' => [
                'autoborna.helper.core_parameters',
              ],
            ],
        ],
        'forms' => [
            'autoborna.form.type.apiclients' => [
                'class'     => \Autoborna\ApiBundle\Form\Type\ClientType::class,
                'arguments' => [
                    'request_stack',
                    'translator',
                    'validator',
                    'session',
                    'router',
                ],
            ],
            'autoborna.form.type.apiconfig' => [
                'class' => 'Autoborna\ApiBundle\Form\Type\ConfigType',
            ],
        ],
        'helpers' => [
            'autoborna.api.helper.entity_result' => [
                'class' => \Autoborna\ApiBundle\Helper\EntityResultHelper::class,
            ],
        ],
        'other' => [
            'autoborna.api.oauth.event_listener' => [
                'class'     => 'Autoborna\ApiBundle\EventListener\OAuthEventListener',
                'arguments' => [
                    'doctrine.orm.entity_manager',
                    'autoborna.security',
                    'translator',
                ],
                'tags' => [
                    'kernel.event_listener',
                    'kernel.event_listener',
                ],
                'tagArguments' => [
                    [
                        'event'  => 'fos_oauth_server.pre_authorization_process',
                        'method' => 'onPreAuthorizationProcess',
                    ],
                    [
                        'event'  => 'fos_oauth_server.post_authorization_process',
                        'method' => 'onPostAuthorizationProcess',
                    ],
                ],
            ],
            'fos_oauth_server.security.authentication.listener.class' => 'Autoborna\ApiBundle\Security\OAuth2\Firewall\OAuthListener',
            'jms_serializer.metadata.annotation_driver'               => 'Autoborna\ApiBundle\Serializer\Driver\AnnotationDriver',
            'jms_serializer.metadata.api_metadata_driver'             => [
                'class' => 'Autoborna\ApiBundle\Serializer\Driver\ApiMetadataDriver',
            ],
            'autoborna.validator.oauthcallback' => [
                'class' => 'Autoborna\ApiBundle\Form\Validator\Constraints\OAuthCallbackValidator',
                'tag'   => 'validator.constraint_validator',
            ],
        ],
        'models' => [
            'autoborna.api.model.client' => [
                'class'     => 'Autoborna\ApiBundle\Model\ClientModel',
                'arguments' => [
                    'request_stack',
                ],
            ],
        ],
    ],

    'parameters' => [
        'api_enabled'                       => false,
        'api_enable_basic_auth'             => false,
        'api_oauth2_access_token_lifetime'  => 60,
        'api_oauth2_refresh_token_lifetime' => 14,
        'api_batch_max_limit'               => 200,
        'api_rate_limiter_limit'            => 0,
        'api_rate_limiter_cache'            => [
            'adapter' => 'cache.adapter.filesystem',
        ],
    ],
];
