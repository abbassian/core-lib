<?php

namespace Autoborna\PointBundle\Controller;

use Autoborna\CoreBundle\Controller\AjaxController as CommonAjaxController;
use Autoborna\CoreBundle\Helper\InputHelper;
use Autoborna\PointBundle\Form\Type\GenericPointSettingsType;
use Autoborna\PointBundle\Form\Type\PointActionType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AjaxController.
 */
class AjaxController extends CommonAjaxController
{
    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function reorderTriggerEventsAction(Request $request)
    {
        $dataArray   = ['success' => 0];
        $session     = $this->get('session');
        $triggerId   = InputHelper::clean($request->request->get('triggerId'));
        $sessionName = 'autoborna.point.'.$triggerId.'.triggerevents.modified';
        $order       = InputHelper::clean($request->request->get('triggerEvent'));
        $components  = $session->get($sessionName);
        if (!empty($order) && !empty($components)) {
            $components = array_replace(array_flip($order), $components);
            $session->set($sessionName, $components);
            $dataArray['success'] = 1;
        }

        return $this->sendJsonResponse($dataArray);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function getActionFormAction(Request $request)
    {
        $dataArray = [
            'success' => 0,
            'html'    => '',
        ];
        $type = InputHelper::clean($request->request->get('actionType'));

        if (!empty($type)) {
            //get the HTML for the form
            /** @var \Autoborna\PointBundle\Model\PointModel $model */
            $model   = $this->getModel('point');
            $actions = $model->getPointActions();

            if (isset($actions['actions'][$type])) {
                $themes = ['AutobornaPointBundle:FormTheme\Action'];
                if (!empty($actions['actions'][$type]['formTheme'])) {
                    $themes[] = $actions['actions'][$type]['formTheme'];
                }

                $formType        = (!empty($actions['actions'][$type]['formType'])) ? $actions['actions'][$type]['formType'] : GenericPointSettingsType::class;
                $formTypeOptions = (!empty($actions['actions'][$type]['formTypeOptions'])) ? $actions['actions'][$type]['formTypeOptions'] : [];
                $form            = $this->get('form.factory')->create(PointActionType::class, [], ['formType' => $formType, 'formTypeOptions' => $formTypeOptions]);
                $html            = $this->renderView('AutobornaPointBundle:Point:actionform.html.php', [
                    'form' => $this->setFormTheme($form, 'AutobornaPointBundle:Point:actionform.html.php', $themes),
                ]);

                //replace pointaction with point
                $html                 = str_replace('pointaction', 'point', $html);
                $dataArray['html']    = $html;
                $dataArray['success'] = 1;
            }
        }

        return $this->sendJsonResponse($dataArray);
    }
}
