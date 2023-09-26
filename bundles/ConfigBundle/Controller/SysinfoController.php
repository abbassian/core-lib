<?php

namespace Autoborna\ConfigBundle\Controller;

use Autoborna\CoreBundle\Controller\FormController;
use Symfony\Component\HttpFoundation\JsonResponse;

class SysinfoController extends FormController
{
    /**
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        if (!$this->user->isAdmin() || $this->coreParametersHelper->get('sysinfo_disabled')) {
            return $this->accessDenied();
        }

        /** @var \Autoborna\ConfigBundle\Model\SysinfoModel $model */
        $model = $this->getModel('config.sysinfo');

        return $this->delegateView([
            'viewParameters' => [
                'phpInfo'         => $model->getPhpInfo(),
                'requirements'    => $model->getRequirements(),
                'recommendations' => $model->getRecommendations(),
                'folders'         => $model->getFolders(),
                'log'             => $model->getLogTail(200),
                'dbInfo'          => $model->getDbInfo(),
            ],
            'contentTemplate' => 'AutobornaConfigBundle:Sysinfo:index.html.php',
            'passthroughVars' => [
                'activeLink'    => '#autoborna_sysinfo_index',
                'autobornaContent' => 'sysinfo',
                'route'         => $this->generateUrl('autoborna_sysinfo_index'),
            ],
        ]);
    }
}
