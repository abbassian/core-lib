<?php

namespace Autoborna\EmailBundle\Event;

use Autoborna\EmailBundle\Swiftmailer\Transport\CallbackTransportInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

/**
 * Event triggered when a transport service send Autoborna a webhook request.
 */
class TransportWebhookEvent extends Event
{
    /**
     * @var CallbackTransportInterface
     */
    private $transport;

    /**
     * @var Request
     */
    private $request;

    public function __construct(CallbackTransportInterface $transport, Request $request)
    {
        $this->transport = $transport;
        $this->request   = $request;
    }

    /**
     * @return CallbackTransportInterface
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Checks if the event is for specific transport.
     *
     * @param string $transportClassName
     *
     * @return bool
     */
    public function transportIsInstanceOf($transportClassName)
    {
        return $this->transport instanceof $transportClassName;
    }
}
