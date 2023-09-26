<?php

namespace Autoborna\StageBundle\Controller;

use Autoborna\CoreBundle\Controller\AjaxController as CommonAjaxController;
use Autoborna\CoreBundle\Helper\InputHelper;
use Autoborna\StageBundle\Form\Type\StageActionType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AjaxController.
 */
class AjaxController extends CommonAjaxController
{
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
            /** @var \Autoborna\StageBundle\Model\StageModel $model */
            $model   = $this->getModel('stage');
            $actions = $model->getStageActions();

            if (isset($actions['actions'][$type])) {
                $themes = ['AutobornaStageBundle:FormTheme\Action'];
                if (!empty($actions['actions'][$type]['formTheme'])) {
                    $themes[] = $actions['actions'][$type]['formTheme'];
                }
                $formType        = (!empty($actions['actions'][$type]['formType'])) ? $actions['actions'][$type]['formType'] : 'genericstage_settings';
                $formTypeOptions = (!empty($actions['actions'][$type]['formTypeOptions'])) ? $actions['actions'][$type]['formTypeOptions'] : [];

                $form = $this->get('form.factory')->create(StageActionType::class, [], ['formType' => $formType, 'formTypeOptions' => $formTypeOptions]);
                $html = $this->renderView('AutobornaStageBundle:Stage:actionform.html.php', [
                    'form' => $this->setFormTheme($form, 'AutobornaStageBundle:Stage:actionform.html.php', $themes),
                ]);

                $html                 = str_replace('stageaction', 'stage', $html);
                $dataArray['html']    = $html;
                $dataArray['success'] = 1;
            }
        }

        return $this->sendJsonResponse($dataArray);
    }
}
