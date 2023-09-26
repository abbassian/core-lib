<?php

namespace Autoborna\SmsBundle\Controller;

use Autoborna\CoreBundle\Controller\FormController;
use Autoborna\CoreBundle\Form\Type\DateRangeType;
use Autoborna\CoreBundle\Helper\InputHelper;
use Autoborna\LeadBundle\Controller\EntityContactsTrait;
use Autoborna\SmsBundle\Entity\Sms;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class SmsController extends FormController
{
    use EntityContactsTrait;

    /**
     * @param int $page
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($page = 1)
    {
        /** @var \Autoborna\SmsBundle\Model\SmsModel $model */
        $model = $this->getModel('sms');

        //set some permissions
        $permissions = $this->get('autoborna.security')->isGranted(
            [
                'sms:smses:viewown',
                'sms:smses:viewother',
                'sms:smses:create',
                'sms:smses:editown',
                'sms:smses:editother',
                'sms:smses:deleteown',
                'sms:smses:deleteother',
                'sms:smses:publishown',
                'sms:smses:publishother',
            ],
            'RETURN_ARRAY'
        );

        if (!$permissions['sms:smses:viewown'] && !$permissions['sms:smses:viewother']) {
            return $this->accessDenied();
        }

        $this->setListFilters();

        $session = $this->get('session');

        //set limits
        $limit = $session->get('autoborna.sms.limit', $this->coreParametersHelper->get('default_pagelimit'));
        $start = (1 === $page) ? 0 : (($page - 1) * $limit);
        if ($start < 0) {
            $start = 0;
        }

        $search = $this->request->get('search', $session->get('autoborna.sms.filter', ''));
        $session->set('autoborna.sms.filter', $search);

        $filter = ['string' => $search];

        if (!$permissions['sms:smses:viewother']) {
            $filter['force'][] =
                [
                    'column' => 'e.createdBy',
                    'expr'   => 'eq',
                    'value'  => $this->user->getId(),
                ];
        }

        $orderBy    = $session->get('autoborna.sms.orderby', 'e.name');
        $orderByDir = $session->get('autoborna.sms.orderbydir', $this->getDefaultOrderDirection());

        $smss = $model->getEntities([
            'start'      => $start,
            'limit'      => $limit,
            'filter'     => $filter,
            'orderBy'    => $orderBy,
            'orderByDir' => $orderByDir,
        ]);

        $count = count($smss);
        if ($count && $count < ($start + 1)) {
            //the number of entities are now less then the current page so redirect to the last page
            if (1 === $count) {
                $lastPage = 1;
            } else {
                $lastPage = (floor($count / $limit)) ?: 1;
            }

            $session->set('autoborna.sms.page', $lastPage);
            $returnUrl = $this->generateUrl('autoborna_sms_index', ['page' => $lastPage]);

            return $this->postActionRedirect([
                'returnUrl'       => $returnUrl,
                'viewParameters'  => ['page' => $lastPage],
                'contentTemplate' => 'AutobornaSmsBundle:Sms:index',
                'passthroughVars' => [
                    'activeLink'    => '#autoborna_sms_index',
                    'autobornaContent' => 'sms',
                ],
            ]);
        }
        $session->set('autoborna.sms.page', $page);

        return $this->delegateView([
            'viewParameters' => [
                'searchValue' => $search,
                'items'       => $smss,
                'totalItems'  => $count,
                'page'        => $page,
                'limit'       => $limit,
                'tmpl'        => $this->request->get('tmpl', 'index'),
                'permissions' => $permissions,
                'model'       => $model,
                'security'    => $this->get('autoborna.security'),
                'configured'  => count($this->get('autoborna.sms.transport_chain')->getEnabledTransports()) > 0,
            ],
            'contentTemplate' => 'AutobornaSmsBundle:Sms:list.html.php',
            'passthroughVars' => [
                'activeLink'    => '#autoborna_sms_index',
                'autobornaContent' => 'sms',
                'route'         => $this->generateUrl('autoborna_sms_index', ['page' => $page]),
            ],
        ]);
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
        /** @var \Autoborna\SmsBundle\Model\SmsModel $model */
        $model    = $this->getModel('sms');
        $security = $this->get('autoborna.security');

        /** @var \Autoborna\SmsBundle\Entity\Sms $sms */
        $sms = $model->getEntity($objectId);
        //set the page we came from
        $page = $this->get('session')->get('autoborna.sms.page', 1);

        if (null === $sms) {
            //set the return URL
            $returnUrl = $this->generateUrl('autoborna_sms_index', ['page' => $page]);

            return $this->postActionRedirect([
                'returnUrl'       => $returnUrl,
                'viewParameters'  => ['page' => $page],
                'contentTemplate' => 'AutobornaSmsBundle:Sms:index',
                'passthroughVars' => [
                    'activeLink'    => '#autoborna_sms_index',
                    'autobornaContent' => 'sms',
                ],
                'flashes' => [
                    [
                        'type'    => 'error',
                        'msg'     => 'autoborna.sms.error.notfound',
                        'msgVars' => ['%id%' => $objectId],
                    ],
                ],
            ]);
        } elseif (!$this->get('autoborna.security')->hasEntityAccess(
            'sms:smses:viewown',
            'sms:smses:viewother',
            $sms->getCreatedBy()
        )
        ) {
            return $this->accessDenied();
        }

        // Audit Log
        $logs = $this->getModel('core.auditlog')->getLogForObject('sms', $sms->getId(), $sms->getDateAdded());

        // Init the date range filter form
        $dateRangeValues = $this->request->get('daterange', []);
        $action          = $this->generateUrl('autoborna_sms_action', ['objectAction' => 'view', 'objectId' => $objectId]);
        $dateRangeForm   = $this->get('form.factory')->create(DateRangeType::class, $dateRangeValues, ['action' => $action]);
        $entityViews     = $model->getHitsLineChartData(
            null,
            new \DateTime($dateRangeForm->get('date_from')->getData()),
            new \DateTime($dateRangeForm->get('date_to')->getData()),
            null,
            ['sms_id' => $sms->getId()]
        );

        // Get click through stats
        $trackableLinks = $model->getSmsClickStats($sms->getId());

        return $this->delegateView([
            'returnUrl'      => $this->generateUrl('autoborna_sms_action', ['objectAction' => 'view', 'objectId' => $sms->getId()]),
            'viewParameters' => [
                'sms'         => $sms,
                'trackables'  => $trackableLinks,
                'logs'        => $logs,
                'isEmbedded'  => $this->request->get('isEmbedded') ? $this->request->get('isEmbedded') : false,
                'permissions' => $security->isGranted([
                    'sms:smses:viewown',
                    'sms:smses:viewother',
                    'sms:smses:create',
                    'sms:smses:editown',
                    'sms:smses:editother',
                    'sms:smses:deleteown',
                    'sms:smses:deleteother',
                    'sms:smses:publishown',
                    'sms:smses:publishother',
                ], 'RETURN_ARRAY'),
                'security'    => $security,
                'entityViews' => $entityViews,
                'contacts'    => $this->forward(
                    'AutobornaSmsBundle:Sms:contacts',
                    [
                        'objectId'   => $sms->getId(),
                        'page'       => $this->get('session')->get('autoborna.sms.contact.page', 1),
                        'ignoreAjax' => true,
                    ]
                )->getContent(),
                'dateRangeForm' => $dateRangeForm->createView(),
            ],
            'contentTemplate' => 'AutobornaSmsBundle:Sms:details.html.php',
            'passthroughVars' => [
                'activeLink'    => '#autoborna_sms_index',
                'autobornaContent' => 'sms',
            ],
        ]);
    }

    /**
     * Generates new form and processes post data.
     *
     * @param Sms $entity
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction($entity = null)
    {
        /** @var \Autoborna\SmsBundle\Model\SmsModel $model */
        $model = $this->getModel('sms');

        if (!$entity instanceof Sms) {
            /** @var \Autoborna\SmsBundle\Entity\Sms $entity */
            $entity = $model->getEntity();
        }

        $method  = $this->request->getMethod();
        $session = $this->get('session');

        if (!$this->get('autoborna.security')->isGranted('sms:smses:create')) {
            return $this->accessDenied();
        }

        //set the page we came from
        $page         = $session->get('autoborna.sms.page', 1);
        $action       = $this->generateUrl('autoborna_sms_action', ['objectAction' => 'new']);
        $sms          = $this->request->request->get('sms', []);
        $updateSelect = 'POST' === $method
            ? ($sms['updateSelect'] ?? false)
            : $this->request->get('updateSelect', false);

        if ($updateSelect) {
            $entity->setSmsType('template');
        }

        //create the form
        $form = $model->createForm($entity, $this->get('form.factory'), $action, ['update_select' => $updateSelect]);

        ///Check for a submitted form and process it
        if ('POST' == $method) {
            $valid = false;
            if (!$cancelled = $this->isFormCancelled($form)) {
                if ($valid = $this->isFormValid($form)) {
                    //form is valid so process the data
                    $model->saveEntity($entity);

                    $this->addFlash(
                        'autoborna.core.notice.created',
                        [
                            '%name%'      => $entity->getName(),
                            '%menu_link%' => 'autoborna_sms_index',
                            '%url%'       => $this->generateUrl(
                                'autoborna_sms_action',
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
                        $returnUrl = $this->generateUrl('autoborna_sms_action', $viewParameters);
                        $template  = 'AutobornaSmsBundle:Sms:view';
                    } else {
                        //return edit view so that all the session stuff is loaded
                        return $this->editAction($entity->getId(), true);
                    }
                }
            } else {
                $viewParameters = ['page' => $page];
                $returnUrl      = $this->generateUrl('autoborna_sms_index', $viewParameters);
                $template       = 'AutobornaSmsBundle:Sms:index';
                //clear any modified content
                $session->remove('autoborna.sms.'.$entity->getId().'.content');
            }

            $passthrough = [
                'activeLink'    => 'autoborna_sms_index',
                'autobornaContent' => 'sms',
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
                    'form' => $this->setFormTheme($form, 'AutobornaSmsBundle:Sms:form.html.php', 'AutobornaSmsBundle:FormTheme\Sms'),
                    'sms'  => $entity,
                ],
                'contentTemplate' => 'AutobornaSmsBundle:Sms:form.html.php',
                'passthroughVars' => [
                    'activeLink'    => '#autoborna_sms_index',
                    'autobornaContent' => 'sms',
                    'updateSelect'  => InputHelper::clean($this->request->query->get('updateSelect')),
                    'route'         => $this->generateUrl(
                        'autoborna_sms_action',
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
        /** @var \Autoborna\SmsBundle\Model\SmsModel $model */
        $model   = $this->getModel('sms');
        $method  = $this->request->getMethod();
        $entity  = $model->getEntity($objectId);
        $session = $this->get('session');
        $page    = $session->get('autoborna.sms.page', 1);

        //set the return URL
        $returnUrl = $this->generateUrl('autoborna_sms_index', ['page' => $page]);

        $postActionVars = [
            'returnUrl'       => $returnUrl,
            'viewParameters'  => ['page' => $page],
            'contentTemplate' => 'AutobornaSmsBundle:Sms:index',
            'passthroughVars' => [
                'activeLink'    => 'autoborna_sms_index',
                'autobornaContent' => 'sms',
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
                                'msg'     => 'autoborna.sms.error.notfound',
                                'msgVars' => ['%id%' => $objectId],
                            ],
                        ],
                    ]
                )
            );
        } elseif (!$this->get('autoborna.security')->hasEntityAccess(
            'sms:smses:viewown',
            'sms:smses:viewother',
            $entity->getCreatedBy()
        )
        ) {
            return $this->accessDenied();
        } elseif ($model->isLocked($entity)) {
            //deny access if the entity is locked
            return $this->isLocked($postActionVars, $entity, 'sms');
        }

        //Create the form
        $action       = $this->generateUrl('autoborna_sms_action', ['objectAction' => 'edit', 'objectId' => $objectId]);
        $sms          = $this->request->request->get('sms', []);
        $updateSelect = 'POST' === $method
            ? ($sms['updateSelect'] ?? false)
            : $this->request->get('updateSelect', false);

        $form = $model->createForm($entity, $this->get('form.factory'), $action, ['update_select' => $updateSelect]);

        ///Check for a submitted form and process it
        if (!$ignorePost && 'POST' == $method) {
            $valid = false;
            if (!$cancelled = $this->isFormCancelled($form)) {
                if ($valid = $this->isFormValid($form)) {
                    //form is valid so process the data
                    $model->saveEntity($entity, $form->get('buttons')->get('save')->isClicked());

                    $this->addFlash(
                        'autoborna.core.notice.updated',
                        [
                            '%name%'      => $entity->getName(),
                            '%menu_link%' => 'autoborna_sms_index',
                            '%url%'       => $this->generateUrl(
                                'autoborna_sms_action',
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
                $session->remove('autoborna.sms.'.$objectId.'.content');
                //unlock the entity
                $model->unlockEntity($entity);
            }

            $passthrough = [
                'activeLink'    => 'autoborna_sms_index',
                'autobornaContent' => 'sms',
            ];

            $template = 'AutobornaSmsBundle:Sms:view';

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
                            'returnUrl'       => $this->generateUrl('autoborna_sms_action', $viewParameters),
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
                    'form'               => $this->setFormTheme($form, 'AutobornaSmsBundle:Sms:form.html.php', 'AutobornaSmsBundle:FormTheme\Sms'),
                    'sms'                => $entity,
                    'forceTypeSelection' => $forceTypeSelection,
                ],
                'contentTemplate' => 'AutobornaSmsBundle:Sms:form.html.php',
                'passthroughVars' => [
                    'activeLink'    => '#autoborna_sms_index',
                    'autobornaContent' => 'sms',
                    'updateSelect'  => InputHelper::clean($this->request->query->get('updateSelect')),
                    'route'         => $this->generateUrl(
                        'autoborna_sms_action',
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
        $model  = $this->getModel('sms');
        $entity = $model->getEntity($objectId);

        if (null != $entity) {
            if (!$this->get('autoborna.security')->isGranted('sms:smses:create')
                || !$this->get('autoborna.security')->hasEntityAccess(
                    'sms:smses:viewown',
                    'sms:smses:viewother',
                    $entity->getCreatedBy()
                )
            ) {
                return $this->accessDenied();
            }

            $entity = clone $entity;
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
        $page      = $this->get('session')->get('autoborna.sms.page', 1);
        $returnUrl = $this->generateUrl('autoborna_sms_index', ['page' => $page]);
        $flashes   = [];

        $postActionVars = [
            'returnUrl'       => $returnUrl,
            'viewParameters'  => ['page' => $page],
            'contentTemplate' => 'AutobornaSmsBundle:Sms:index',
            'passthroughVars' => [
                'activeLink'    => 'autoborna_sms_index',
                'autobornaContent' => 'sms',
            ],
        ];

        if ('POST' == $this->request->getMethod()) {
            $model  = $this->getModel('sms');
            $entity = $model->getEntity($objectId);

            if (null === $entity) {
                $flashes[] = [
                    'type'    => 'error',
                    'msg'     => 'autoborna.sms.error.notfound',
                    'msgVars' => ['%id%' => $objectId],
                ];
            } elseif (!$this->get('autoborna.security')->hasEntityAccess(
                'sms:smses:deleteown',
                'sms:smses:deleteother',
                $entity->getCreatedBy()
            )
            ) {
                return $this->accessDenied();
            } elseif ($model->isLocked($entity)) {
                return $this->isLocked($postActionVars, $entity, 'sms');
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
                ['flashes' => $flashes]
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
        $page      = $this->get('session')->get('autoborna.sms.page', 1);
        $returnUrl = $this->generateUrl('autoborna_sms_index', ['page' => $page]);
        $flashes   = [];

        $postActionVars = [
            'returnUrl'       => $returnUrl,
            'viewParameters'  => ['page' => $page],
            'contentTemplate' => 'AutobornaSmsBundle:Sms:index',
            'passthroughVars' => [
                'activeLink'    => '#autoborna_sms_index',
                'autobornaContent' => 'sms',
            ],
        ];

        if ('POST' == $this->request->getMethod()) {
            $model = $this->getModel('sms');
            $ids   = json_decode($this->request->query->get('ids', '{}'));

            $deleteIds = [];

            // Loop over the IDs to perform access checks pre-delete
            foreach ($ids as $objectId) {
                $entity = $model->getEntity($objectId);

                if (null === $entity) {
                    $flashes[] = [
                        'type'    => 'error',
                        'msg'     => 'autoborna.sms.error.notfound',
                        'msgVars' => ['%id%' => $objectId],
                    ];
                } elseif (!$this->get('autoborna.security')->hasEntityAccess(
                    'sms:smses:viewown',
                    'sms:smses:viewother',
                    $entity->getCreatedBy()
                )
                ) {
                    $flashes[] = $this->accessDenied(true);
                } elseif ($model->isLocked($entity)) {
                    $flashes[] = $this->isLocked($postActionVars, $entity, 'sms', true);
                } else {
                    $deleteIds[] = $objectId;
                }
            }

            // Delete everything we are able to
            if (!empty($deleteIds)) {
                $entities = $model->deleteEntities($deleteIds);

                $flashes[] = [
                    'type'    => 'notice',
                    'msg'     => 'autoborna.sms.notice.batch_deleted',
                    'msgVars' => [
                        '%count%' => count($entities),
                    ],
                ];
            }
        } //else don't do anything

        return $this->postActionRedirect(
            array_merge(
                $postActionVars,
                ['flashes' => $flashes]
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
        /** @var \Autoborna\SmsBundle\Model\SmsModel $model */
        $model    = $this->getModel('sms');
        $sms      = $model->getEntity($objectId);
        $security = $this->get('autoborna.security');

        if (null !== $sms && $security->hasEntityAccess('sms:smses:viewown', 'sms:smses:viewother')) {
            return $this->delegateView([
                'viewParameters' => [
                    'sms' => $sms,
                ],
                'contentTemplate' => 'AutobornaSmsBundle:Sms:preview.html.php',
            ]);
        }

        return new Response('', Response::HTTP_NOT_FOUND);
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
            'sms:smses:view',
            'sms',
            'sms_message_stats',
            'sms',
            'sms_id'
        );
    }

    protected function getModelName(): string
    {
        return 'sms';
    }

    protected function getDefaultOrderDirection(): string
    {
        return 'DESC';
    }
}
