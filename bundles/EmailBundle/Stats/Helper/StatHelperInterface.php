<?php

namespace Autoborna\EmailBundle\Stats\Helper;

use Autoborna\EmailBundle\Stats\FetchOptions\EmailStatOptions;
use Autoborna\StatsBundle\Aggregate\Collection\StatCollection;

interface StatHelperInterface
{
    /**
     * @return string
     */
    public function getName();

    public function fetchStats(\DateTime $fromDateTime, \DateTime $toDateTime, EmailStatOptions $options);

    public function generateStats(\DateTime $fromDateTime, \DateTime $toDateTime, EmailStatOptions $options, StatCollection $statCollection);
}
