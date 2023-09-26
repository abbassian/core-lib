<?php

namespace Autoborna\QueueBundle\Queue;

/**
 * Class QueueProtocol.
 */
class QueueProtocol
{
    const BEANSTALKD = 'beanstalkd';
    const RABBITMQ   = 'rabbitmq';
}
