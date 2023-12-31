<?php

namespace Autoborna\FormBundle\Controller;

use Autoborna\CoreBundle\Controller\AjaxController as CommonAjaxController;
use Autoborna\CoreBundle\Helper\InputHelper;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AjaxController.
 */
class AjaxController extends CommonAjaxController
{
    /**
     * @param string $name
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function reorderFieldsAction(Request $request, $bundle, $name = 'fields')
    {
        if ('form' === $name) {
            $name = 'fields';
        }
        $dataArray   = ['success' => 0];
        $sessionId   = InputHelper::clean($request->request->get('formId'));
        $sessionName = 'autoborna.form.'.$sessionId.'.'.$name.'.modified';
        $session     = $this->get('session');
        $orderName   = ('fields' == $name) ? 'autobornaform' : 'autobornaform_action';
        $order       = InputHelper::clean($request->request->get($orderName));
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
    protected function reorderActionsAction(Request $request)
    {
        return $this->reorderFieldsAction($request, 'actions');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function updateFormFieldsAction(Request $request)
    {
        $formId     = (int) $request->request->get('formId');
        $dataArray  = ['success' => 0];
        $model      = $this->getModel('form');
        $entity     = $model->getEntity($formId);
        $formFields = empty($entity) ? [] : $entity->getFields();
        $fields     = [];

        foreach ($formFields as $field) {
            if ('button' != $field->getType()) {
                $properties = $field->getProperties();
                $options    = [];

                if (!empty($properties['list']['list'])) {
                    //If the field is a SELECT field then the data gets stored in [list][list]
                    $optionList = $properties['list']['list'];
                } elseif (!empty($properties['optionlist']['list'])) {
                    //If the field is a radio or a checkbox then it will be stored in [optionlist][list]
                    $optionList = $properties['optionlist']['list'];
                }
                if (!empty($optionList)) {
                    foreach ($optionList as $listItem) {
                        if (is_array($listItem) && isset($listItem['value']) && isset($listItem['label'])) {
                            //The select box needs values to be [value] => label format so make sure we have that style then put it in
                            $options[$listItem['value']] = $listItem['label'];
                        } elseif (!is_array($listItem)) {
                            //Keeping for BC
                            $options[] = $listItem;
                        }
                    }
                }

                $fields[] = [
                    'id'      => $field->getId(),
                    'label'   => $field->getLabel(),
                    'alias'   => $field->getAlias(),
                    'type'    => $field->getType(),
                    'options' => $options,
                ];

                // Be sure to not pollute the symbol table.
                unset($optionList);
            }
        }

        $dataArray['fields']  = $fields;
        $dataArray['success'] = 1;

        return $this->sendJsonResponse($dataArray);
    }

    /**
     * Ajax submit for forms.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function submitAction()
    {
        $response     = $this->forwardWithPost('AutobornaFormBundle:Public:submit', $this->request->request->all(), [], ['ajax' => true]);
        $responseData = json_decode($response->getContent(), true);
        $success      = (!in_array($response->getStatusCode(), [404, 500]) && empty($responseData['errorMessage'])
            && empty($responseData['validationErrors']));

        $message = '';
        $type    = '';
        if (isset($responseData['successMessage'])) {
            $message = $responseData['successMessage'];
            $type    = 'notice';
        } elseif (isset($responseData['errorMessage'])) {
            $message = $responseData['errorMessage'];
            $type    = 'error';
        }

        $data = array_merge($responseData, ['message' => $message, 'type' => $type, 'success' => $success]);

        return $this->sendJsonResponse($data);
    }
}
