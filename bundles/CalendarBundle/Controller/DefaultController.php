<?php

namespace Autoborna\CalendarBundle\Controller;

use Autoborna\CoreBundle\Controller\FormController;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class DefaultController.
 */
class DefaultController extends FormController
{
    /**
     * Generates the default view.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->delegateView([
            'contentTemplate' => 'AutobornaCalendarBundle:Default:index.html.php',
            'passthroughVars' => [
                'activeLink'    => '#autoborna_calendar_index',
                'autobornaContent' => 'calendar',
                'route'         => $this->generateUrl('autoborna_calendar_index'),
            ],
        ]);
    }

    /**
     * Generates the modal view.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction()
    {
        $source    = $this->request->query->get('source');
        $startDate = new \DateTime($this->request->query->get('startDate'));
        $entityId  = $this->request->query->get('objectId');

        /* @type \Autoborna\CalendarBundle\Model\CalendarModel $model */
        $calendarModel = $this->getModel('calendar');
        $event         = $calendarModel->editCalendarEvent($source, $entityId);

        $model         = $event->getModel();
        $entity        = $event->getEntity();
        $session       = $this->get('session');
        $sourceSession = $this->get('session')->get('autoborna.calendar.'.$source, 1);

        //set the return URL
        $returnUrl = $this->generateUrl('autoborna_calendar_index', [$source => $sourceSession]);

        $postActionVars = [
            'returnUrl'       => $returnUrl,
            'viewParameters'  => [$source => $sourceSession],
            'contentTemplate' => $event->getContentTemplate(),
            'passthroughVars' => [
                'activeLink'    => 'autoborna_calendar_index',
                'autobornaContent' => $source,
            ],
        ];

        //not found
        if (null === $entity) {
            return $this->postActionRedirect(
                array_merge($postActionVars, [
                    'flashes' => [
                        [
                            'type'    => 'error',
                            'msg'     => 'autoborna.'.$source.'.error.notfound',
                            'msgVars' => ['%id%' => $entityId],
                        ],
                    ],
                ])
            );
        } elseif (!$event->hasAccess()) {
            return $this->accessDenied();
        } elseif ($model->isLocked($entity)) {
            //deny access if the entity is locked
            return $this->isLocked($postActionVars, $entity, $source.'.'.$source);
        }

        //Create the form
        $action = $this->generateUrl('autoborna_calendar_action', [
            'objectAction' => 'edit',
            'objectId'     => $entity->getId(),
            'source'       => $source,
            'startDate'    => $startDate->format('Y-m-d H:i:s'),
        ]);
        $form = $model->createForm($entity, $this->get('form.factory'), $action, ['formName' => $event->getFormName()]);

        ///Check for a submitted form and process it
        if ('POST' == $this->request->getMethod()) {
            $valid = false;
            if (!$cancelled = $this->isFormCancelled($form)) {
                if ($valid = $this->isFormValid($form)) {
                    $contentName     = 'autoborna.'.$source.'builder.'.$entity->getSessionId().'.content';
                    $existingContent = $entity->getContent();
                    $newContent      = $session->get($contentName, []);
                    $content         = array_merge($existingContent, $newContent);
                    $entity->setContent($content);

                    //form is valid so process the data
                    $model->saveEntity($entity, $form->get('buttons')->get('save')->isClicked());

                    //clear the session
                    $session->remove($contentName);

                    $this->addFlash('autoborna.core.notice.updated', [
                        '%name%'      => $entity->getTitle(),
                        '%menu_link%' => 'autoborna_'.$source.'_index',
                        '%url%'       => $this->generateUrl('autoborna_'.$source.'_action', [
                            'objectAction' => 'edit',
                            'objectId'     => $entity->getId(),
                        ]),
                    ]);
                }
            } else {
                //clear any modified content
                $session->remove('autoborna.'.$source.'builder.'.$entityId.'.content');
                //unlock the entity
                $model->unlockEntity($entity);
            }

            if ($cancelled || ($valid && $form->get('buttons')->get('save')->isClicked())) {
                return new JsonResponse([
                    'autobornaContent' => 'calendarModal',
                    'closeModal'    => 1,
                ]);
            }
        } else {
            //lock the entity
            $model->lockEntity($entity);
        }

        $builderComponents = $model->getBuilderComponents($entity);

        return $this->delegateView([
            'viewParameters' => [
                'form'   => $this->setFormTheme($form, $event->getContentTemplate()),
                'tokens' => $builderComponents[$source.'Tokens'],
                'entity' => $entity,
                'model'  => $model,
            ],
            'contentTemplate' => $event->getContentTemplate(),
            'passthroughVars' => [
                'activeLink'    => '#autoborna_calendar_index',
                'autobornaContent' => 'calendarModal',
                'route'         => $this->generateUrl('autoborna_calendar_action', [
                    'objectAction' => 'edit',
                    'objectId'     => $entity->getId(),
                    'source'       => $source,
                    'startDate'    => $startDate->format('Y-m-d H:i:s'),
                ]),
            ],
        ]);
    }
}
