<?php

namespace Autoborna\DynamicContentBundle\Controller;

use Autoborna\CoreBundle\Controller\CommonController;
use Autoborna\DynamicContentBundle\Helper\DynamicContentHelper;
use Autoborna\LeadBundle\Entity\Lead;
use Autoborna\LeadBundle\Model\LeadModel;
use Autoborna\LeadBundle\Tracker\Service\DeviceTrackingService\DeviceTrackingServiceInterface;
use Autoborna\PageBundle\Model\PageModel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class DynamicContentApiController.
 */
class DynamicContentApiController extends CommonController
{
    /**
     * @param $objectAlias
     *
     * @return mixed
     */
    public function processAction($objectAlias)
    {
        // Don't store a visitor with this request
        defined('MAUTIC_NON_TRACKABLE_REQUEST') || define('MAUTIC_NON_TRACKABLE_REQUEST', 1);

        $method = $this->request->getMethod();
        if (method_exists($this, $method.'Action')) {
            return $this->{$method.'Action'}($objectAlias);
        } else {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'This endpoint is not able to process '.strtoupper($method).' requests.');
        }
    }

    public function getAction($objectAlias)
    {
        /** @var LeadModel $model */
        $model = $this->getModel('lead');
        /** @var DynamicContentHelper $helper */
        $helper = $this->get('autoborna.helper.dynamicContent');
        /** @var DeviceTrackingServiceInterface $deviceTrackingService */
        $deviceTrackingService = $this->get('autoborna.lead.service.device_tracking_service');
        /** @var PageModel $pageModel */
        $pageModel = $this->getModel('page');

        /** @var Lead $lead */
        $lead          = $model->getContactFromRequest($pageModel->getHitQuery($this->request));
        $content       = $helper->getDynamicContentForLead($objectAlias, $lead);
        $trackedDevice = $deviceTrackingService->getTrackedDevice();
        $deviceId      = (null === $trackedDevice ? null : $trackedDevice->getTrackingId());

        return empty($content)
            ? new Response('', Response::HTTP_NO_CONTENT)
            : new JsonResponse(
                [
                    'content'   => $content,
                    'id'        => $lead->getId(),
                    'sid'       => $deviceId,
                    'device_id' => $deviceId,
                ]
            );
    }
}
