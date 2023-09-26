<?php

namespace Autoborna\EmailBundle\Swiftmailer\Transport;

use Autoborna\EmailBundle\MonitoredEmail\Exception\BounceNotFound;
use Autoborna\EmailBundle\MonitoredEmail\Message;
use Autoborna\EmailBundle\MonitoredEmail\Processor\Bounce\BouncedEmail;

/**
 * Interface InterfaceBounceProcessor.
 */
interface BounceProcessorInterface
{
    /**
     * Get the email address that bounced.
     *
     * @return BouncedEmail
     *
     * @throws BounceNotFound
     */
    public function processBounce(Message $message);
}
