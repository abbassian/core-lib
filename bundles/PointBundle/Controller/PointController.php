<?php

namespace Autoborna\PointBundle\Controller;

use Autoborna\CoreBundle\Controller\AbstractFormController;
use Autoborna\CoreBundle\Factory\PageHelperFactoryInterface;
use Autoborna\PointBundle\Entity\Point;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PointController extends AbstractFormController
{
    /**
     * @param int $page
     *
     * @return JsonResponse|Response
     */
    public function indexAction($page = 1)
    {
        //set some permissions
        $permissions = $this->get('autoborna.security')->isGranted([
            'point:points:view',
            'point:points:create',
            'point:points:edit',
            'point:points:delete',
            'point:points:publish',
        ], 'RETURN_ARRAY');

        if (!$permissions['point:points:view']) {
            return $this->accessDenied();
        }

        $this->setListFilters();

        /** @var PageHelperFactoryInterface $pageHelperFacotry */
        $pageHelperFacotry = $this->get('autoborna.page.helper.factory');
        $pageHelper        = $pageHelperFacotry->make('autoborna.point', $page);

        $limit      = $pageHelper->getLimit();
        $start      = $pageHelper->getStart();
        $search     = $this->request->get('search', $this->get('session')->get('autoborna.point.filter', ''));
        $filter     = ['string' => $search, 'force' => []];
        $orderBy    = $this->get('session')->get('autoborna.point.orderby', 'p.name');
        $orderByDir = $this->get('session')->get('autoborna.point.orderbydir', 'ASC');
        $points     = $this->getModel('point')->getEntities([
            'start'      => $start,
            'limit'      => $limit,
            'filter'     => $filter,
            'orderBy'    => $orderBy,
            'orderByDir' => $orderByDir,
        ]);

        $this->get('session')->set('autoborna.point.filter', $search);

        $count = count($points);
        if ($count && $count < ($start + 1)) {
            $lastPage  = $pageHelper->countPage($count);
            $returnUrl = $this->generateUrl('autoborna_point_index', ['page' => $lastPage]);
            $pageHelper->rememberPage($lastPage);

            return $this->postActionRedirect([
                'returnUrl'       => $returnUrl,
                'viewParameters'  => ['page' => $lastPage],
                'contentTemplate' => 'AutobornaPointBundle:Point:index',
                'passthroughVars' => [
                    'activeLink'    => '#autoborna_point_index',
                    'autobornaContent' => 'point',
                ],
            ]);
        }

        $pageHelper->rememberPage($page);

        //get the list of actions
        $actions = $this->getModel('point')->getPointActions();

        return $this->delegateView([
            'viewParameters' => [
                'searchValue' => $search,
                'items'       => $points,
                'actions'     => $actions['actions'],
                'page'        => $page,
                'limit'       => $limit,
                'permissions' => $permissions,
                'tmpl'        => $this->request->isXmlHttpRequest() ? $this->request->get('tmpl', 'index') : 'index',
            ],
            'contentTemplate' => 'AutobornaPointBundle:Point:list.html.php',
            'passthroughVars' => [
                'activeLink'    => '#autoborna_point_index',
                'autobornaContent' => 'point',
                'route'         => $this->generateUrl('autoborna_point_index', ['page' => $page]),
            ],
        ]);
    }

    /**
     * Generates new form and processes post data.
     *
     * @param \Autoborna\PointBundle\Entity\Point $entity
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newAction($entity = null)
    {
        $model = $this->getModel('point');

        if (!($entity instanceof Point)) {
            /** @var \Autoborna\PointBundle\Entity\Point $entity */
            $entity = $model->getEntity();
        }

        if (!$this->get('autoborna.security')->isGranted('point:points:create')) {
            return $this->accessDenied();
        }

        //set the page we came from
        $page       = $this->get('session')->get('autoborna.point.page', 1);
        $method     = $this->request->getMethod();
        $point      = $this->request->request->get('point', []);
        $actionType = 'POST' === $method ? ($point['type'] ?? '') : '';
        $action     = $this->generateUrl('autoborna_point_action', ['objectAction' => 'new']);
        $actions    = $model->getPointActions();
        $form       = $model->createForm($entity, $this->get('form.factory'), $action, [
            'pointActions' => $actions,
            'actionType'   => $actionType,
        ]);
        $viewParameters = ['page' => $page];

        ///Check for a submitted form and process it
        if ('POST' === $method) {
            $valid = false;

            if (!$cancelled = $this->isFormCancelled($form)) {
                if ($valid = $this->isFormValid($form)) {
                    //form is valid so process the data
                    $model->saveEntity($entity);

                    $this->addFlash('autoborna.core.notice.created', [
                        '%name%'      => $entity->getName(),
                        '%menu_link%' => 'autoborna_point_index',
                        '%url%'       => $this->generateUrl('autoborna_point_action', [
                            'objectAction' => 'edit',
                            'objectId'     => $entity->getId(),
                        ]),
                    ]);

                    if ($form->get('buttons')->get('save')->isClicked()) {
                        $returnUrl = $this->generateUrl('autoborna_point_index', $viewParameters);
                        $template  = 'AutobornaPointBundle:Point:index';
                    } else {
                        //return edit view so that all the session stuff is loaded
                        return $this->editAction($entity->getId(), true);
                    }
                }
            } else {
                $returnUrl = $this->generateUrl('autoborna_point_index', $viewParameters);
                $template  = 'AutobornaPointBundle:Point:index';
            }

            if ($cancelled || ($valid && $form->get('buttons')->get('save')->isClicked())) {
                return $this->postActionRedirect([
                    'returnUrl'       => $returnUrl,
                    'viewParameters'  => $viewParameters,
                    'contentTemplate' => $template,
                    'passthroughVars' => [
                        'activeLink'    => '#autoborna_point_index',
                        'autobornaContent' => 'point',
                    ],
                ]);
            }
        }

        $themes = ['AutobornaPointBundle:FormTheme\Action'];
        if ($actionType && !empty($actions['actions'][$actionType]['formTheme'])) {
            $themes[] = $actions['actions'][$actionType]['formTheme'];
        }

        return $this->delegateView([
            'viewParameters' => [
                'tmpl'    => $this->request->isXmlHttpRequest() ? $this->request->get('tmpl', 'index') : 'index',
                'entity'  => $entity,
                'form'    => $this->setFormTheme($form, 'AutobornaPointBundle:Point:form.html.php', $themes),
                'actions' => $actions['actions'],
            ],
            'contentTemplate' => 'AutobornaPointBundle:Point:form.html.php',
            'passthroughVars' => [
                'activeLink'    => '#autoborna_point_index',
                'autobornaContent' => 'point',
                'route'         => $this->generateUrl('autoborna_point_action', [
                        'objectAction' => (!empty($valid) ? 'edit' : 'new'), //valid means a new form was applied
                        'objectId'     => $entity->getId(),
                    ]
                ),
            ],
        ]);
    }

    /**
     * Generates edit form and processes post data.
     *
     * @param int  $objectId
     * @param bool $ignorePost
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editAction($objectId, $ignorePost = false)
    {
        $model  = $this->getModel('point');
        $entity = $model->getEntity($objectId);

        //set the page we came from
        $page = $this->get('session')->get('autoborna.point.page', 1);

        $viewParameters = ['page' => $page];

        //set the return URL
        $returnUrl = $this->generateUrl('autoborna_point_index', ['page' => $page]);

        $postActionVars = [
            'returnUrl'       => $returnUrl,
            'viewParameters'  => $viewParameters,
            'contentTemplate' => 'AutobornaPointBundle:Point:index',
            'passthroughVars' => [
                'activeLink'    => '#autoborna_point_index',
                'autobornaContent' => 'point',
            ],
        ];

        //form not found
        if (null === $entity) {
            return $this->postActionRedirect(
                array_merge($postActionVars, [
                    'flashes' => [
                        [
                            'type'    => 'error',
                            'msg'     => 'autoborna.point.error.notfound',
                            'msgVars' => ['%id%' => $objectId],
                        ],
                    ],
                ])
            );
        } elseif (!$this->get('autoborna.security')->isGranted('point:points:edit')) {
            return $this->accessDenied();
        } elseif ($model->isLocked($entity)) {
            //deny access if the entity is locked
            return $this->isLocked($postActionVars, $entity, 'point');
        }

        $method     = $this->request->getMethod();
        $point      = $this->request->request->get('point', []);
        $actionType = 'POST' === $method ? ($point['type'] ?? '') : $entity->getType();

        $action  = $this->generateUrl('autoborna_point_action', ['objectAction' => 'edit', 'objectId' => $objectId]);
        $actions = $model->getPointActions();
        $form    = $model->createForm($entity, $this->get('form.factory'), $action, [
            'pointActions' => $actions,
            'actionType'   => $actionType,
        ]);

        ///Check for a submitted form and process it
        if (!$ignorePost && 'POST' === $method) {
            $valid = false;

            if (!$cancelled = $this->isFormCancelled($form)) {
                if ($valid = $this->isFormValid($form)) {
                    //form is valid so process the data
                    $model->saveEntity($entity, $form->get('buttons')->get('save')->isClicked());

                    $this->addFlash('autoborna.core.notice.updated', [
                        '%name%'      => $entity->getName(),
                        '%menu_link%' => 'autoborna_point_index',
                        '%url%'       => $this->generateUrl('autoborna_point_action', [
                            'objectAction' => 'edit',
                            'objectId'     => $entity->getId(),
                        ]),
                    ]);

                    if ($form->get('buttons')->get('save')->isClicked()) {
                        $returnUrl = $this->generateUrl('autoborna_point_index', $viewParameters);
                        $template  = 'AutobornaPointBundle:Point:index';
                    }
                }
            } else {
                //unlock the entity
                $model->unlockEntity($entity);

                $returnUrl = $this->generateUrl('autoborna_point_index', $viewParameters);
                $template  = 'AutobornaPointBundle:Point:index';
            }

            if ($cancelled || ($valid && $form->get('buttons')->get('save')->isClicked())) {
                return $this->postActionRedirect(
                    array_merge($postActionVars, [
                        'returnUrl'       => $returnUrl,
                        'viewParameters'  => $viewParameters,
                        'contentTemplate' => $template,
                    ])
                );
            }
        } else {
            //lock the entity
            $model->lockEntity($entity);
        }

        $themes = ['AutobornaPointBundle:FormTheme\Action'];
        if (!empty($actions['actions'][$actionType]['formTheme'])) {
            $themes[] = $actions['actions'][$actionType]['formTheme'];
        }

        return $this->delegateView([
            'viewParameters' => [
                'tmpl'    => $this->request->isXmlHttpRequest() ? $this->request->get('tmpl', 'index') : 'index',
                'entity'  => $entity,
                'form'    => $this->setFormTheme($form, 'AutobornaPointBundle:Point:form.html.php', $themes),
                'actions' => $actions['actions'],
            ],
            'contentTemplate' => 'AutobornaPointBundle:Point:form.html.php',
            'passthroughVars' => [
                'activeLink'    => '#autoborna_point_index',
                'autobornaContent' => 'point',
                'route'         => $this->generateUrl('autoborna_point_action', [
                        'objectAction' => 'edit',
                        'objectId'     => $entity->getId(),
                    ]
                ),
            ],
        ]);
    }

    /**
     * Clone an entity.
     *
     * @param int $objectId
     *
     * @return array|JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function cloneAction($objectId)
    {
        $model  = $this->getModel('point');
        $entity = $model->getEntity($objectId);

        if (null != $entity) {
            if (!$this->get('autoborna.security')->isGranted('point:points:create')) {
                return $this->accessDenied();
            }

            $entity = clone $entity;
            $entity->setIsPublished(false);
        }

        return $this->newAction($entity);
    }

    /**
     * Deletes the entity.
     *
     * @param int $objectId
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($objectId)
    {
        $page      = $this->get('session')->get('autoborna.point.page', 1);
        $returnUrl = $this->generateUrl('autoborna_point_index', ['page' => $page]);
        $flashes   = [];

        $postActionVars = [
            'returnUrl'       => $returnUrl,
            'viewParameters'  => ['page' => $page],
            'contentTemplate' => 'AutobornaPointBundle:Point:index',
            'passthroughVars' => [
                'activeLink'    => '#autoborna_point_index',
                'autobornaContent' => 'point',
            ],
        ];

        if ('POST' == $this->request->getMethod()) {
            $model  = $this->getModel('point');
            $entity = $model->getEntity($objectId);

            if (null === $entity) {
                $flashes[] = [
                    'type'    => 'error',
                    'msg'     => 'autoborna.point.error.notfound',
                    'msgVars' => ['%id%' => $objectId],
                ];
            } elseif (!$this->get('autoborna.security')->isGranted('point:points:delete')) {
                return $this->accessDenied();
            } elseif ($model->isLocked($entity)) {
                return $this->isLocked($postActionVars, $entity, 'point');
            }

            $model->deleteEntity($entity);

            $identifier = $this->get('translator')->trans($entity->getName());
            $flashes[]  = [
                'type'    => 'notice',
                'msg'     => 'autoborna.core.notice.deleted',
                'msgVars' => [
                    '%name%' => $identifier,
                    '%id%'   => $objectId,
                ],
            ];
        } //else don't do anything

        return $this->postActionRedirect(
            array_merge($postActionVars, [
                'flashes' => $flashes,
            ])
        );
    }

    /**
     * Deletes a group of entities.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function batchDeleteAction()
    {
        $page      = $this->get('session')->get('autoborna.point.page', 1);
        $returnUrl = $this->generateUrl('autoborna_point_index', ['page' => $page]);
        $flashes   = [];

        $postActionVars = [
            'returnUrl'       => $returnUrl,
            'viewParameters'  => ['page' => $page],
            'contentTemplate' => 'AutobornaPointBundle:Point:index',
            'passthroughVars' => [
                'activeLink'    => '#autoborna_point_index',
                'autobornaContent' => 'point',
            ],
        ];

        if ('POST' == $this->request->getMethod()) {
            $model     = $this->getModel('point');
            $ids       = json_decode($this->request->query->get('ids', '{}'));
            $deleteIds = [];

            // Loop over the IDs to perform access checks pre-delete
            foreach ($ids as $objectId) {
                $entity = $model->getEntity($objectId);

                if (null === $entity) {
                    $flashes[] = [
                        'type'    => 'error',
                        'msg'     => 'autoborna.point.error.notfound',
                        'msgVars' => ['%id%' => $objectId],
                    ];
                } elseif (!$this->get('autoborna.security')->isGranted('point:points:delete')) {
                    $flashes[] = $this->accessDenied(true);
                } elseif ($model->isLocked($entity)) {
                    $flashes[] = $this->isLocked($postActionVars, $entity, 'point', true);
                } else {
                    $deleteIds[] = $objectId;
                }
            }

            // Delete everything we are able to
            if (!empty($deleteIds)) {
                $entities = $model->deleteEntities($deleteIds);

                $flashes[] = [
                    'type'    => 'notice',
                    'msg'     => 'autoborna.point.notice.batch_deleted',
                    'msgVars' => [
                        '%count%' => count($entities),
                    ],
                ];
            }
        } //else don't do anything

        return $this->postActionRedirect(
            array_merge($postActionVars, [
                'flashes' => $flashes,
            ])
        );
    }
}
