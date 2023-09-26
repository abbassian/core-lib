<?php

namespace Autoborna\FormBundle\Controller;

use Autoborna\CoreBundle\Controller\FormController as CommonFormController;
use Autoborna\CoreBundle\Factory\PageHelperFactoryInterface;
use Autoborna\CoreBundle\Form\Type\DateRangeType;
use Autoborna\FormBundle\Entity\Field;
use Autoborna\FormBundle\Entity\Form;
use Autoborna\FormBundle\Exception\ValidationException;
use Autoborna\FormBundle\Helper\FormFieldHelper;
use Autoborna\FormBundle\Model\FormModel;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Response;

class FormController extends CommonFormController
{
    /**
     * @param int $page
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function indexAction($page = 1)
    {
        //set some permissions
        $permissions = $this->get('autoborna.security')->isGranted(
            [
                'form:forms:viewown',
                'form:forms:viewother',
                'form:forms:create',
                'form:forms:editown',
                'form:forms:editother',
                'form:forms:deleteown',
                'form:forms:deleteother',
                'form:forms:publishown',
                'form:forms:publishother',
            ],
            'RETURN_ARRAY'
        );

        if (!$permissions['form:forms:viewown'] && !$permissions['form:forms:viewother']) {
            return $this->accessDenied();
        }

        $this->setListFilters();

        $session = $this->get('session');

        /** @var PageHelperFactoryInterface $pageHelperFacotry */
        $pageHelperFacotry = $this->get('autoborna.page.helper.factory');
        $pageHelper        = $pageHelperFacotry->make('autoborna.form', $page);
        $limit             = $pageHelper->getLimit();
        $start             = $pageHelper->getStart();
        $search            = $this->request->get('search', $session->get('autoborna.form.filter', ''));
        $filter            = ['string' => $search, 'force' => []];
        $session->set('autoborna.form.filter', $search);

        if (!$permissions['form:forms:viewother']) {
            $filter['force'][] = ['column' => 'f.createdBy', 'expr' => 'eq', 'value' => $this->user->getId()];
        }

        $orderBy    = $session->get('autoborna.form.orderby', 'f.dateModified');
        $orderByDir = $session->get('autoborna.form.orderbydir', $this->getDefaultOrderDirection());
        $forms      = $this->getModel('form.form')->getEntities(
            [
                'start'      => $start,
                'limit'      => $limit,
                'filter'     => $filter,
                'orderBy'    => $orderBy,
                'orderByDir' => $orderByDir,
            ]
        );

        $count = count($forms);

        if ($count && $count < ($start + 1)) {
            //the number of entities are now less then the current page so redirect to the last page
            $lastPage = $pageHelper->countPage($count);
            $pageHelper->rememberPage($lastPage);
            $returnUrl = $this->generateUrl('autoborna_form_index', ['page' => $lastPage]);

            return $this->postActionRedirect(
                [
                    'returnUrl'       => $returnUrl,
                    'viewParameters'  => ['page' => $lastPage],
                    'contentTemplate' => 'AutobornaFormBundle:Form:index',
                    'passthroughVars' => [
                        'activeLink'    => '#autoborna_form_index',
                        'autobornaContent' => 'form',
                    ],
                ]
            );
        }

        $pageHelper->rememberPage($page);

        return $this->delegateView(
            [
                'viewParameters'  => [
                    'searchValue' => $search,
                    'items'       => $forms,
                    'totalItems'  => $count,
                    'page'        => $page,
                    'limit'       => $limit,
                    'permissions' => $permissions,
                    'security'    => $this->get('autoborna.security'),
                    'tmpl'        => $this->request->get('tmpl', 'index'),
                ],
                'contentTemplate' => 'AutobornaFormBundle:Form:list.html.php',
                'passthroughVars' => [
                    'activeLink'    => '#autoborna_form_index',
                    'autobornaContent' => 'form',
                    'route'         => $this->generateUrl('autoborna_form_index', ['page' => $page]),
                ],
            ]
        );
    }

    /**
     * Loads a specific form into the detailed panel.
     *
     * @param int $objectId
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function viewAction($objectId)
    {
        /** @var \Autoborna\FormBundle\Model\FormModel $model */
        $model      = $this->getModel('form');
        $activeForm = $model->getEntity($objectId);

        //set the page we came from
        $page = $this->get('session')->get('autoborna.form.page', 1);

        if (null === $activeForm) {
            //set the return URL
            $returnUrl = $this->generateUrl('autoborna_form_index', ['page' => $page]);

            return $this->postActionRedirect(
                [
                    'returnUrl'       => $returnUrl,
                    'viewParameters'  => ['page' => $page],
                    'contentTemplate' => 'AutobornaFormBundle:Form:index',
                    'passthroughVars' => [
                        'activeLink'    => '#autoborna_form_index',
                        'autobornaContent' => 'form',
                    ],
                    'flashes' => [
                        [
                            'type'    => 'error',
                            'msg'     => 'autoborna.form.error.notfound',
                            'msgVars' => ['%id%' => $objectId],
                        ],
                    ],
                ]
            );
        } elseif (!$this->get('autoborna.security')->hasEntityAccess(
            'form:forms:viewown',
            'form:forms:viewother',
            $activeForm->getCreatedBy()
        )
        ) {
            return $this->accessDenied();
        }

        $permissions = $this->get('autoborna.security')->isGranted(
            [
                'form:forms:viewown',
                'form:forms:viewother',
                'form:forms:create',
                'form:forms:editown',
                'form:forms:editother',
                'form:forms:deleteown',
                'form:forms:deleteother',
                'form:forms:publishown',
                'form:forms:publishother',
            ],
            'RETURN_ARRAY'
        );

        // Audit Log
        $logs = $this->getModel('core.auditlog')->getLogForObject('form', $objectId, $activeForm->getDateAdded());

        // Init the date range filter form
        $dateRangeValues = $this->request->get('daterange', []);
        $action          = $this->generateUrl('autoborna_form_action', ['objectAction' => 'view', 'objectId' => $objectId]);
        $dateRangeForm   = $this->get('form.factory')->create(DateRangeType::class, $dateRangeValues, ['action' => $action]);

        // Submission stats per time period
        $timeStats = $this->getModel('form.submission')->getSubmissionsLineChartData(
            null,
            new \DateTime($dateRangeForm->get('date_from')->getData()),
            new \DateTime($dateRangeForm->get('date_to')->getData()),
            null,
            ['form_id' => $objectId]
        );

        // Only show actions and fields that still exist
        $customComponents  = $model->getCustomComponents();
        $activeFormActions = [];
        foreach ($activeForm->getActions() as $formAction) {
            if (!isset($customComponents['actions'][$formAction->getType()])) {
                continue;
            }
            $type                          = explode('.', $formAction->getType());
            $activeFormActions[$type[0]][] = $formAction;
        }

        $activeFormFields = [];
        $fieldHelper      = $this->get('autoborna.helper.form.field_helper');
        $availableFields  = array_flip($fieldHelper->getChoiceList($customComponents['fields']));
        foreach ($activeForm->getFields() as $field) {
            if (!isset($availableFields[$field->getType()])) {
                continue;
            }

            $activeFormFields[] = $field;
        }

        $submissionCounts = $this->getModel('form.submission')->getRepository()->getSubmissionCounts($activeForm);

        return $this->delegateView(
            [
                'viewParameters' => [
                    'activeForm'       => $activeForm,
                    'submissionCounts' => $submissionCounts,
                    'page'             => $page,
                    'logs'             => $logs,
                    'permissions'      => $permissions,
                    'stats'            => [
                        'submissionsInTime' => $timeStats,
                    ],
                    'dateRangeForm'     => $dateRangeForm->createView(),
                    'activeFormActions' => $activeFormActions,
                    'activeFormFields'  => $activeFormFields,
                    'formScript'        => htmlspecialchars($model->getFormScript($activeForm), ENT_QUOTES, 'UTF-8'),
                    'formContent'       => htmlspecialchars($model->getContent($activeForm, false), ENT_QUOTES, 'UTF-8'),
                    'availableActions'  => $customComponents['actions'],
                ],
                'contentTemplate' => 'AutobornaFormBundle:Form:details.html.php',
                'passthroughVars' => [
                    'activeLink'    => '#autoborna_form_index',
                    'autobornaContent' => 'form',
                    'route'         => $action,
                ],
            ]
        );
    }

    /**
     * Generates new form and processes post data.
     *
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @throws \Exception
     */
    public function newAction()
    {
        /** @var \Autoborna\FormBundle\Model\FormModel $model */
        $model   = $this->getModel('form');
        $entity  = $model->getEntity();
        $session = $this->get('session');

        if (!$this->get('autoborna.security')->isGranted('form:forms:create')) {
            return $this->accessDenied();
        }

        //set the page we came from
        $page       = $this->get('session')->get('autoborna.form.page', 1);
        $autobornaform = $this->request->request->get('autobornaform', []);
        $sessionId  = $autobornaform['sessionId'] ?? 'autoborna_'.sha1(uniqid(mt_rand(), true));

        //set added/updated fields
        $modifiedFields = $session->get('autoborna.form.'.$sessionId.'.fields.modified', []);
        $deletedFields  = $session->get('autoborna.form.'.$sessionId.'.fields.deleted', []);

        //set added/updated actions
        $modifiedActions = $session->get('autoborna.form.'.$sessionId.'.actions.modified', []);
        $deletedActions  = $session->get('autoborna.form.'.$sessionId.'.actions.deleted', []);

        $action = $this->generateUrl('autoborna_form_action', ['objectAction' => 'new']);
        $form   = $model->createForm($entity, $this->get('form.factory'), $action);

        ///Check for a submitted form and process it
        if ('POST' == $this->request->getMethod()) {
            $valid = false;
            if (!$cancelled = $this->isFormCancelled($form)) {
                if ($valid = $this->isFormValid($form)) {
                    //only save fields that are not to be deleted
                    $fields = array_diff_key($modifiedFields, array_flip($deletedFields));

                    //make sure that at least one field is selected
                    if (empty($fields)) {
                        //set the error
                        $form->addError(
                            new FormError(
                                $this->get('translator')->trans('autoborna.form.form.fields.notempty', [], 'validators')
                            )
                        );
                        $valid = false;
                    } else {
                        $model->setFields($entity, $fields);

                        try {
                            // Set alias to prevent SQL errors
                            $alias = $model->cleanAlias($entity->getName(), '', 10);
                            $entity->setAlias($alias);

                            // Set timestamps
                            $model->setTimestamps($entity, true, false);

                            // Save the form first and new actions so that new fields are available to actions.
                            // Using the repository function to not trigger the listeners twice.

                            $model->getRepository()->saveEntity($entity);

                            // Only save actions that are not to be deleted
                            $actions = array_diff_key($modifiedActions, array_flip($deletedActions));

                            // Set and persist actions
                            $model->setActions($entity, $actions);

                            // Save and trigger listeners
                            $model->saveEntity($entity, $form->get('buttons')->get('save')->isClicked());

                            $this->addFlash(
                                'autoborna.core.notice.created',
                                [
                                    '%name%'      => $entity->getName(),
                                    '%menu_link%' => 'autoborna_form_index',
                                    '%url%'       => $this->generateUrl(
                                        'autoborna_form_action',
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
                                $returnUrl = $this->generateUrl('autoborna_form_action', $viewParameters);
                                $template  = 'AutobornaFormBundle:Form:view';
                            } else {
                                //return edit view so that all the session stuff is loaded
                                return $this->editAction($entity->getId(), true);
                            }
                        } catch (ValidationException $ex) {
                            $form->addError(
                                new FormError(
                                    $ex->getMessage()
                                )
                            );
                            $valid = false;
                        } catch (\Exception $e) {
                            $form['name']->addError(
                                new FormError($this->get('translator')->trans('autoborna.form.schema.failed', [], 'validators'))
                            );
                            $valid = false;

                            if ('dev' == $this->container->getParameter('kernel.environment')) {
                                throw $e;
                            }
                        }
                    }
                }
            } else {
                $viewParameters = ['page' => $page];
                $returnUrl      = $this->generateUrl('autoborna_form_index', $viewParameters);
                $template       = 'AutobornaFormBundle:Form:index';
            }

            if ($cancelled || ($valid && $form->get('buttons')->get('save')->isClicked())) {
                //clear temporary fields
                $this->clearSessionComponents($sessionId);

                return $this->postActionRedirect(
                    [
                        'returnUrl'       => $returnUrl,
                        'viewParameters'  => $viewParameters,
                        'contentTemplate' => $template,
                        'passthroughVars' => [
                            'activeLink'    => '#autoborna_form_index',
                            'autobornaContent' => 'form',
                        ],
                    ]
                );
            }
        } else {
            //clear out existing fields in case the form was refreshed, browser closed, etc
            $this->clearSessionComponents($sessionId);
            $modifiedFields = $modifiedActions = $deletedActions = $deletedFields = [];

            $form->get('sessionId')->setData($sessionId);

            //add a submit button
            $keyId = 'new'.hash('sha1', uniqid(mt_rand()));
            $field = new Field();

            $modifiedFields[$keyId]                    = $field->convertToArray();
            $modifiedFields[$keyId]['label']           = $this->translator->trans('autoborna.core.form.submit');
            $modifiedFields[$keyId]['alias']           = 'submit';
            $modifiedFields[$keyId]['showLabel']       = 1;
            $modifiedFields[$keyId]['type']            = 'button';
            $modifiedFields[$keyId]['id']              = $keyId;
            $modifiedFields[$keyId]['inputAttributes'] = 'class="btn btn-default"';
            $modifiedFields[$keyId]['formId']          = $sessionId;
            unset($modifiedFields[$keyId]['form']);
            $session->set('autoborna.form.'.$sessionId.'.fields.modified', $modifiedFields);
        }

        //fire the form builder event
        $customComponents = $model->getCustomComponents($sessionId);

        /** @var FormFieldHelper $fieldHelper */
        $fieldHelper = $this->get('autoborna.helper.form.field_helper');

        return $this->delegateView(
            [
                'viewParameters' => [
                    'fields'         => $fieldHelper->getChoiceList($customComponents['fields']),
                    'viewOnlyFields' => $customComponents['viewOnlyFields'],
                    'actions'        => $customComponents['choices'],
                    'actionSettings' => $customComponents['actions'],
                    'formFields'     => $modifiedFields,
                    'formActions'    => $modifiedActions,
                    'deletedFields'  => $deletedFields,
                    'deletedActions' => $deletedActions,
                    'tmpl'           => $this->request->isXmlHttpRequest() ? $this->request->get('tmpl', 'index') : 'index',
                    'activeForm'     => $entity,
                    'form'           => $form->createView(),
                    'contactFields'  => $this->getModel('lead.field')->getFieldListWithProperties(),
                    'companyFields'  => $this->getModel('lead.field')->getFieldListWithProperties('company'),
                    'inBuilder'      => true,
                ],
                'contentTemplate' => 'AutobornaFormBundle:Builder:index.html.php',
                'passthroughVars' => [
                    'activeLink'    => '#autoborna_form_index',
                    'autobornaContent' => 'form',
                    'route'         => $this->generateUrl(
                        'autoborna_form_action',
                        [
                            'objectAction' => (!empty($valid) ? 'edit' : 'new'), //valid means a new form was applied
                            'objectId'     => $entity->getId(),
                        ]
                    ),
                ],
            ]
        );
    }

    /**
     * Generates edit form and processes post data.
     *
     * @param int  $objectId
     * @param bool $ignorePost
     * @param bool $forceTypeSelection
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function editAction($objectId, $ignorePost = false, $forceTypeSelection = false)
    {
        /** @var \Autoborna\FormBundle\Model\FormModel $model */
        $model            = $this->getModel('form');
        $formData         = $this->request->request->get('autobornaform');
        $sessionId        = isset($formData['sessionId']) ? $formData['sessionId'] : null;
        $customComponents = $model->getCustomComponents();

        if ($objectId instanceof Form) {
            $entity   = $objectId;
            $objectId = 'autoborna_'.sha1(uniqid(mt_rand(), true));
        } else {
            $entity = $model->getEntity($objectId);

            // Process submit of cloned form
            if (null == $entity && $objectId == $sessionId) {
                $entity = $model->getEntity();
            }
        }

        $session    = $this->get('session');
        $cleanSlate = true;

        //set the page we came from
        $page = $this->get('session')->get('autoborna.form.page', 1);

        //set the return URL
        $returnUrl = $this->generateUrl('autoborna_form_index', ['page' => $page]);

        $postActionVars = [
            'returnUrl'       => $returnUrl,
            'viewParameters'  => ['page' => $page],
            'contentTemplate' => 'AutobornaFormBundle:Form:index',
            'passthroughVars' => [
                'activeLink'    => '#autoborna_form_index',
                'autobornaContent' => 'form',
            ],
        ];

        //form not found
        if (null === $entity) {
            return $this->postActionRedirect(
                array_merge(
                    $postActionVars,
                    [
                        'flashes' => [
                            [
                                'type'    => 'error',
                                'msg'     => 'autoborna.form.error.notfound',
                                'msgVars' => ['%id%' => $objectId],
                            ],
                        ],
                    ]
                )
            );
        } elseif (!$this->get('autoborna.security')->hasEntityAccess(
            'form:forms:editown',
            'form:forms:editother',
            $entity->getCreatedBy()
        )
        ) {
            return $this->accessDenied();
        } elseif ($model->isLocked($entity)) {
            //deny access if the entity is locked
            return $this->isLocked($postActionVars, $entity, 'form.form');
        }

        $action = $this->generateUrl('autoborna_form_action', ['objectAction' => 'edit', 'objectId' => $objectId]);
        $form   = $model->createForm($entity, $this->get('form.factory'), $action);

        ///Check for a submitted form and process it
        if (!$ignorePost && 'POST' == $this->request->getMethod()) {
            $valid = false;
            if (!$cancelled = $this->isFormCancelled($form)) {
                //set added/updated fields
                $modifiedFields = $session->get('autoborna.form.'.$objectId.'.fields.modified', []);
                $deletedFields  = $session->get('autoborna.form.'.$objectId.'.fields.deleted', []);
                $fields         = array_diff_key($modifiedFields, array_flip($deletedFields));

                //set added/updated actions
                $modifiedActions = $session->get('autoborna.form.'.$objectId.'.actions.modified', []);
                $deletedActions  = $session->get('autoborna.form.'.$objectId.'.actions.deleted', []);
                $actions         = array_diff_key($modifiedActions, array_flip($deletedActions));

                if ($valid = $this->isFormValid($form)) {
                    //make sure that at least one field is selected
                    if (empty($fields)) {
                        //set the error
                        $form->addError(
                            new FormError(
                                $this->get('translator')->trans('autoborna.form.form.fields.notempty', [], 'validators')
                            )
                        );
                        $valid = false;
                    } else {
                        $model->setFields($entity, $fields);
                        $model->deleteFields($entity, $deletedFields);

                        $alias = $entity->getAlias();

                        if (empty($alias)) {
                            $alias = $model->cleanAlias($entity->getName(), '', 10);
                            $entity->setAlias($alias);
                        }

                        if (!$entity->getId()) {
                            // Set timestamps because this is a new clone
                            $model->setTimestamps($entity, true, false);
                        }

                        // save the form first so that new fields are available to actions
                        // use the repository method to not trigger listeners twice
                        try {
                            $model->getRepository()->saveEntity($entity);

                            // Ensure actions are compatible with form type
                            if (!$entity->isStandalone()) {
                                foreach ($actions as $actionId => $action) {
                                    if (empty($customComponents['actions'][$action['type']]['allowCampaignForm'])) {
                                        unset($actions[$actionId]);
                                        $deletedActions[] = $actionId;
                                    }
                                }
                            }

                            if (count($actions)) {
                                // Now set and persist the actions
                                $model->setActions($entity, $actions);
                            }

                            // Delete deleted actions
                            $model->deleteActions($entity, $deletedActions);

                            // Persist and execute listeners
                            $model->saveEntity($entity, $form->get('buttons')->get('save')->isClicked());

                            // Reset objectId to entity ID (can be session ID in case of cloned entity)
                            $objectId = $entity->getId();

                            $this->addFlash(
                                'autoborna.core.notice.updated',
                                [
                                    '%name%'      => $entity->getName(),
                                    '%menu_link%' => 'autoborna_form_index',
                                    '%url%'       => $this->generateUrl(
                                        'autoborna_form_action',
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
                                $returnUrl = $this->generateUrl('autoborna_form_action', $viewParameters);
                                $template  = 'AutobornaFormBundle:Form:view';
                            }
                        } catch (ValidationException $ex) {
                            $form->addError(
                                new FormError(
                                    $ex->getMessage()
                                )
                            );
                            $valid = false;
                        }
                    }
                }
            } else {
                //unlock the entity
                $model->unlockEntity($entity);

                $viewParameters = ['page' => $page];
                $returnUrl      = $this->generateUrl('autoborna_form_index', $viewParameters);
                $template       = 'AutobornaFormBundle:Form:index';
            }

            if ($cancelled || ($valid && $form->get('buttons')->get('save')->isClicked())) {
                //remove fields from session
                $this->clearSessionComponents($objectId);

                // Clear session items in case columns changed
                $session->remove('autoborna.formresult.'.$entity->getId().'.orderby');
                $session->remove('autoborna.formresult.'.$entity->getId().'.orderbydir');
                $session->remove('autoborna.formresult.'.$entity->getId().'.filters');

                return $this->postActionRedirect(
                    array_merge(
                        $postActionVars,
                        [
                            'returnUrl'       => $returnUrl,
                            'viewParameters'  => $viewParameters,
                            'contentTemplate' => $template,
                        ]
                    )
                );
            } elseif ($valid && $form->get('buttons')->get('apply')->isClicked()) {
                // Rebuild everything to include new ids
                $cleanSlate = true;
                $reorder    = true;

                if ($valid) {
                    // Rebuild the form with new action so that apply doesn't keep creating a clone
                    $action = $this->generateUrl('autoborna_form_action', ['objectAction' => 'edit', 'objectId' => $entity->getId()]);
                    $form   = $model->createForm($entity, $this->get('form.factory'), $action);
                }
            }
        } else {
            $cleanSlate = true;

            //lock the entity
            $model->lockEntity($entity);
        }

        if (!$form->isSubmitted()) {
            $form->get('sessionId')->setData($objectId);
        }

        // Get field and action settings
        $fieldHelper     = $this->get('autoborna.helper.form.field_helper');
        $availableFields = $fieldHelper->getChoiceList($customComponents['fields']);

        if ($cleanSlate) {
            //clean slate
            $this->clearSessionComponents($objectId);

            //load existing fields into session
            $modifiedFields    = [];
            $usedLeadFields    = [];
            $usedCompanyFields = [];
            $existingFields    = $entity->getFields()->toArray();
            $submitButton      = false;

            foreach ($existingFields as $formField) {
                // Check to see if the field still exists

                if ('button' == $formField->getType()) {
                    //submit button found
                    $submitButton = true;
                }
                if ('button' !== $formField->getType() && !in_array($formField->getType(), $availableFields)) {
                    continue;
                }

                $id    = $formField->getId();
                $field = $formField->convertToArray();

                if (!$id) {
                    // Cloned entity
                    $id = $field['id'] = $field['sessionId'] = 'new'.hash('sha1', uniqid(mt_rand()));
                }

                unset($field['form']);

                if (isset($customComponents['fields'][$field['type']])) {
                    // Set the custom parameters
                    $field['customParameters'] = $customComponents['fields'][$field['type']];
                }
                $field['formId'] = $objectId;

                $modifiedFields[$id] = $field;

                if (!empty($field['leadField']) && empty($field['parent'])) {
                    $usedLeadFields[$id] = $field['leadField'];
                }
            }
            if (!$submitButton) { //means something deleted the submit button from the form
                //add a submit button
                $keyId = 'new'.hash('sha1', uniqid(mt_rand()));
                $field = new Field();

                $modifiedFields[$keyId]                    = $field->convertToArray();
                $modifiedFields[$keyId]['label']           = $this->translator->trans('autoborna.core.form.submit');
                $modifiedFields[$keyId]['alias']           = 'submit';
                $modifiedFields[$keyId]['showLabel']       = 1;
                $modifiedFields[$keyId]['type']            = 'button';
                $modifiedFields[$keyId]['id']              = $keyId;
                $modifiedFields[$keyId]['inputAttributes'] = 'class="btn btn-default"';
                $modifiedFields[$keyId]['formId']          = $objectId;
                unset($modifiedFields[$keyId]['form']);
            }
            $session->set('autoborna.form.'.$objectId.'.fields.leadfields', $usedLeadFields);

            if (!empty($reorder)) {
                uasort(
                    $modifiedFields,
                    function ($a, $b) {
                        if ($a['order'] == $b['order']) {
                            return 0;
                        }

                        return $a['order'] < $b['order'] ? -1 : 1;
                    }
                );
            }

            $session->set('autoborna.form.'.$objectId.'.fields.modified', $modifiedFields);
            $deletedFields = [];

            // Load existing actions into session
            $modifiedActions = [];
            $existingActions = $entity->getActions()->toArray();

            foreach ($existingActions as $formAction) {
                // Check to see if the action still exists
                if (!isset($customComponents['actions'][$formAction->getType()])) {
                    continue;
                }

                $id     = $formAction->getId();
                $action = $formAction->convertToArray();

                if (!$id) {
                    // Cloned entity so use a random Id instead
                    $action['id'] = $id = 'new'.hash('sha1', uniqid(mt_rand()));
                }
                unset($action['form']);

                $modifiedActions[$id] = $action;
            }

            if (!empty($reorder)) {
                uasort(
                    $modifiedActions,
                    function ($a, $b) {
                        if ($a['order'] == $b['order']) {
                            return 0;
                        }

                        return $a['order'] < $b['order'] ? -1 : 1;
                    }
                );
            }

            $session->set('autoborna.form.'.$objectId.'.actions.modified', $modifiedActions);
            $deletedActions = [];
        }

        return $this->delegateView(
            [
                'viewParameters' => [
                    'fields'             => $availableFields,
                    'viewOnlyFields'     => $customComponents['viewOnlyFields'],
                    'actions'            => $customComponents['choices'],
                    'actionSettings'     => $customComponents['actions'],
                    'formFields'         => $modifiedFields,
                    'fieldSettings'      => $customComponents['fields'],
                    'formActions'        => $modifiedActions,
                    'deletedFields'      => $deletedFields,
                    'deletedActions'     => $deletedActions,
                    'tmpl'               => $this->request->isXmlHttpRequest() ? $this->request->get('tmpl', 'index') : 'index',
                    'activeForm'         => $entity,
                    'form'               => $form->createView(),
                    'forceTypeSelection' => $forceTypeSelection,
                    'contactFields'      => $this->getModel('lead.field')->getFieldListWithProperties('lead'),
                    'companyFields'      => $this->getModel('lead.field')->getFieldListWithProperties('company'),
                    'inBuilder'          => true,
                ],
                'contentTemplate' => 'AutobornaFormBundle:Builder:index.html.php',
                'passthroughVars' => [
                    'activeLink'    => '#autoborna_form_index',
                    'autobornaContent' => 'form',
                    'route'         => $this->generateUrl(
                        'autoborna_form_action',
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
     * @param int $objectId
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function cloneAction($objectId)
    {
        $model = $this->getModel('form.form');

        /** @var \Autoborna\FormBundle\Entity\Form $entity */
        $entity = $model->getEntity($objectId);

        if (null != $entity) {
            if (!$this->get('autoborna.security')->isGranted('form:forms:create')
                || !$this->get('autoborna.security')->hasEntityAccess(
                    'form:forms:viewown',
                    'form:forms:viewother',
                    $entity->getCreatedBy()
                )
            ) {
                return $this->accessDenied();
            }

            $entity = clone $entity;
            $entity->setIsPublished(false);

            // Clone the forms's fields
            $fields = $entity->getFields()->toArray();
            /** @var \Autoborna\FormBundle\Entity\Field $field */
            foreach ($fields as $field) {
                $fieldClone = clone $field;
                $fieldClone->setForm($entity);
                $fieldClone->setSessionId(null);
                $entity->addField($field->getId(), $fieldClone);
            }

            // Clone the forms's actions
            $actions = $entity->getActions()->toArray();
            /** @var \Autoborna\FormBundle\Entity\Action $action */
            foreach ($actions as $action) {
                $actionClone = clone $action;
                $actionClone->setForm($entity);
                $entity->addAction($action->getId(), $actionClone);
            }
        }

        return $this->editAction($entity, true, true);
    }

    /**
     * Gives a preview of the form.
     *
     * @param int $objectId
     *
     * @return Response
     */
    public function previewAction($objectId)
    {
        /** @var FormModel $model */
        $model = $this->getModel('form.form');
        $form  = $model->getEntity($objectId);

        if (null === $form) {
            $html =
                '<h1>'.
                $this->get('translator')->trans('autoborna.form.error.notfound', ['%id%' => $objectId], 'flashes').
                '</h1>';
        } elseif (!$this->get('autoborna.security')->hasEntityAccess(
            'form:forms:editown',
            'form:forms:editother',
            $form->getCreatedBy()
        )
        ) {
            $html = '<h1>'.$this->get('translator')->trans('autoborna.core.error.accessdenied', [], 'flashes').'</h1>';
        } else {
            $html = $model->getContent($form, true, false);
        }

        $model->populateValuesWithGetParameters($form, $html);

        $viewParams = [
            'content'     => $html,
            'stylesheets' => [],
            'name'        => $form->getName(),
            'metaRobots'  => '<meta name="robots" content="index">',
        ];

        if ($form->getNoIndex()) {
            $viewParams['metaRobots'] = '<meta name="robots" content="noindex">';
        }

        $template = $form->getTemplate();
        if (!empty($template)) {
            $theme = $this->get('autoborna.helper.theme')->getTheme($template);
            if ($theme->getTheme() != $template) {
                $config = $theme->getConfig();
                if (in_array('form', $config['features'])) {
                    $template = $theme->getTheme();
                } else {
                    $template = null;
                }
            }
        }

        $viewParams['template'] = $template;

        if (!empty($template)) {
            $logicalName     = $this->get('autoborna.helper.theme')->checkForTwigTemplate(':'.$template.':form.html.php');
            $assetsHelper    = $this->get('templating.helper.assets');
            $slotsHelper     = $this->get('templating.helper.slots');
            $analyticsHelper = $this->get('autoborna.helper.template.analytics');

            $slotsHelper->set('pageTitle', $form->getName());

            $analytics = $analyticsHelper->getCode();

            if (!empty($analytics)) {
                $assetsHelper->addCustomDeclaration($analytics);
            }
            if ($form->getNoIndex()) {
                $assetsHelper->addCustomDeclaration('<meta name="robots" content="noindex">');
            }

            return $this->render($logicalName, $viewParams);
        }

        return $this->render('AutobornaFormBundle::form.html.php', $viewParams);
    }

    /**
     * Deletes the entity.
     *
     * @param int $objectId
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($objectId)
    {
        $page      = $this->get('session')->get('autoborna.form.page', 1);
        $returnUrl = $this->generateUrl('autoborna_form_index', ['page' => $page]);
        $flashes   = [];

        $postActionVars = [
            'returnUrl'       => $returnUrl,
            'viewParameters'  => ['page' => $page],
            'contentTemplate' => 'AutobornaFormBundle:Form:index',
            'passthroughVars' => [
                'activeLink'    => '#autoborna_form_index',
                'autobornaContent' => 'form',
            ],
        ];

        if ('POST' == $this->request->getMethod()) {
            $model  = $this->getModel('form.form');
            $entity = $model->getEntity($objectId);

            if (null === $entity) {
                $flashes[] = [
                    'type'    => 'error',
                    'msg'     => 'autoborna.form.error.notfound',
                    'msgVars' => ['%id%' => $objectId],
                ];
            } elseif (!$this->get('autoborna.security')->hasEntityAccess(
                'form:forms:deleteown',
                'form:forms:deleteother',
                $entity->getCreatedBy()
            )
            ) {
                return $this->accessDenied();
            } elseif ($model->isLocked($entity)) {
                return $this->isLocked($postActionVars, $entity, 'form.form');
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
        $page      = $this->get('session')->get('autoborna.form.page', 1);
        $returnUrl = $this->generateUrl('autoborna_form_index', ['page' => $page]);
        $flashes   = [];

        $postActionVars = [
            'returnUrl'       => $returnUrl,
            'viewParameters'  => ['page' => $page],
            'contentTemplate' => 'AutobornaFormBundle:Form:index',
            'passthroughVars' => [
                'activeLink'    => '#autoborna_form_index',
                'autobornaContent' => 'form',
            ],
        ];

        if ('POST' == $this->request->getMethod()) {
            $model     = $this->getModel('form');
            $ids       = json_decode($this->request->query->get('ids', ''));
            $deleteIds = [];

            // Loop over the IDs to perform access checks pre-delete
            foreach ($ids as $objectId) {
                $objectId = (int) $objectId;
                $entity   = $model->getEntity($objectId);

                if (null === $entity) {
                    $flashes[] = [
                        'type'    => 'error',
                        'msg'     => 'autoborna.form.error.notfound',
                        'msgVars' => ['%id%' => $objectId],
                    ];
                } elseif (!$this->get('autoborna.security')->hasEntityAccess(
                    'form:forms:deleteown',
                    'form:forms:deleteother',
                    $entity->getCreatedBy()
                )
                ) {
                    $flashes[] = $this->accessDenied(true);
                } elseif ($model->isLocked($entity)) {
                    $flashes[] = $this->isLocked($postActionVars, $entity, 'form.form', true);
                } else {
                    $deleteIds[] = $objectId;
                }
            }

            // Delete everything we are able to
            if (!empty($deleteIds)) {
                $entities = $model->deleteEntities($deleteIds);

                $flashes[] = [
                    'type'    => 'notice',
                    'msg'     => 'autoborna.form.notice.batch_deleted',
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
     * Clear field and actions from the session.
     */
    public function clearSessionComponents($sessionId)
    {
        $session = $this->get('session');
        $session->remove('autoborna.form.'.$sessionId.'.fields.modified');
        $session->remove('autoborna.form.'.$sessionId.'.fields.deleted');
        $session->remove('autoborna.form.'.$sessionId.'.fields.leadfields');

        $session->remove('autoborna.form.'.$sessionId.'.actions.modified');
        $session->remove('autoborna.form.'.$sessionId.'.actions.deleted');
    }

    public function batchRebuildHtmlAction()
    {
        $page      = $this->get('session')->get('autoborna.form.page', 1);
        $returnUrl = $this->generateUrl('autoborna_form_index', ['page' => $page]);
        $flashes   = [];

        $postActionVars = [
            'returnUrl'       => $returnUrl,
            'viewParameters'  => ['page' => $page],
            'contentTemplate' => 'AutobornaFormBundle:Form:index',
            'passthroughVars' => [
                'activeLink'    => '#autoborna_form_index',
                'autobornaContent' => 'form',
            ],
        ];

        if ('POST' == $this->request->getMethod()) {
            /** @var \Autoborna\FormBundle\Model\FormModel $model */
            $model = $this->getModel('form');
            $ids   = json_decode($this->request->query->get('ids', ''));
            $count = 0;
            // Loop over the IDs to perform access checks pre-delete
            foreach ($ids as $objectId) {
                $entity = $model->getEntity($objectId);

                if (null === $entity) {
                    $flashes[] = [
                        'type'    => 'error',
                        'msg'     => 'autoborna.form.error.notfound',
                        'msgVars' => ['%id%' => $objectId],
                    ];
                } elseif (!$this->get('autoborna.security')->hasEntityAccess(
                    'form:forms:editown',
                    'form:forms:editother',
                    $entity->getCreatedBy()
                )
                ) {
                    $flashes[] = $this->accessDenied(true);
                } elseif ($model->isLocked($entity)) {
                    $flashes[] = $this->isLocked($postActionVars, $entity, 'form.form', true);
                } else {
                    $model->generateHtml($entity);
                    ++$count;
                }
            }

            $flashes[] = [
                'type'    => 'notice',
                'msg'     => 'autoborna.form.notice.batch_html_generated',
                'msgVars' => [
                    '%count%'     => $count,
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

    public function getModelName(): string
    {
        return 'form';
    }

    protected function getDefaultOrderDirection(): string
    {
        return 'DESC';
    }
}
