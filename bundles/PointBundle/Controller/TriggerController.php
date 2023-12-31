<?php

namespace Autoborna\PointBundle\Controller;

use Autoborna\CoreBundle\Controller\FormController;
use Autoborna\CoreBundle\Factory\PageHelperFactoryInterface;
use Autoborna\PointBundle\Entity\Trigger;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TriggerController extends FormController
{
    /**
     * @param int $page
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function indexAction($page = 1)
    {
        //set some permissions
        $permissions = $this->get('autoborna.security')->isGranted([
            'point:triggers:view',
            'point:triggers:create',
            'point:triggers:edit',
            'point:triggers:delete',
            'point:triggers:publish',
        ], 'RETURN_ARRAY');

        if (!$permissions['point:triggers:view']) {
            return $this->accessDenied();
        }

        $this->setListFilters();

        /** @var PageHelperFactoryInterface $pageHelperFacotry */
        $pageHelperFacotry = $this->get('autoborna.page.helper.factory');
        $pageHelper        = $pageHelperFacotry->make('autoborna.point.trigger', $page);

        $limit      = $pageHelper->getLimit();
        $start      = $pageHelper->getStart();
        $search     = $this->request->get('search', $this->get('session')->get('autoborna.point.trigger.filter', ''));
        $filter     = ['string' => $search, 'force' => []];
        $orderBy    = $this->get('session')->get('autoborna.point.trigger.orderby', 't.name');
        $orderByDir = $this->get('session')->get('autoborna.point.trigger.orderbydir', 'ASC');
        $triggers   = $this->getModel('point.trigger')->getEntities(
            [
                'start'      => $start,
                'limit'      => $limit,
                'filter'     => $filter,
                'orderBy'    => $orderBy,
                'orderByDir' => $orderByDir,
            ]
        );

        $this->get('session')->set('autoborna.point.trigger.filter', $search);

        $count = count($triggers);
        if ($count && $count < ($start + 1)) {
            $lastPage  = $pageHelper->countPage($count);
            $returnUrl = $this->generateUrl('autoborna_pointtrigger_index', ['page' => $lastPage]);
            $pageHelper->rememberPage($lastPage);

            return $this->postActionRedirect([
                'returnUrl'       => $returnUrl,
                'viewParameters'  => ['page' => $lastPage],
                'contentTemplate' => 'AutobornaPointBundle:Trigger:index',
                'passthroughVars' => [
                    'activeLink'    => '#autoborna_pointtrigger_index',
                    'autobornaContent' => 'pointTrigger',
                ],
            ]);
        }

        $pageHelper->rememberPage($page);

        return $this->delegateView([
            'viewParameters' => [
                'searchValue' => $search,
                'items'       => $triggers,
                'page'        => $page,
                'limit'       => $limit,
                'permissions' => $permissions,
                'tmpl'        => $this->request->isXmlHttpRequest() ? $this->request->get('tmpl', 'index') : 'index',
            ],
            'contentTemplate' => 'AutobornaPointBundle:Trigger:list.html.php',
            'passthroughVars' => [
                'activeLink'    => '#autoborna_pointtrigger_index',
                'autobornaContent' => 'pointTrigger',
                'route'         => $this->generateUrl('autoborna_pointtrigger_index', ['page' => $page]),
            ],
        ]);
    }

    /**
     * View a specific trigger.
     *
     * @param int $objectId
     *
     * @return array|JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function viewAction($objectId)
    {
        $entity = $this->getModel('point.trigger')->getEntity($objectId);

        //set the page we came from
        $page = $this->get('session')->get('autoborna.point.trigger.page', 1);

        $permissions = $this->get('autoborna.security')->isGranted([
            'point:triggers:view',
            'point:triggers:create',
            'point:triggers:edit',
            'point:triggers:delete',
            'point:triggers:publish',
        ], 'RETURN_ARRAY');

        if (null === $entity) {
            //set the return URL
            $returnUrl = $this->generateUrl('autoborna_pointtrigger_index', ['page' => $page]);

            return $this->postActionRedirect([
                'returnUrl'       => $returnUrl,
                'viewParameters'  => ['page' => $page],
                'contentTemplate' => 'AutobornaPointBundle:Trigger:index',
                'passthroughVars' => [
                    'activeLink'    => '#autoborna_pointtrigger_index',
                    'autobornaContent' => 'pointTrigger',
                ],
                'flashes' => [
                    [
                        'type'    => 'error',
                        'msg'     => 'autoborna.point.trigger.error.notfound',
                        'msgVars' => ['%id%' => $objectId],
                    ],
                ],
            ]);
        } elseif (!$permissions['point:triggers:view']) {
            return $this->accessDenied();
        }

        return $this->delegateView([
            'viewParameters' => [
                'entity'      => $entity,
                'page'        => $page,
                'permissions' => $permissions,
            ],
            'contentTemplate' => 'AutobornaPointBundle:Trigger:details.html.php',
            'passthroughVars' => [
                'activeLink'    => '#autoborna_pointtrigger_index',
                'autobornaContent' => 'pointTrigger',
                'route'         => $this->generateUrl('autoborna_pointtrigger_action', [
                        'objectAction' => 'view',
                        'objectId'     => $entity->getId(), ]
                ),
            ],
        ]);
    }

    /**
     * Generates new form and processes post data.
     *
     * @param \Autoborna\PointBundle\Entity\Trigger $entity
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newAction($entity = null)
    {
        /** @var \Autoborna\PointBundle\Model\TriggerModel $model */
        $model = $this->getModel('point.trigger');

        if (!($entity instanceof Trigger)) {
            /** @var \Autoborna\PointBundle\Entity\Trigger $entity */
            $entity = $model->getEntity();
        }

        $session      = $this->get('session');
        $pointTrigger = $this->request->request->get('pointtrigger', []);
        $sessionId    = $pointTrigger['sessionId'] ?? 'autoborna_'.sha1(uniqid(random_int(1, PHP_INT_MAX), true));

        if (!$this->get('autoborna.security')->isGranted('point:triggers:create')) {
            return $this->accessDenied();
        }

        //set the page we came from
        $page = $this->get('session')->get('autoborna.point.trigger.page', 1);

        //set added/updated events
        $addEvents     = $session->get('autoborna.point.'.$sessionId.'.triggerevents.modified', []);
        $deletedEvents = $session->get('autoborna.point.'.$sessionId.'.triggerevents.deleted', []);

        $action = $this->generateUrl('autoborna_pointtrigger_action', ['objectAction' => 'new']);
        $form   = $model->createForm($entity, $this->get('form.factory'), $action);
        $form->get('sessionId')->setData($sessionId);

        ///Check for a submitted form and process it
        if ('POST' == $this->request->getMethod()) {
            $valid = false;
            if (!$cancelled = $this->isFormCancelled($form)) {
                if ($valid = $this->isFormValid($form)) {
                    //only save events that are not to be deleted
                    $events = array_diff_key($addEvents, array_flip($deletedEvents));

                    //make sure that at least one action is selected
                    if ('point.trigger' == 'point' && empty($events)) {
                        //set the error
                        $form->addError(new FormError(
                            $this->get('translator')->trans('autoborna.core.value.required', [], 'validators')
                        ));
                        $valid = false;
                    } else {
                        $model->setEvents($entity, $events);

                        $model->saveEntity($entity);

                        $this->addFlash('autoborna.core.notice.created', [
                            '%name%'      => $entity->getName(),
                            '%menu_link%' => 'autoborna_pointtrigger_index',
                            '%url%'       => $this->generateUrl('autoborna_pointtrigger_action', [
                                'objectAction' => 'edit',
                                'objectId'     => $entity->getId(),
                            ]),
                        ]);

                        if (!$form->get('buttons')->get('save')->isClicked()) {
                            //return edit view so that all the session stuff is loaded
                            return $this->editAction($entity->getId(), true);
                        }
                    }
                }
            }

            if ($cancelled || ($valid && $form->get('buttons')->get('save')->isClicked())) {
                $viewParameters = ['page' => $page];
                $returnUrl      = $this->generateUrl('autoborna_pointtrigger_index', $viewParameters);
                $template       = 'AutobornaPointBundle:Trigger:index';

                //clear temporary fields
                $this->clearSessionComponents($sessionId);

                return $this->postActionRedirect([
                    'returnUrl'       => $returnUrl,
                    'viewParameters'  => $viewParameters,
                    'contentTemplate' => $template,
                    'passthroughVars' => [
                        'activeLink'    => '#autoborna_pointtrigger_index',
                        'autobornaContent' => 'pointTrigger',
                    ],
                ]);
            }
        } else {
            //clear out existing fields in case the form was refreshed, browser closed, etc
            $this->clearSessionComponents($sessionId);
            $addEvents = $deletedEvents = [];
        }

        return $this->delegateView([
            'viewParameters' => [
                'events'        => $model->getEventGroups(),
                'triggerEvents' => $addEvents,
                'deletedEvents' => $deletedEvents,
                'tmpl'          => $this->request->isXmlHttpRequest() ? $this->request->get('tmpl', 'index') : 'index',
                'entity'        => $entity,
                'form'          => $form->createView(),
                'sessionId'     => $sessionId,
            ],
            'contentTemplate' => 'AutobornaPointBundle:Trigger:form.html.php',
            'passthroughVars' => [
                'activeLink'    => '#autoborna_pointtrigger_index',
                'autobornaContent' => 'pointTrigger',
                'route'         => $this->generateUrl('autoborna_pointtrigger_action', [
                        'objectAction' => (!empty($valid) ? 'edit' : 'new'), //valid means a new form was applied
                        'objectId'     => $entity->getId(), ]
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
     * @return JsonResponse|Response
     */
    public function editAction($objectId, $ignorePost = false)
    {
        /** @var \Autoborna\PointBundle\Model\TriggerModel $model */
        $model      = $this->getModel('point.trigger');
        $entity     = $model->getEntity($objectId);
        $session    = $this->get('session');
        $cleanSlate = true;

        //set the page we came from
        $page = $this->get('session')->get('autoborna.point.trigger.page', 1);

        //set the return URL
        $returnUrl = $this->generateUrl('autoborna_pointtrigger_index', ['page' => $page]);

        $postActionVars = [
            'returnUrl'       => $returnUrl,
            'viewParameters'  => ['page' => $page],
            'contentTemplate' => 'AutobornaPointBundle:Trigger:index',
            'passthroughVars' => [
                'activeLink'    => '#autoborna_pointtrigger_index',
                'autobornaContent' => 'pointTrigger',
            ],
        ];

        //form not found
        if (null === $entity) {
            return $this->postActionRedirect(
                array_merge($postActionVars, [
                    'flashes' => [
                        [
                            'type'    => 'error',
                            'msg'     => 'autoborna.point.trigger.error.notfound',
                            'msgVars' => ['%id%' => $objectId],
                        ],
                    ],
                ])
            );
        } elseif (!$this->get('autoborna.security')->isGranted('point:triggers:edit')) {
            return $this->accessDenied();
        } elseif ($model->isLocked($entity)) {
            //deny access if the entity is locked
            return $this->isLocked($postActionVars, $entity, 'point.trigger');
        }

        $action = $this->generateUrl('autoborna_pointtrigger_action', ['objectAction' => 'edit', 'objectId' => $objectId]);
        $form   = $model->createForm($entity, $this->get('form.factory'), $action);
        $form->get('sessionId')->setData($objectId);

        ///Check for a submitted form and process it
        if (!$ignorePost && 'POST' == $this->request->getMethod()) {
            $valid = false;
            if (!$cancelled = $this->isFormCancelled($form)) {
                //set added/updated events
                $addEvents     = $session->get('autoborna.point.'.$objectId.'.triggerevents.modified', []);
                $deletedEvents = $session->get('autoborna.point.'.$objectId.'.triggerevents.deleted', []);
                $events        = array_diff_key($addEvents, array_flip($deletedEvents));

                if ($valid = $this->isFormValid($form)) {
                    //make sure that at least one field is selected
                    if ('point.trigger' == 'point' && empty($addEvents)) {
                        //set the error
                        $form->addError(new FormError(
                            $this->get('translator')->trans('autoborna.core.value.required', [], 'validators')
                        ));
                        $valid = false;
                    } else {
                        $model->setEvents($entity, $events);

                        //form is valid so process the data
                        $model->saveEntity($entity, $form->get('buttons')->get('save')->isClicked());

                        //delete entities
                        if (count($deletedEvents)) {
                            $this->getModel('point.triggerevent')->deleteEntities($deletedEvents);
                        }

                        $this->addFlash('autoborna.core.notice.updated', [
                            '%name%'      => $entity->getName(),
                            '%menu_link%' => 'autoborna_pointtrigger_index',
                            '%url%'       => $this->generateUrl('autoborna_pointtrigger_action', [
                                'objectAction' => 'edit',
                                'objectId'     => $entity->getId(),
                            ]),
                        ]);
                    }
                }
            } else {
                //unlock the entity
                $model->unlockEntity($entity);
            }

            if ($cancelled || ($valid && $form->get('buttons')->get('save')->isClicked())) {
                $viewParameters = ['page' => $page];
                $returnUrl      = $this->generateUrl('autoborna_pointtrigger_index', $viewParameters);
                $template       = 'AutobornaPointBundle:Trigger:index';

                //remove fields from session
                $this->clearSessionComponents($objectId);

                return $this->postActionRedirect(
                    array_merge($postActionVars, [
                        'returnUrl'       => $returnUrl,
                        'viewParameters'  => $viewParameters,
                        'contentTemplate' => $template,
                    ])
                );
            } elseif ($form->get('buttons')->get('apply')->isClicked()) {
                //rebuild everything to include new ids
                $cleanSlate = true;
            }
        } else {
            $cleanSlate = true;

            //lock the entity
            $model->lockEntity($entity);
        }

        if ($cleanSlate) {
            //clean slate
            $this->clearSessionComponents($objectId);

            //load existing events into session
            $triggerEvents   = [];
            $existingActions = $entity->getEvents()->toArray();
            foreach ($existingActions as $a) {
                $id     = $a->getId();
                $action = $a->convertToArray();
                unset($action['form']);
                $triggerEvents[$id] = $action;
            }
            $session->set('autoborna.point.'.$objectId.'.triggerevents.modified', $triggerEvents);
            $deletedEvents = [];
        }

        return $this->delegateView([
            'viewParameters' => [
                'events'        => $model->getEventGroups(),
                'triggerEvents' => $triggerEvents,
                'deletedEvents' => $deletedEvents,
                'tmpl'          => $this->request->isXmlHttpRequest() ? $this->request->get('tmpl', 'index') : 'index',
                'entity'        => $entity,
                'form'          => $form->createView(),
                'sessionId'     => $objectId,
            ],
            'contentTemplate' => 'AutobornaPointBundle:Trigger:form.html.php',
            'passthroughVars' => [
                'activeLink'    => '#autoborna_pointtrigger_index',
                'autobornaContent' => 'pointTrigger',
                'route'         => $this->generateUrl('autoborna_pointtrigger_action', [
                        'objectAction' => 'edit',
                        'objectId'     => $entity->getId(), ]
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
        $model  = $this->getModel('point.trigger');
        $entity = $model->getEntity($objectId);

        if (null != $entity) {
            if (!$this->get('autoborna.security')->isGranted('point:triggers:create')) {
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
     * @param $objectId
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($objectId)
    {
        $page      = $this->get('session')->get('autoborna.point.trigger.page', 1);
        $returnUrl = $this->generateUrl('autoborna_pointtrigger_index', ['page' => $page]);
        $flashes   = [];

        $postActionVars = [
            'returnUrl'       => $returnUrl,
            'viewParameters'  => ['page' => $page],
            'contentTemplate' => 'AutobornaPointBundle:Trigger:index',
            'passthroughVars' => [
                'activeLink'    => '#autoborna_pointtrigger_index',
                'autobornaContent' => 'pointTrigger',
            ],
        ];

        if ('POST' == $this->request->getMethod()) {
            $model  = $this->getModel('point.trigger');
            $entity = $model->getEntity($objectId);

            if (null === $entity) {
                $flashes[] = [
                    'type'    => 'error',
                    'msg'     => 'autoborna.point.trigger.error.notfound',
                    'msgVars' => ['%id%' => $objectId],
                ];
            } elseif (!$this->get('autoborna.security')->isGranted('point:triggers:delete')) {
                return $this->accessDenied();
            } elseif ($model->isLocked($entity)) {
                return $this->isLocked($postActionVars, $entity, 'point.trigger');
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
        $page      = $this->get('session')->get('autoborna.point.trigger.page', 1);
        $returnUrl = $this->generateUrl('autoborna_pointtrigger_index', ['page' => $page]);
        $flashes   = [];

        $postActionVars = [
            'returnUrl'       => $returnUrl,
            'viewParameters'  => ['page' => $page],
            'contentTemplate' => 'AutobornaPointBundle:Trigger:index',
            'passthroughVars' => [
                'activeLink'    => '#autoborna_pointtrigger_index',
                'autobornaContent' => 'pointTrigger',
            ],
        ];

        if ('POST' == $this->request->getMethod()) {
            $model     = $this->getModel('point.trigger');
            $ids       = json_decode($this->request->query->get('ids', '{}'));
            $deleteIds = [];

            // Loop over the IDs to perform access checks pre-delete
            foreach ($ids as $objectId) {
                $entity = $model->getEntity($objectId);

                if (null === $entity) {
                    $flashes[] = [
                        'type'    => 'error',
                        'msg'     => 'autoborna.point.trigger.error.notfound',
                        'msgVars' => ['%id%' => $objectId],
                    ];
                } elseif (!$this->get('autoborna.security')->isGranted('point:triggers:delete')) {
                    $flashes[] = $this->accessDenied(true);
                } elseif ($model->isLocked($entity)) {
                    $flashes[] = $this->isLocked($postActionVars, $entity, 'point.trigger', true);
                } else {
                    $deleteIds[] = $objectId;
                }
            }

            // Delete everything we are able to
            if (!empty($deleteIds)) {
                $entities = $model->deleteEntities($deleteIds);

                $flashes[] = [
                    'type'    => 'notice',
                    'msg'     => 'autoborna.point.trigger.notice.batch_deleted',
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

    /**
     * Clear field and events from the session.
     */
    public function clearSessionComponents($sessionId)
    {
        $session = $this->get('session');
        $session->remove('autoborna.point.'.$sessionId.'.triggerevents.modified');
        $session->remove('autoborna.point.'.$sessionId.'.triggerevents.deleted');
    }
}
