<?php

namespace Autoborna\EmailBundle\MonitoredEmail\Processor;

use Autoborna\EmailBundle\MonitoredEmail\Message;

interface ProcessorInterface
{
    /**
     * Process the message.
     *
     * @return bool
     */
    public function process(Message $message);
}
