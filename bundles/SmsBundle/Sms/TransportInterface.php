<?php

namespace Autoborna\SmsBundle\Sms;

use Autoborna\LeadBundle\Entity\Lead;

interface TransportInterface
{
    /**
     * @param string $content
     *
     * @return bool
     */
    public function sendSms(Lead $lead, $content);
}
