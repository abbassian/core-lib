<?php

namespace Autoborna\EmailBundle\Swiftmailer\Transport;

use Autoborna\EmailBundle\MonitoredEmail\Exception\UnsubscriptionNotFound;
use Autoborna\EmailBundle\MonitoredEmail\Message;
use Autoborna\EmailBundle\MonitoredEmail\Processor\Unsubscription\UnsubscribedEmail;

/**
 * Interface InterfaceUnsubscriptionProcessor.
 */
interface UnsubscriptionProcessorInterface
{
    /**
     * Get the email address that unsubscribed.
     *
     * @return UnsubscribedEmail
     *
     * @throws UnsubscriptionNotFound
     */
    public function processUnsubscription(Message $message);
}
