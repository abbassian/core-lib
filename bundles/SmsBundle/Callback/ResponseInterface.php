<?php

namespace Autoborna\SmsBundle\Callback;

use Symfony\Component\HttpFoundation\Response;

interface ResponseInterface
{
    /**
     * @return Response
     */
    public function getResponse();
}
