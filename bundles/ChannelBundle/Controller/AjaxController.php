<?php

namespace Autoborna\ChannelBundle\Controller;

use Autoborna\CoreBundle\Controller\AjaxController as CommonAjaxController;
use Autoborna\CoreBundle\Controller\AjaxLookupControllerTrait;
use Symfony\Component\HttpFoundation\Request;

class AjaxController extends CommonAjaxController
{
    use AjaxLookupControllerTrait;

    /**
     * @param $eventId
     * @param $contactId
     *
     * @return LeadEventLog|\Symfony\Component\HttpFoundation\JsonResponse
     *
     * @throws \Exception
     */
    public function cancelQueuedMessageEventAction(Request $request)
    {
        $dataArray      = ['success' => 0];
        $messageQueueId = (int) $request->request->get('channelId');
        $queueModel     = $this->getModel('channel.queue');
        $queuedMessage  = $queueModel->getEntity($messageQueueId);
        if ($queuedMessage) {
            $queuedMessage->setStatus('cancelled');
            $queueModel->saveEntity($queuedMessage);
            $dataArray = ['success' => 1];
        }

        return $this->sendJsonResponse($dataArray);
    }
}
