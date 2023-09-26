<?php

namespace Autoborna\SmsBundle\Controller;

use Autoborna\SmsBundle\Callback\HandlerContainer;
use Autoborna\SmsBundle\Exception\CallbackHandlerNotFound;
use Autoborna\SmsBundle\Helper\ReplyHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ReplyController extends AbstractController
{
    /**
     * @var HandlerContainer
     */
    private $callbackHandler;

    /**
     * @var ReplyHelper
     */
    private $replyHelper;

    /**
     * ReplyController constructor.
     */
    public function __construct(HandlerContainer $callbackHandler, ReplyHelper $replyHelper)
    {
        $this->callbackHandler = $callbackHandler;
        $this->replyHelper     = $replyHelper;
    }

    /**
     * @param $transport
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function callbackAction(Request $request, $transport)
    {
        define('MAUTIC_NON_TRACKABLE_REQUEST', 1);

        try {
            $handler = $this->callbackHandler->getHandler($transport);
        } catch (CallbackHandlerNotFound $exception) {
            throw new NotFoundHttpException();
        }

        return $this->replyHelper->handleRequest($handler, $request);
    }
}
