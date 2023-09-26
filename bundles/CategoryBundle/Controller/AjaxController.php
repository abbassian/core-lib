<?php

namespace Autoborna\CategoryBundle\Controller;

use Autoborna\CoreBundle\Controller\AjaxController as CommonAjaxController;
use Autoborna\CoreBundle\Helper\InputHelper;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AjaxController.
 */
class AjaxController extends CommonAjaxController
{
    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function categoryListAction(Request $request)
    {
        $bundle    = InputHelper::clean($request->query->get('bundle'));
        $filter    = InputHelper::clean($request->query->get('filter'));
        $results   = $this->getModel('category')->getLookupResults($bundle, $filter, 10);
        $dataArray = [];
        foreach ($results as $r) {
            $dataArray[] = [
                'label' => $r['title']." ({$r['id']})",
                'value' => $r['id'],
            ];
        }

        return $this->sendJsonResponse($dataArray);
    }
}
