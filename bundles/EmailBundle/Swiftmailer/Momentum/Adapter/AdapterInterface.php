<?php

namespace Autoborna\EmailBundle\Swiftmailer\Momentum\Adapter;

use Autoborna\EmailBundle\Swiftmailer\Momentum\DTO\TransmissionDTO;
use SparkPost\SparkPostPromise;

/**
 * Interface AdapterInterface.
 */
interface AdapterInterface
{
    /**
     * @return SparkPostPromise
     */
    public function createTransmission(TransmissionDTO $transmissionDTO);
}
