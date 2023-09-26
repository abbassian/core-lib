<?php

namespace Autoborna\SmsBundle\Controller;

use Autoborna\CoreBundle\Controller\AjaxController as CommonAjaxController;
use Autoborna\CoreBundle\Controller\AjaxLookupControllerTrait;
use Autoborna\SmsBundle\Broadcast\BroadcastQuery;
use Autoborna\SmsBundle\Model\SmsModel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AjaxController extends CommonAjaxController
{
    use AjaxLookupControllerTrait;

    protected function getSmsCountStatsAction(Request $request)
    {
        /** @var SmsModel $model */
        $model = $this->getModel('sms');
        /** @var BroadcastQuery $broadcastQuery */
        $broadcastQuery     = $this->get('autoborna.sms.broadcast.query');
        $cacheStorageHelper = $this->get('autoborna.helper.cache_storage');

        $id  = $request->get('id');
        $ids = $request->get('ids');

        // Support for legacy calls
        if (!$ids && $id) {
            $ids = [$id];
        }

        $data = [];
        foreach ($ids as $id) {
            if ($sms = $model->getEntity($id)) {
                if ('list' !== $sms->getSmsType()) {
                    continue;
                }

                $pending = $broadcastQuery->getPendingCount($sms);
                $cacheStorageHelper->set(sprintf('%s|%s|%s', 'sms', $sms->getId(), 'pending'), $pending);
                if (!$pending) {
                    continue;
                }
                $data[] = [
                    'id'          => $id,
                    'pending'     => $this->translator->trans(
                        'autoborna.sms.stat.leadcount',
                        ['%count%' => $pending]
                    ),
                ];
            }
        }

        // Support for legacy calls
        if ($request->get('id')) {
            $data = $data[0];
        } else {
            $data = [
                'success' => 1,
                'stats'   => $data,
            ];
        }

        return new JsonResponse($data);
    }
}
