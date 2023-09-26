<?php

return [
    'routes' => [
        'main' => [
            'autoborna_category_batch_contact_set' => [
                'path'       => '/categories/batch/contact/set',
                'controller' => 'AutobornaCategoryBundle:BatchContact:exec',
            ],
            'autoborna_category_batch_contact_view' => [
                'path'       => '/categories/batch/contact/view',
                'controller' => 'AutobornaCategoryBundle:BatchContact:index',
            ],
            'autoborna_category_index' => [
                'path'       => '/categories/{bundle}/{page}',
                'controller' => 'AutobornaCategoryBundle:Category:index',
                'defaults'   => [
                    'bundle' => 'category',
                ],
            ],
            'autoborna_category_action' => [
                'path'       => '/categories/{bundle}/{objectAction}/{objectId}',
                'controller' => 'AutobornaCategoryBundle:Category:executeCategory',
                'defaults'   => [
                    'bundle' => 'category',
                ],
            ],
        ],
        'api' => [
            'autoborna_api_categoriesstandard' => [
                'standard_entity' => true,
                'name'            => 'categories',
                'path'            => '/categories',
                'controller'      => 'AutobornaCategoryBundle:Api\CategoryApi',
            ],
        ],
    ],

    'menu' => [
        'admin' => [
            'autoborna.category.menu.index' => [
                'route'     => 'autoborna_category_index',
                'access'    => 'category:categories:view',
                'iconClass' => 'fa-folder',
                'id'        => 'autoborna_category_index',
            ],
        ],
    ],

    'services' => [
        'events' => [
            'autoborna.category.subscriber' => [
                'class'     => \Autoborna\CategoryBundle\EventListener\CategorySubscriber::class,
                'arguments' => [
                    'autoborna.helper.bundle',
                    'autoborna.helper.ip_lookup',
                    'autoborna.core.model.auditlog',
                ],
            ],
            'autoborna.category.button.subscriber' => [
                'class'     => \Autoborna\CategoryBundle\EventListener\ButtonSubscriber::class,
                'arguments' => [
                    'router',
                    'translator',
                ],
            ],
        ],
        'forms' => [
            'autoborna.form.type.category' => [
                'class'     => 'Autoborna\CategoryBundle\Form\Type\CategoryListType',
                'arguments' => [
                    'doctrine.orm.entity_manager',
                    'translator',
                    'autoborna.category.model.category',
                    'router',
                ],
            ],
            'autoborna.form.type.category_form' => [
                'class'     => \Autoborna\CategoryBundle\Form\Type\CategoryType::class,
                'arguments' => [
                    'session',
                ],
            ],
            'autoborna.form.type.category_bundles_form' => [
                'class'     => 'Autoborna\CategoryBundle\Form\Type\CategoryBundlesType',
                'arguments' => [
                    'event_dispatcher',
                ],
            ],
        ],
        'models' => [
            'autoborna.category.model.category' => [
                'class'     => 'Autoborna\CategoryBundle\Model\CategoryModel',
                'arguments' => [
                    'request_stack',
                ],
            ],
            'autoborna.category.model.contact.action' => [
                'class'     => \Autoborna\CategoryBundle\Model\ContactActionModel::class,
                'arguments' => [
                    'autoborna.lead.model.lead',
                ],
            ],
        ],
    ],
];
