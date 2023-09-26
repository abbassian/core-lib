<?php

namespace Autoborna\LeadBundle\Controller;

use Doctrine\ORM\EntityNotFoundException;
use Exception;
use Autoborna\CoreBundle\Controller\FormController;
use Autoborna\CoreBundle\Form\Type\DateRangeType;
use Autoborna\CoreBundle\Helper\InputHelper;
use Autoborna\LeadBundle\Entity\LeadList;
use Autoborna\LeadBundle\Model\LeadModel;
use Autoborna\LeadBundle\Model\ListModel;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Translation\TranslatorInterface;

class ListController extends FormController
{
    use EntityContactsTrait;

    const ROUTE_SEGMENT_CONTACTS = 'autoborna_segment_contacts';

    const SEGMENT_CONTACT_FIELDS = ['id', 'company', 'city', 'state', 'country'];

    /**
     * @var array
     */
    protected $listFilters = [];

    /**
     * Generate's default list view.
     *
     * @param int $page
     *
     * @return JsonResponse|Response
     *
     * @throws Exception
     */
    public function indexAction($page = 1)
    {
        /** @var ListModel $model */
        $model   = $this->getModel('lead.list');
        $session = $this->get('session');

        //set some permissions
        $permissions = $this->get('autoborna.security')->isGranted([
            'lead:leads:viewown',
            'lead:leads:viewother',
            'lead:lists:viewother',
            'lead:lists:editother',
            'lead:lists:deleteother',
        ], 'RETURN_ARRAY');

        //Lists can be managed by anyone who has access to leads
        if (!$permissions['lead:leads:viewown'] && !$permissions['lead:leads:viewother']) {
            return $this->accessDenied();
        }

        $this->setListFilters();

        //set limits
        $limit = $session->get('autoborna.lead.list.limit', $this->coreParametersHelper->get('default_pagelimit'));
        $start = (1 === $page) ? 0 : (($page - 1) * $limit);
        if ($start < 0) {
            $start = 0;
        }

        $search = $this->request->get('search', $session->get('autoborna.segment.filter', ''));
        $session->set('autoborna.segment.filter', $search);

        //do some default filtering
        $orderBy    = $session->get('autoborna.lead.list.orderby', 'l.dateModified');
        $orderByDir = $session->get('autoborna.lead.list.orderbydir', $this->getDefaultOrderDirection());

        $filter = [
            'string' => $search,
        ];

        $tmpl = $this->request->isXmlHttpRequest() ? $this->request->get('tmpl', 'index') : 'index';

        if (!$permissions['lead:lists:viewother']) {
            $translator      = $this->get('translator');
            $mine            = $translator->trans('autoborna.core.searchcommand.ismine');
            $global          = $translator->trans('autoborna.lead.list.searchcommand.isglobal');
            $filter['force'] = "($mine or $global)";
        }

        [$count, $items] = $this->getIndexItems($start, $limit, $filter, $orderBy, $orderByDir);

        if ($count && $count < ($start + 1)) {
            //the number of entities are now less then the current page so redirect to the last page
            if (1 === $count) {
                $lastPage = 1;
            } else {
                $lastPage = (ceil($count / $limit)) ?: 1;
            }
            $session->set('autoborna.segment.page', $lastPage);
            $returnUrl = $this->generateUrl('autoborna_segment_index', ['page' => $lastPage]);

            return $this->postActionRedirect([
                'returnUrl'      => $returnUrl,
                'viewParameters' => [
                    'page' => $lastPage,
                    'tmpl' => $tmpl,
                ],
                'contentTemplate' => 'AutobornaLeadBundle:List:index',
                'passthroughVars' => [
                    'activeLink'    => '#autoborna_segment_index',
                    'autobornaContent' => 'leadlist',
                ],
            ]);
        }

        //set what page currently on so that we can return here after form submission/cancellation
        $session->set('autoborna.segment.page', $page);

        $listIds    = array_keys($items->getIterator()->getArrayCopy());
        $leadCounts = (!empty($listIds)) ? $model->getSegmentContactCountFromCache($listIds) : [];

        $parameters = [
            'items'                          => $items,
            'leadCounts'                     => $leadCounts,
            'page'                           => $page,
            'limit'                          => $limit,
            'permissions'                    => $permissions,
            'security'                       => $this->get('autoborna.security'),
            'tmpl'                           => $tmpl,
            'currentUser'                    => $this->user,
            'searchValue'                    => $search,
            'segmentRebuildWarningThreshold' => $this->coreParametersHelper->get('segment_rebuild_time_warning'),
        ];

        return $this->delegateView(
            $this->getViewArguments([
                'viewParameters'  => $parameters,
                'contentTemplate' => 'AutobornaLeadBundle:List:list.html.php',
                'passthroughVars' => [
                    'activeLink'    => '#autoborna_segment_index',
                    'route'         => $this->generateUrl('autoborna_segment_index', ['page' => $page]),
                    'autobornaContent' => 'leadlist',
                ],
            ],
            'index'
            )
        );
    }

    /**
     * Generate's new form and processes post data.
     *
     * @return JsonResponse|RedirectResponse|Response
     */
    public function newAction()
    {
        if (!$this->get('autoborna.security')->isGranted('lead:leads:viewown')) {
            return $this->accessDenied();
        }

        //retrieve the entity
        $list = new LeadList();
        /** @var ListModel $model */
        $model = $this->getModel('lead.list');
        //set the page we came from
        $page = $this->get('session')->get('autoborna.segment.page', 1);
        //set the return URL for post actions
        $returnUrl = $this->generateUrl('autoborna_segment_index', ['page' => $page]);
        $action    = $this->generateUrl('autoborna_segment_action', ['objectAction' => 'new']);

        //get the user form factory
        $form = $model->createForm($list, $this->get('form.factory'), $action);

        ///Check for a submitted form and process it
        if ('POST' == $this->request->getMethod()) {
            $valid = false;
            if (!$cancelled = $this->isFormCancelled($form)) {
                if ($valid = $this->isFormValid($form)) {
                    //form is valid so process the data
                    $list->setDateModified(new \DateTime());
                    $model->saveEntity($list);

                    $this->addFlash('autoborna.core.notice.created', [
                        '%name%'      => $list->getName().' ('.$list->getAlias().')',
                        '%menu_link%' => 'autoborna_segment_index',
                        '%url%'       => $this->generateUrl('autoborna_segment_action', [
                            'objectAction' => 'edit',
                            'objectId'     => $list->getId(),
                        ]),
                    ]);
                }
            }

            if ($cancelled || ($valid && $form->get('buttons')->get('save')->isClicked())) {
                return $this->postActionRedirect([
                    'returnUrl'       => $returnUrl,
                    'viewParameters'  => ['page' => $page],
                    'contentTemplate' => 'AutobornaLeadBundle:List:index',
                    'passthroughVars' => [
                        'activeLink'    => '#autoborna_segment_index',
                        'autobornaContent' => 'leadlist',
                    ],
                ]);
            } elseif ($valid && !$cancelled) {
                return $this->editAction($list->getId(), true);
            }
        }

        return $this->delegateView([
            'viewParameters' => [
                'form' => $this->setFormTheme($form, 'AutobornaLeadBundle:List:form.html.php', 'AutobornaLeadBundle:FormTheme\Filter'),
            ],
            'contentTemplate' => 'AutobornaLeadBundle:List:form.html.php',
            'passthroughVars' => [
                'activeLink'    => '#autoborna_segment_index',
                'route'         => $this->generateUrl('autoborna_segment_action', ['objectAction' => 'new']),
                'autobornaContent' => 'leadlist',
            ],
        ]);
    }

    /**
     * Generate's clone form and processes post data.
     *
     * @param int  $objectId
     * @param bool $ignorePost
     *
     * @return Response
     */
    public function cloneAction($objectId, $ignorePost = false)
    {
        $postActionVars = $this->getPostActionVars();

        try {
            $segment = $this->getSegment($objectId);

            return $this->createSegmentModifyResponse(
                clone $segment,
                $postActionVars,
                $this->generateUrl('autoborna_segment_action', ['objectAction' => 'clone', 'objectId' => $objectId]),
                $ignorePost
            );
        } catch (AccessDeniedException $exception) {
            return $this->accessDenied();
        } catch (EntityNotFoundException $exception) {
            return $this->postActionRedirect(
                array_merge($postActionVars, [
                    'flashes' => [
                        [
                            'type'    => 'error',
                            'msg'     => 'autoborna.lead.list.error.notfound',
                            'msgVars' => ['%id%' => $objectId],
                        ],
                    ],
                ])
            );
        }
    }

    /**
     * Generate's edit form and processes post data.
     *
     * @param int  $objectId
     * @param bool $ignorePost
     *
     * @return Response
     */
    public function editAction($objectId, $ignorePost = false, bool $isNew = false)
    {
        $postActionVars = $this->getPostActionVars($objectId);

        try {
            $segment = $this->getSegment($objectId);

            if ($isNew) {
                $segment->setNew();
            }

            return $this->createSegmentModifyResponse(
                $segment,
                $postActionVars,
                $this->generateUrl('autoborna_segment_action', ['objectAction' => 'edit', 'objectId' => $objectId]),
                $ignorePost
            );
        } catch (AccessDeniedException $exception) {
            return $this->accessDenied();
        } catch (EntityNotFoundException $exception) {
            return $this->postActionRedirect(
                array_merge($postActionVars, [
                    'flashes' => [
                        [
                            'type'    => 'error',
                            'msg'     => 'autoborna.lead.list.error.notfound',
                            'msgVars' => ['%id%' => $objectId],
                        ],
                    ],
                ])
            );
        }
    }

    /**
     * Create modifying response for segments - edit/clone.
     *
     * @param string $action
     * @param bool   $ignorePost
     *
     * @return Response
     */
    private function createSegmentModifyResponse(LeadList $segment, array $postActionVars, $action, $ignorePost)
    {
        /** @var ListModel $segmentModel */
        $segmentModel = $this->getModel('lead.list');

        if ($segmentModel->isLocked($segment)) {
            return $this->isLocked($postActionVars, $segment, 'lead.list');
        }

        /** @var FormInterface $form */
        $form = $segmentModel->createForm($segment, $this->get('form.factory'), $action);

        ///Check for a submitted form and process it
        if (!$ignorePost && 'POST' == $this->request->getMethod()) {
            if (!$cancelled = $this->isFormCancelled($form)) {
                if ($this->isFormValid($form)) {
                    //form is valid so process the data
                    $segmentModel->saveEntity($segment, $form->get('buttons')->get('save')->isClicked());

                    $this->addFlash('autoborna.core.notice.updated', [
                        '%name%'      => $segment->getName().' ('.$segment->getAlias().')',
                        '%menu_link%' => 'autoborna_segment_index',
                        '%url%'       => $this->generateUrl('autoborna_segment_action', [
                            'objectAction' => 'edit',
                            'objectId'     => $segment->getId(),
                        ]),
                    ]);

                    if ($form->get('buttons')->get('apply')->isClicked()) {
                        $contentTemplate                     = 'AutobornaLeadBundle:List:form.html.php';
                        $postActionVars['contentTemplate']   = $contentTemplate;
                        $postActionVars['forwardController'] = false;
                        $postActionVars['returnUrl']         = $this->generateUrl('autoborna_segment_action', [
                            'objectAction' => 'edit',
                            'objectId'     => $segment->getId(),
                        ]);

                        $postActionVars['viewParameters'] = [
                            'objectAction' => 'edit',
                            'objectId'     => $segment->getId(),
                            'form'         => $this->setFormTheme($form, $contentTemplate, 'AutobornaLeadBundle:FormTheme\Filter'),
                        ];

                        return $this->postActionRedirect($postActionVars);
                    } else {
                        return $this->viewAction($segment->getId());
                    }
                }
            } else {
                //unlock the entity
                $segmentModel->unlockEntity($segment);
            }

            if ($cancelled) {
                return $this->postActionRedirect($postActionVars);
            }
        } else {
            //lock the entity
            $segmentModel->lockEntity($segment);
        }

        return $this->delegateView([
            'viewParameters' => [
                'form'          => $this->setFormTheme($form, 'AutobornaLeadBundle:List:form.html.php', 'AutobornaLeadBundle:FormTheme\Filter'),
                'currentListId' => $segment->getId(),
            ],
            'contentTemplate' => 'AutobornaLeadBundle:List:form.html.php',
            'passthroughVars' => [
                'activeLink'    => '#autoborna_segment_index',
                'route'         => $action,
                'autobornaContent' => 'leadlist',
            ],
        ]);
    }

    /**
     * Return segment if exists and user has access.
     *
     * @param int $segmentId
     *
     * @return LeadList
     *
     * @throws EntityNotFoundException
     * @throws AccessDeniedException
     */
    private function getSegment($segmentId)
    {
        /** @var LeadList $segment */
        $segment = $this->getModel('lead.list')->getEntity($segmentId);

        // Check if exists
        if (!$segment instanceof LeadList) {
            throw new EntityNotFoundException(sprintf('Segment with id %d not found.', $segmentId));
        }

        if (!$this->get('autoborna.security')->hasEntityAccess(
            true, 'lead:lists:editother', $segment->getCreatedBy()
        )) {
            throw new AccessDeniedException(sprintf('User has not access on segment with id %d', $segmentId));
        }

        return $segment;
    }

    /**
     * Get variables for POST action.
     *
     * @param null $objectId
     *
     * @return array
     */
    private function getPostActionVars($objectId = null)
    {
        //set the return URL
        if ($objectId) {
            $returnUrl       = $this->generateUrl('autoborna_segment_action', ['objectAction' => 'view', 'objectId'=> $objectId]);
            $viewParameters  = ['objectAction' => 'view', 'objectId'=> $objectId];
            $contentTemplate = 'AutobornaLeadBundle:List:view';
        } else {
            //set the page we came from
            $page            = $this->get('session')->get('autoborna.segment.page', 1);
            $returnUrl       = $this->generateUrl('autoborna_segment_index', ['page' => $page]);
            $viewParameters  = ['page' => $page];
            $contentTemplate = 'AutobornaLeadBundle:List:index';
        }

        return [
            'returnUrl'       => $returnUrl,
            'viewParameters'  => $viewParameters,
            'contentTemplate' => $contentTemplate,
            'passthroughVars' => [
                'activeLink'    => '#autoborna_segment_index',
                'autobornaContent' => 'leadlist',
            ],
        ];
    }

    /**
     * Delete a list.
     *
     * @param $objectId
     *
     * @return JsonResponse|RedirectResponse
     */
    public function deleteAction($objectId)
    {
        /** @var ListModel $model */
        $model     = $this->getModel('lead.list');
        $page      = $this->get('session')->get('autoborna.segment.page', 1);
        $returnUrl = $this->generateUrl('autoborna_segment_index', ['page' => $page]);
        $flashes   = [];

        $postActionVars = [
            'returnUrl'       => $returnUrl,
            'viewParameters'  => ['page' => $page],
            'contentTemplate' => 'AutobornaLeadBundle:List:index',
            'passthroughVars' => [
                'activeLink'    => '#autoborna_segment_index',
                'autobornaContent' => 'lead',
            ],
        ];

        $dependents = $model->getSegmentsWithDependenciesOnSegment($objectId);

        if (!empty($dependents)) {
            $flashes[] = [
                    'type'    => 'error',
                    'msg'     => 'autoborna.lead.list.error.cannot.delete',
                    'msgVars' => ['%segments%' => implode(', ', $dependents)],
                ];

            return $this->postActionRedirect(
                array_merge($postActionVars, [
                    'flashes' => $flashes,
                ])
            );
        }

        if ('POST' == $this->request->getMethod()) {
            /** @var ListModel $model */
            $model = $this->getModel('lead.list');
            $list  = $model->getEntity($objectId);

            if (null === $list) {
                $flashes[] = [
                    'type'    => 'error',
                    'msg'     => 'autoborna.lead.list.error.notfound',
                    'msgVars' => ['%id%' => $objectId],
                ];
            } elseif (!$this->get('autoborna.security')->hasEntityAccess(
                true, 'lead:lists:deleteother', $list->getCreatedBy()
            )
            ) {
                return $this->accessDenied();
            } elseif ($model->isLocked($list)) {
                return $this->isLocked($postActionVars, $list, 'lead.list');
            }

            $model->deleteEntity($list);

            $flashes[] = [
                'type'    => 'notice',
                'msg'     => 'autoborna.core.notice.deleted',
                'msgVars' => [
                    '%name%' => $list->getName(),
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
     * @return JsonResponse|RedirectResponse
     */
    public function batchDeleteAction()
    {
        $page      = $this->get('session')->get('autoborna.segment.page', 1);
        $returnUrl = $this->generateUrl('autoborna_segment_index', ['page' => $page]);
        $flashes   = [];

        $postActionVars = [
            'returnUrl'       => $returnUrl,
            'viewParameters'  => ['page' => $page],
            'contentTemplate' => 'AutobornaLeadBundle:List:index',
            'passthroughVars' => [
                'activeLink'    => '#autoborna_segment_index',
                'autobornaContent' => 'lead',
            ],
        ];

        if ('POST' == $this->request->getMethod()) {
            /** @var ListModel $model */
            $model           = $this->getModel('lead.list');
            $ids             = json_decode($this->request->query->get('ids', '{}'));
            $canNotBeDeleted = $model->canNotBeDeleted($ids);

            if (!empty($canNotBeDeleted)) {
                $flashes[] = [
                    'type'    => 'error',
                    'msg'     => 'autoborna.lead.list.error.cannot.delete.batch',
                    'msgVars' => ['%segments%' => implode(', ', $canNotBeDeleted)],
                ];
            }

            $toBeDeleted = array_diff($ids, array_keys($canNotBeDeleted));
            $deleteIds   = [];

            // Loop over the IDs to perform access checks pre-delete
            foreach ($toBeDeleted as $objectId) {
                $entity = $model->getEntity($objectId);

                if (null === $entity) {
                    $flashes[] = [
                        'type'    => 'error',
                        'msg'     => 'autoborna.lead.list.error.notfound',
                        'msgVars' => ['%id%' => $objectId],
                    ];
                } elseif (!$this->get('autoborna.security')->hasEntityAccess(
                    true, 'lead:lists:deleteother', $entity->getCreatedBy()
                )) {
                    $flashes[] = $this->accessDenied(true);
                } elseif ($model->isLocked($entity)) {
                    $flashes[] = $this->isLocked($postActionVars, $entity, 'lead.list', true);
                } else {
                    $deleteIds[] = $objectId;
                }
            }

            // Delete everything we are able to
            if (!empty($deleteIds)) {
                $entities = $model->deleteEntities($deleteIds);

                $flashes[] = [
                    'type'    => 'notice',
                    'msg'     => 'autoborna.lead.list.notice.batch_deleted',
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
     * @param $objectId
     *
     * @return JsonResponse|RedirectResponse
     */
    public function removeLeadAction($objectId)
    {
        return $this->changeList($objectId, 'remove');
    }

    /**
     * @param $objectId
     *
     * @return JsonResponse|RedirectResponse
     */
    public function addLeadAction($objectId)
    {
        return $this->changeList($objectId, 'add');
    }

    /**
     * @param $listId
     * @param $action
     *
     * @return array|JsonResponse|RedirectResponse
     */
    protected function changeList($listId, $action)
    {
        $page      = $this->get('session')->get('autoborna.lead.page', 1);
        $returnUrl = $this->generateUrl('autoborna_contact_index', ['page' => $page]);
        $flashes   = [];

        $postActionVars = [
            'returnUrl'       => $returnUrl,
            'viewParameters'  => ['page' => $page],
            'contentTemplate' => 'AutobornaLeadBundle:Lead:index',
            'passthroughVars' => [
                'activeLink'    => '#autoborna_contact_index',
                'autobornaContent' => 'lead',
            ],
        ];

        $leadId = $this->request->get('leadId');
        if (!empty($leadId) && 'POST' == $this->request->getMethod()) {
            /** @var ListModel $model */
            $model = $this->getModel('lead.list');
            /** @var LeadList $list */
            $list = $model->getEntity($listId);
            /** @var LeadModel $leadModel */
            $leadModel = $this->getModel('lead');
            $lead      = $leadModel->getEntity($leadId);

            if (null === $lead) {
                $flashes[] = [
                    'type'    => 'error',
                    'msg'     => 'autoborna.lead.lead.error.notfound',
                    'msgVars' => ['%id%' => $listId],
                ];
            } elseif (!$this->get('autoborna.security')->hasEntityAccess(
                'lead:leads:editown', 'lead:leads:editother', $lead->getPermissionUser()
            )) {
                return $this->accessDenied();
            } elseif (null === $list) {
                $flashes[] = [
                    'type'    => 'error',
                    'msg'     => 'autoborna.lead.list.error.notfound',
                    'msgVars' => ['%id%' => $list->getId()],
                ];
            } elseif (!$list->isGlobal() && !$this->get('autoborna.security')->hasEntityAccess(
                    true, 'lead:lists:viewother', $list->getCreatedBy()
                )) {
                return $this->accessDenied();
            } elseif ($model->isLocked($lead)) {
                return $this->isLocked($postActionVars, $lead, 'lead');
            } else {
                $function = ('remove' == $action) ? 'removeLead' : 'addLead';
                $model->$function($lead, $list, true);

                $identifier = $this->get('translator')->trans($lead->getPrimaryIdentifier());
                $flashes[]  = [
                    'type' => 'notice',
                    'msg'  => ('remove' == $action) ? 'autoborna.lead.lead.notice.removedfromlists' :
                        'autoborna.lead.lead.notice.addedtolists',
                    'msgVars' => [
                        '%name%' => $identifier,
                        '%id%'   => $leadId,
                        '%list%' => $list->getName(),
                        '%url%'  => $this->generateUrl('autoborna_contact_action', [
                            'objectAction' => 'edit',
                            'objectId'     => $leadId,
                        ]),
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
     * Loads a specific form into the detailed panel.
     *
     * @param $objectId
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function viewAction($objectId)
    {
        /** @var \Autoborna\LeadBundle\Model\ListModel $model */
        $model    = $this->getModel('lead.list');
        $security = $this->get('autoborna.security');

        /** @var LeadList $list */
        $list = $model->getEntity($objectId);
        //set the page we came from
        $page = $this->get('session')->get('autoborna.segment.page', 1);

        if ('POST' === $this->request->getMethod() && $this->request->request->has('includeEvents')) {
            $filters = [
                'includeEvents' => InputHelper::clean($this->request->get('includeEvents', [])),
            ];
            $this->get('session')->set('autoborna.segment.filters', $filters);
        } else {
            $filters = [];
        }

        if (null === $list) {
            //set the return URL
            $returnUrl = $this->generateUrl('autoborna_segment_index', ['page' => $page]);

            return $this->postActionRedirect([
                'returnUrl'       => $returnUrl,
                'viewParameters'  => ['page' => $page],
                'contentTemplate' => 'AutobornaLeadBundle:List:index',
                'passthroughVars' => [
                    'activeLink'    => '#autoborna_segment_index',
                    'autobornaContent' => 'list',
                ],
                'flashes' => [
                    [
                        'type'    => 'error',
                        'msg'     => 'autoborna.list.error.notfound',
                        'msgVars' => ['%id%' => $objectId],
                    ],
                ],
            ]);
        } elseif (!$this->get('autoborna.security')->hasEntityAccess(
            'lead:leads:viewown',
            'lead:lists:viewother',
            $list->getCreatedBy()
        )
        ) {
            return $this->accessDenied();
        }
        /** @var TranslatorInterface $translator */
        $translator = $this->get('translator');
        /** @var ListModel $listModel */
        $listModel                    = $this->getModel('lead.list');
        $dateRangeValues              = $this->request->get('daterange', []);
        $action                       = $this->generateUrl('autoborna_segment_action', ['objectAction' => 'view', 'objectId' => $objectId]);
        $dateRangeForm                = $this->get('form.factory')->create(DateRangeType::class, $dateRangeValues, ['action' => $action]);
        $segmentContactsLineChartData = $listModel->getSegmentContactsLineChartData(
            null,
            new \DateTime($dateRangeForm->get('date_from')->getData()),
            new \DateTime($dateRangeForm->get('date_to')->getData()),
            null,
            [
                'leadlist_id'   => [
                    'value'            => $objectId,
                    'list_column_name' => 't.lead_id',
                ],
                't.leadlist_id' => $objectId,
            ]
        );

        return $this->delegateView([
            'returnUrl'      => $this->generateUrl('autoborna_segment_action', ['objectAction' => 'view', 'objectId' => $list->getId()]),
            'viewParameters' => [
                'usageStats'     => $this->get('autoborna.lead.segment.stat.dependencies')->getChannelsIds($list->getId()),
                'campaignStats'  => $this->get('autoborna.lead.segment.stat.campaign.share')->getCampaignList($list->getId()),
                'stats'          => $segmentContactsLineChartData,
                'list'           => $list,
                'segmentCount'   => $listModel->getRepository()->getLeadCount($list->getId()),
                'permissions'    => $security->isGranted([
                    'lead:leads:editown',
                    'lead:lists:viewother',
                    'lead:lists:editother',
                    'lead:lists:deleteother',
                ], 'RETURN_ARRAY'),
                'security'      => $security,
                'dateRangeForm' => $dateRangeForm->createView(),
                'events'        => [
                    'filters' => $filters,
                    'types'   => [
                        'manually_added'   => $translator->trans('autoborna.segment.contact.manually.added'),
                        'manually_removed' => $translator->trans('autoborna.segment.contact.manually.removed'),
                        'filter_added'     => $translator->trans('autoborna.segment.contact.filter.added'),
                    ],
                ],
            ],
            'contentTemplate' => 'AutobornaLeadBundle:List:details.html.php',
            'passthroughVars' => [
                'activeLink'    => '#autoborna_segment_index',
                'autobornaContent' => 'list',
            ],
        ]);
    }

    /**
     * Get the permission base from the model.
     */
    protected function getPermissionBase(): string
    {
        return $this->getModel('lead.list')->getPermissionBase();
    }

    /**
     * Get List Model.
     */
    protected function getListModel(): ListModel
    {
        /** @var ListModel $model */
        $model = $this->getModel('lead.list');

        return $model;
    }

    /**
     * Get Model Name.
     */
    protected function getModelName(): string
    {
        return 'lead.list';
    }

    /**
     * @param $start
     * @param $limit
     * @param $filter
     * @param $orderBy
     * @param $orderByDir
     */
    protected function getIndexItems($start, $limit, $filter, $orderBy, $orderByDir, array $args = []): array
    {
        $session        = $this->get('session');
        $currentFilters = $session->get('autoborna.lead.list.list_filters', []);
        $updatedFilters = $this->request->get('filters', false);

        $sourceLists = $this->getListModel()->getSourceLists();
        $listFilters = [
            'filters' => [
                'placeholder' => $this->get('translator')->trans('autoborna.lead.list.filter.placeholder'),
                'multiple'    => true,
                'groups'      => [
                    'autoborna.lead.list.source.segment.category' => [
                        'options' => $sourceLists['categories'],
                        'prefix'  => 'category',
                    ],
                ],
            ],
        ];

        if ($updatedFilters) {
            // Filters have been updated

            // Parse the selected values
            $newFilters     = [];
            $updatedFilters = json_decode($updatedFilters, true);

            if ($updatedFilters) {
                foreach ($updatedFilters as $updatedFilter) {
                    [$clmn, $fltr] = explode(':', $updatedFilter);

                    $newFilters[$clmn][] = $fltr;
                }

                $currentFilters = $newFilters;
            } else {
                $currentFilters = [];
            }
        }
        $session->set('autoborna.lead.list.list_filters', $currentFilters);

        $joinCategories = false;
        if (!empty($currentFilters)) {
            $catIds = [];
            foreach ($currentFilters as $type => $typeFilters) {
                $listFilters['filters']['groups']['autoborna.lead.list.source.segment.'.$type]['values'] = $typeFilters;

                foreach ($typeFilters as $fltr) {
                    if ('category' == $type) {
                        $catIds[] = (int) $fltr;
                    } // else for other group filters
                }
            }

            if (!empty($catIds)) {
                $joinCategories    = true;
                $filter['force'][] = ['column' => 'cat.id', 'expr' => 'in', 'value' => $catIds];
            }
        }

        // Store for customizeViewArguments
        $this->listFilters = $listFilters;

        return parent::getIndexItems(
            $start,
            $limit,
            $filter,
            $orderBy,
            $orderByDir,
            [
                'joinCategories' => $joinCategories,
            ]
        );
    }

    /**
     * @param $action
     */
    public function getViewArguments(array $args, $action): array
    {
        switch ($action) {
            case 'index':
                $args['viewParameters']['filters'] = $this->listFilters;
                break;
        }

        return $args;
    }

    /**
     * @param int $objectId
     * @param int $page
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function contactsAction($objectId, $page = 1)
    {
        $session = $this->get('session');
        \assert($session instanceof SessionInterface);
        $session->set('autoborna.segment.contact.page', $page);

        $manuallyRemoved = 0;
        $listFilters     = ['manually_removed' => $manuallyRemoved];
        if ('POST' === $this->request->getMethod() && $this->request->request->has('includeEvents')) {
            $filters = [
                'includeEvents' => InputHelper::clean($this->request->get('includeEvents', [])),
            ];
            $this->get('session')->set('autoborna.segment.filters', $filters);
        } else {
            $filters = [];
        }

        if (!empty($filters)) {
            if (isset($filters['includeEvents']) && in_array('manually_added', $filters['includeEvents'])) {
                $listFilters = array_merge($listFilters, ['manually_added' => 1]);
            }
            if (isset($filters['includeEvents']) && in_array('manually_removed', $filters['includeEvents'])) {
                $listFilters = array_merge($listFilters, ['manually_removed' => 1]);
            }
            if (isset($filters['includeEvents']) && in_array('filter_added', $filters['includeEvents'])) {
                $listFilters = array_merge($listFilters, ['manually_added' => 0]);
            }
        }

        return $this->generateContactsGrid(
            $objectId,
            $page,
            ['lead:leads:viewother', 'lead:leads:viewown'],
            'segment',
            'lead_lists_leads',
            null,
            'leadlist_id',
            $listFilters
        );
    }

    protected function getDefaultOrderDirection()
    {
        return 'DESC';
    }
}
