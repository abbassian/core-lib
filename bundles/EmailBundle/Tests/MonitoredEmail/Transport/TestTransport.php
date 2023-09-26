<?php

namespace Autoborna\EmailBundle\Tests\MonitoredEmail\Transport;

use Autoborna\EmailBundle\MonitoredEmail\Message;
use Autoborna\EmailBundle\MonitoredEmail\Processor\Bounce\BouncedEmail;
use Autoborna\EmailBundle\MonitoredEmail\Processor\Unsubscription\UnsubscribedEmail;
use Autoborna\EmailBundle\Swiftmailer\Transport\BounceProcessorInterface;
use Autoborna\EmailBundle\Swiftmailer\Transport\UnsubscriptionProcessorInterface;

class TestTransport extends \Swift_Transport_NullTransport implements BounceProcessorInterface, UnsubscriptionProcessorInterface
{
    public function processBounce(Message $message)
    {
        return new BouncedEmail();
    }

    public function processUnsubscription(Message $message)
    {
        return new UnsubscribedEmail('contact@email.com', 'test+unsubscribe_123abc@test.com');
    }
}
