<?php

namespace Autoborna\NotificationBundle\Controller;

use Autoborna\CoreBundle\Controller\CommonController;
use Symfony\Component\HttpFoundation\Response;

class JsController extends CommonController
{
    /**
     * We can't user JsonResponse here, because
     * it improperly encodes the data array.
     *
     * @return Response
     */
    public function manifestAction()
    {
        $gcmSenderId = $this->get('autoborna.helper.core_parameters')->get('gcm_sender_id', '446150739532');
        $data        = [
            'start_url'             => '/',
            'gcm_sender_id'         => $gcmSenderId,
            'gcm_user_visible_only' => true,
        ];

        return new Response(
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            200,
            [
                'Content-Type' => 'application/json',
            ]
        );
    }

    /**
     * @return Response
     */
    public function workerAction()
    {
        return new Response(
            "importScripts('https://cdn.onesignal.com/sdks/OneSignalSDK.js');",
            200,
            [
                'Service-Worker-Allowed' => '/',
                'Content-Type'           => 'application/javascript',
            ]
        );
    }

    /**
     * @return Response
     */
    public function updaterAction()
    {
        return new Response(
            "importScripts('https://cdn.onesignal.com/sdks/OneSignalSDK.js');",
            200,
            [
                'Service-Worker-Allowed' => '/',
                'Content-Type'           => 'application/javascript',
            ]
        );
    }
}
