<?php

namespace Autoborna\NotificationBundle\Controller;

use Autoborna\CoreBundle\Controller\AbstractFormController;
use Autoborna\CoreBundle\Form\Type\DateRangeType;
use Autoborna\CoreBundle\Helper\InputHelper;
use Autoborna\LeadBundle\Controller\EntityContactsTrait;
use Autoborna\NotificationBundle\Entity\Notification;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class NotificationController extends AbstractFormController
{
    use EntityContactsTrait;

    /**
     * @param int $page
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($page = 1)
    {
        /** @var \Autoborna\NotificationBundle\Model\NotificationModel $model */
        $model = $this->getModel('notification');

        //set some permissions
        $permissions = $this->get('autoborna.security')->isGranted(
            [
                'notification:notifications:viewown',
                'notification:notifications:viewother',
                'notification:notifications:create',
                'notification:notifications:editown',
                'notification:notifications:editother',
                'notification:notifications:deleteown',
                'notification:notifications:deleteother',
                'notification:notifications:publishown',
                'notification:notifications:publishother',
            ],
            'RETURN_ARRAY'
        );

        if (!$permissions['notification:notifications:viewown'] && !$permissions['notification:notifications:viewother']) {
            return $this->accessDenied();
        }

        if ('POST' == $this->request->getMethod()) {
            $this->setListFilters();
        }

        $session = $this->get('session');

        //set limits
        $limit = $session->get('autoborna.notification.limit', $this->coreParametersHelper->get('default_pagelimit'));
        $start = (1 === $page) ? 0 : (($page - 1) * $limit);
        if ($start < 0) {
            $start = 0;
        }

        $search = $this->request->get('search', $session->get('autoborna.notification.filter', ''));
        $session->set('autoborna.notification.filter', $search);

        $filter = [
            'string' => $search,
            'where'  => [
                [
                    'expr' => 'eq',
                    'col'  => 'mobile',
                    'val'  => 0,
                ],
            ],
        ];

        if (!$permissions['notification:notifications:viewother']) {
            $filter['force'][] =
                ['column' => 'e.createdBy', 'expr' => 'eq', 'value' => $this->user->getId()];
        }

        $orderBy    = $session->get('autoborna.notification.orderby', 'e.name');
        $orderByDir = $session->get('autoborna.notification.orderbydir', 'DESC');

        $notifications = $model->getEntities(
            [
                'start'      => $start,
                'limit'      => $limit,
                'filter'     => $filter,
                'orderBy'    => $orderBy,
                'orderByDir' => $orderByDir,
            ]
        );

        $count = count($notifications);
        if ($count && $count < ($start + 1)) {
            //the number of entities are now less then the current page so redirect to the last page
            if (1 === $count) {
                $lastPage = 1;
            } else {
                $lastPage = (floor($count / $limit)) ?: 1;
            }

            $session->set('autoborna.notification.page', $lastPage);
            $returnUrl = $this->generateUrl('autoborna_notification_index', ['page' => $lastPage]);

            return $this->postActionRedirect(
                [
                    'returnUrl'       => $returnUrl,
                    'viewParameters'  => ['page' => $lastPage],
                    'contentTemplate' => 'AutobornaNotificationBundle:Notification:index',
                    'passthroughVars' => [
                        'activeLink'    => '#autoborna_notification_index',
                        'autobornaContent' => 'notification',
                    ],
                ]
            );
        }
        $session->set('autoborna.notification.page', $page);

        return $this->delegateView(
            [
                'viewParameters' => [
                    'searchValue' => $search,
                    'items'       => $notifications,
                    'totalItems'  => $count,
                    'page'        => $page,
                    'limit'       => $limit,
                    'tmpl'        => $this->request->get('tmpl', 'index'),
                    'permissions' => $permissions,
                    'model'       => $model,
                    'security'    => $this->get('autoborna.security'),
                ],
                'contentTemplate' => 'AutobornaNotificationBundle:Notification:list.html.php',
                'passthroughVars' => [
                    'activeLink'    => '#autoborna_notification_index',
                    'autobornaContent' => 'notification',
                    'route'         => $this->generateUrl('autoborna_notification_index', ['page' => $page]),
                ],
            ]
        );
    }

    /**
     * Loads a specific form into the detailed panel.
     *
     * @param $objectId
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function viewAction($objectId)
    {
        /** @var \Autoborna\NotificationBundle\Model\NotificationModel $model */
        $model    = $this->getModel('notification');
        $security = $this->get('autoborna.security');

        /** @var \Autoborna\NotificationBundle\Entity\Notification $notification */
        $notification = $model->getEntity($objectId);
        //set the page we came from
        $page = $this->get('session')->get('autoborna.notification.page', 1);

        if (null === $notification) {
            //set the return URL
            $returnUrl = $this->generateUrl('autoborna_notification_index', ['page' => $page]);

            return $this->postActionRedirect(
                [
                    'returnUrl'       => $returnUrl,
                    'viewParameters'  => ['page' => $page],
                    'contentTemplate' => 'AutobornaNotificationBundle:Notification:index',
                    'passthroughVars' => [
                        'activeLink'    => '#autoborna_notification_index',
                        'autobornaContent' => 'notification',
                    ],
                    'flashes' => [
                        [
                            'type'    => 'error',
                            'msg'     => 'autoborna.notification.error.notfound',
                            'msgVars' => ['%id%' => $objectId],
                        ],
                    ],
                ]
            );
        } elseif (!$this->get('autoborna.security')->hasEntityAccess(
            'notification:notifications:viewown',
            'notification:notifications:viewother',
            $notification->getCreatedBy()
        )
        ) {
            return $this->accessDenied();
        }

        // Audit Log
        $logs = $this->getModel('core.auditlog')->getLogForObject('notification', $notification->getId(), $notification->getDateAdded());

        // Init the date range filter form
        $dateRangeValues = $this->request->get('daterange', []);
        $action          = $this->generateUrl('autoborna_notification_action', ['objectAction' => 'view', 'objectId' => $objectId]);
        $dateRangeForm   = $this->get('form.factory')->create(DateRangeType::class, $dateRangeValues, ['action' => $action]);
        $entityViews     = $model->getHitsLineChartData(
            null,
            new \DateTime($dateRangeForm->get('date_from')->getData()),
            new \DateTime($dateRangeForm->get('date_to')->getData()),
            null,
            ['notification_id' => $notification->getId()]
        );

        // Get click through stats
        $trackableLinks = $model->getNotificationClickStats($notification->getId());

        return $this->delegateView([
            'returnUrl'      => $this->generateUrl('autoborna_notification_action', ['objectAction' => 'view', 'objectId' => $notification->getId()]),
            'viewParameters' => [
                'notification' => $notification,
                'trackables'   => $trackableLinks,
                'logs'         => $logs,
                'permissions'  => $security->isGranted([
                    'notification:notifications:viewown',
                    'notification:notifications:viewother',
                    'notification:notifications:create',
                    'notification:notifications:editown',
                    'notification:notifications:editother',
                    'notification:notifications:deleteown',
                    'notification:notifications:deleteother',
                    'notification:notifications:publishown',
                    'notification:notifications:publishother',
                ], 'RETURN_ARRAY'),
                'security'    => $security,
                'entityViews' => $entityViews,
                'contacts'    => $this->forward(
                    'AutobornaNotificationBundle:Notification:contacts',
                    [
                        'objectId'   => $notification->getId(),
                        'page'       => $this->get('session')->get('autoborna.notification.contact.page', 1),
                        'ignoreAjax' => true,
                    ]
                )->getContent(),
                'dateRangeForm' => $dateRangeForm->createView(),
            ],
            'contentTemplate' => 'AutobornaNotificationBundle:Notification:details.html.php',
            'passthroughVars' => [
                'activeLink'    => '#autoborna_notification_index',
                'autobornaContent' => 'notification',
            ],
        ]);
    }

    /**
     * Generates new form and processes post data.
     *
     * @param Notification $entity
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction($entity = null)
    {
        /** @var \Autoborna\NotificationBundle\Model\NotificationModel $model */
        $model = $this->getModel('notification');

        if (!$entity instanceof Notification) {
            /** @var \Autoborna\NotificationBundle\Entity\Notification $entity */
            $entity = $model->getEntity();
        }

        $method  = $this->request->getMethod();
        $session = $this->get('session');

        if (!$this->get('autoborna.security')->isGranted('notification:notifications:create')) {
            return $this->accessDenied();
        }

        //set the page we came from
        $page         = $session->get('autoborna.notification.page', 1);
        $action       = $this->generateUrl('autoborna_notification_action', ['objectAction' => 'new']);
        $notification = $this->request->request->get('notification', []);
        $updateSelect = ('POST' == $method)
            ? ($notification['updateSelect'] ?? false)
            : $this->request->get('updateSelect', false);

        if ($updateSelect) {
            $entity->setNotificationType('template');
        }

        //create the form
        $form = $model->createForm($entity, $this->get('form.factory'), $action, ['update_select' => $updateSelect]);

        ///Check for a submitted form and process it
        if ('POST' === $method) {
            $valid = false;
            if (!$cancelled = $this->isFormCancelled($form)) {
                if ($valid = $this->isFormValid($form)) {
                    //form is valid so process the data
                    $model->saveEntity($entity);

                    $this->addFlash(
                        'autoborna.core.notice.created',
                        [
                            '%name%'      => $entity->getName(),
                            '%menu_link%' => 'autoborna_notification_index',
                            '%url%'       => $this->generateUrl(
                                'autoborna_notification_action',
                                [
                                    'objectAction' => 'edit',
                                    'objectId'     => $entity->getId(),
                                ]
                            ),
                        ]
                    );

                    if ($form->get('buttons')->get('save')->isClicked()) {
                        $viewParameters = [
                            'objectAction' => 'view',
                            'objectId'     => $entity->getId(),
                        ];
                        $returnUrl = $this->generateUrl('autoborna_notification_action', $viewParameters);
                        $template  = 'AutobornaNotificationBundle:Notification:view';
                    } else {
                        //return edit view so that all the session stuff is loaded
                        return $this->editAction($entity->getId(), true);
                    }
                }
            } else {
                $viewParameters = ['page' => $page];
                $returnUrl      = $this->generateUrl('autoborna_notification_index', $viewParameters);
                $template       = 'AutobornaNotificationBundle:Notification:index';
                //clear any modified content
                $session->remove('autoborna.notification.'.$entity->getId().'.content');
            }

            $passthrough = [
                'activeLink'    => 'autoborna_notification_index',
                'autobornaContent' => 'notification',
            ];

            // Check to see if this is a popup
            if (isset($form['updateSelect'])) {
                $template    = false;
                $passthrough = array_merge(
                    $passthrough,
                    [
                        'updateSelect' => $form['updateSelect']->getData(),
                        'id'           => $entity->getId(),
                        'name'         => $entity->getName(),
                        'group'        => $entity->getLanguage(),
                    ]
                );
            }

            if ($cancelled || ($valid && $form->get('buttons')->get('save')->isClicked())) {
                return $this->postActionRedirect(
                    [
                        'returnUrl'       => $returnUrl,
                        'viewParameters'  => $viewParameters,
                        'contentTemplate' => $template,
                        'passthroughVars' => $passthrough,
                    ]
                );
            }
        }

        return $this->delegateView(
            [
                'viewParameters' => [
                    'form'         => $this->setFormTheme($form, 'AutobornaNotificationBundle:Notification:form.html.php', 'AutobornaNotificationBundle:FormTheme\Notification'),
                    'notification' => $entity,
                ],
                'contentTemplate' => 'AutobornaNotificationBundle:Notification:form.html.php',
                'passthroughVars' => [
                    'activeLink'    => '#autoborna_notification_index',
                    'autobornaContent' => 'notification',
                    'updateSelect'  => InputHelper::clean($this->request->query->get('updateSelect')),
                    'route'         => $this->generateUrl(
                        'autoborna_notification_action',
                        [
                            'objectAction' => 'new',
                        ]
                    ),
                ],
            ]
        );
    }

    /**
     * @param      $objectId
     * @param bool $ignorePost
     * @param bool $forceTypeSelection
     *
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editAction($objectId, $ignorePost = false, $forceTypeSelection = false)
    {
        /** @var \Autoborna\NotificationBundle\Model\NotificationModel $model */
        $model   = $this->getModel('notification');
        $method  = $this->request->getMethod();
        $entity  = $model->getEntity($objectId);
        $session = $this->get('session');
        $page    = $session->get('autoborna.notification.page', 1);

        //set the return URL
        $returnUrl = $this->generateUrl('autoborna_notification_index', ['page' => $page]);

        $postActionVars = [
            'returnUrl'       => $returnUrl,
            'viewParameters'  => ['page' => $page],
            'contentTemplate' => 'AutobornaNotificationBundle:Notification:index',
            'passthroughVars' => [
                'activeLink'    => 'autoborna_notification_index',
                'autobornaContent' => 'notification',
            ],
        ];

        //not found
        if (null === $entity) {
            return $this->postActionRedirect(
                array_merge(
                    $postActionVars,
                    [
                        'flashes' => [
                            [
                                'type'    => 'error',
                                'msg'     => 'autoborna.notification.error.notfound',
                                'msgVars' => ['%id%' => $objectId],
                            ],
                        ],
                    ]
                )
            );
        } elseif (!$this->get('autoborna.security')->hasEntityAccess(
            'notification:notifications:viewown',
            'notification:notifications:viewother',
            $entity->getCreatedBy()
        )
        ) {
            return $this->accessDenied();
        } elseif ($model->isLocked($entity)) {
            //deny access if the entity is locked
            return $this->isLocked($postActionVars, $entity, 'notification');
        }

        //Create the form
        $action       = $this->generateUrl('autoborna_notification_action', ['objectAction' => 'edit', 'objectId' => $objectId]);
        $notification = $this->request->request->get('notification', []);
        $updateSelect = 'POST' === $method
            ? ($notification['updateSelect'] ?? false)
            : $this->request->get('updateSelect', false);

        $form = $model->createForm($entity, $this->get('form.factory'), $action, ['update_select' => $updateSelect]);

        ///Check for a submitted form and process it
        if (!$ignorePost && 'POST' === $method) {
            $valid = false;

            if (!$cancelled = $this->isFormCancelled($form)) {
                if ($valid = $this->isFormValid($form)) {
                    //form is valid so process the data
                    $model->saveEntity($entity, $form->get('buttons')->get('save')->isClicked());

                    $this->addFlash(
                        'autoborna.core.notice.updated',
                        [
                            '%name%'      => $entity->getName(),
                            '%menu_link%' => 'autoborna_notification_index',
                            '%url%'       => $this->generateUrl(
                                'autoborna_notification_action',
                                [
                                    'objectAction' => 'edit',
                                    'objectId'     => $entity->getId(),
                                ]
                            ),
                        ],
                        'warning'
                    );
                }
            } else {
                //clear any modified content
                $session->remove('autoborna.notification.'.$objectId.'.content');
                //unlock the entity
                $model->unlockEntity($entity);
            }

            $template    = 'AutobornaNotificationBundle:Notification:view';
            $passthrough = [
                'activeLink'    => 'autoborna_notification_index',
                'autobornaContent' => 'notification',
            ];

            // Check to see if this is a popup
            if (isset($form['updateSelect'])) {
                $template    = false;
                $passthrough = array_merge(
                    $passthrough,
                    [
                        'updateSelect' => $form['updateSelect']->getData(),
                        'id'           => $entity->getId(),
                        'name'         => $entity->getName(),
                        'group'        => $entity->getLanguage(),
                    ]
                );
            }

            if ($cancelled || ($valid && $form->get('buttons')->get('save')->isClicked())) {
                $viewParameters = [
                    'objectAction' => 'view',
                    'objectId'     => $entity->getId(),
                ];

                return $this->postActionRedirect(
                    array_merge(
                        $postActionVars,
                        [
                            'returnUrl'       => $this->generateUrl('autoborna_notification_action', $viewParameters),
                            'viewParameters'  => $viewParameters,
                            'contentTemplate' => $template,
                            'passthroughVars' => $passthrough,
                        ]
                    )
                );
            }
        } else {
            //lock the entity
            $model->lockEntity($entity);
        }

        return $this->delegateView(
            [
                'viewParameters' => [
                    'form'               => $this->setFormTheme($form, 'AutobornaNotificationBundle:Notification:form.html.php', 'AutobornaNotificationBundle:FormTheme\Notification'),
                    'notification'       => $entity,
                    'forceTypeSelection' => $forceTypeSelection,
                ],
                'contentTemplate' => 'AutobornaNotificationBundle:Notification:form.html.php',
                'passthroughVars' => [
                    'activeLink'    => '#autoborna_notification_index',
                    'autobornaContent' => 'notification',
                    'updateSelect'  => InputHelper::clean($this->request->query->get('updateSelect')),
                    'route'         => $this->generateUrl(
                        'autoborna_notification_action',
                        [
                            'objectAction' => 'edit',
                            'objectId'     => $entity->getId(),
                        ]
                    ),
                ],
            ]
        );
    }

    /**
     * Clone an entity.
     *
     * @param $objectId
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function cloneAction($objectId)
    {
        $model  = $this->getModel('notification');
        $entity = $model->getEntity($objectId);

        if (null != $entity) {
            if (!$this->get('autoborna.security')->isGranted('notification:notifications:create')
                || !$this->get('autoborna.security')->hasEntityAccess(
                    'notification:notifications:viewown',
                    'notification:notifications:viewother',
                    $entity->getCreatedBy()
                )
            ) {
                return $this->accessDenied();
            }

            $entity      = clone $entity;
            $session     = $this->get('session');
            $contentName = 'autoborna.notification.'.$entity->getId().'.content';

            $session->set($contentName, $entity->getContent());
        }

        return $this->newAction($entity);
    }

    /**
     * Deletes the entity.
     *
     * @param $objectId
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($objectId)
    {
        $page      = $this->get('session')->get('autoborna.notification.page', 1);
        $returnUrl = $this->generateUrl('autoborna_notification_index', ['page' => $page]);
        $flashes   = [];

        $postActionVars = [
            'returnUrl'       => $returnUrl,
            'viewParameters'  => ['page' => $page],
            'contentTemplate' => 'AutobornaNotificationBundle:Notification:index',
            'passthroughVars' => [
                'activeLink'    => 'autoborna_notification_index',
                'autobornaContent' => 'notification',
            ],
        ];

        if ('POST' == $this->request->getMethod()) {
            $model  = $this->getModel('notification');
            $entity = $model->getEntity($objectId);

            if (null === $entity) {
                $flashes[] = [
                    'type'    => 'error',
                    'msg'     => 'autoborna.notification.error.notfound',
                    'msgVars' => ['%id%' => $objectId],
                ];
            } elseif (!$this->get('autoborna.security')->hasEntityAccess(
                'notification:notifications:deleteown',
                'notification:notifications:deleteother',
                $entity->getCreatedBy()
            )
            ) {
                return $this->accessDenied();
            } elseif ($model->isLocked($entity)) {
                return $this->isLocked($postActionVars, $entity, 'notification');
            }

            $model->deleteEntity($entity);

            $flashes[] = [
                'type'    => 'notice',
                'msg'     => 'autoborna.core.notice.deleted',
                'msgVars' => [
                    '%name%' => $entity->getName(),
                    '%id%'   => $objectId,
                ],
            ];
        } //else don't do anything

        return $this->postActionRedirect(
            array_merge(
                $postActionVars,
                [
                    'flashes' => $flashes,
                ]
            )
        );
    }

    /**
     * Deletes a group of entities.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function batchDeleteAction()
    {
        $page      = $this->get('session')->get('autoborna.notification.page', 1);
        $returnUrl = $this->generateUrl('autoborna_notification_index', ['page' => $page]);
        $flashes   = [];

        $postActionVars = [
            'returnUrl'       => $returnUrl,
            'viewParameters'  => ['page' => $page],
            'contentTemplate' => 'AutobornaNotificationBundle:Notification:index',
            'passthroughVars' => [
                'activeLink'    => '#autoborna_notification_index',
                'autobornaContent' => 'notification',
            ],
        ];

        if ('POST' == $this->request->getMethod()) {
            $model = $this->getModel('notification');
            $ids   = json_decode($this->request->query->get('ids', '{}'));

            $deleteIds = [];

            // Loop over the IDs to perform access checks pre-delete
            foreach ($ids as $objectId) {
                $entity = $model->getEntity($objectId);

                if (null === $entity) {
                    $flashes[] = [
                        'type'    => 'error',
                        'msg'     => 'autoborna.notification.error.notfound',
                        'msgVars' => ['%id%' => $objectId],
                    ];
                } elseif (!$this->get('autoborna.security')->hasEntityAccess(
                    'notification:notifications:viewown',
                    'notification:notifications:viewother',
                    $entity->getCreatedBy()
                )
                ) {
                    $flashes[] = $this->accessDenied(true);
                } elseif ($model->isLocked($entity)) {
                    $flashes[] = $this->isLocked($postActionVars, $entity, 'notification', true);
                } else {
                    $deleteIds[] = $objectId;
                }
            }

            // Delete everything we are able to
            if (!empty($deleteIds)) {
                $entities = $model->deleteEntities($deleteIds);

                $flashes[] = [
                    'type'    => 'notice',
                    'msg'     => 'autoborna.notification.notice.batch_deleted',
                    'msgVars' => [
                        '%count%' => count($entities),
                    ],
                ];
            }
        } //else don't do anything

        return $this->postActionRedirect(
            array_merge(
                $postActionVars,
                [
                    'flashes' => $flashes,
                ]
            )
        );
    }

    /**
     * @param $objectId
     *
     * @return JsonResponse|Response
     */
    public function previewAction($objectId)
    {
        /** @var \Autoborna\NotificationBundle\Model\NotificationModel $model */
        $model        = $this->getModel('notification');
        $notification = $model->getEntity($objectId);

        if (null != $notification
            && $this->get('autoborna.security')->hasEntityAccess(
                'notification:notifications:editown',
                'notification:notifications:editother'
            )
        ) {
        }

        return $this->delegateView(
            [
                'viewParameters' => [
                    'notification' => $notification,
                ],
                'contentTemplate' => 'AutobornaNotificationBundle:Notification:preview.html.php',
            ]
        );
    }

    /**
     * @param     $objectId
     * @param int $page
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function contactsAction($objectId, $page = 1)
    {
        return $this->generateContactsGrid(
            $objectId,
            $page,
            'notification:notifications:view',
            'notification',
            'push_notification_stats',
            'notification',
            'notification_id'
        );
    }
}
