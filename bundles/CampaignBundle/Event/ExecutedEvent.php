<?php

namespace Autoborna\CampaignBundle\Event;

use Autoborna\CampaignBundle\Entity\LeadEventLog;
use Autoborna\CampaignBundle\EventCollector\Accessor\Event\AbstractEventAccessor;

class ExecutedEvent extends \Symfony\Component\EventDispatcher\Event
{
    /**
     * @var AbstractEventAccessor
     */
    private $config;

    /**
     * @var LeadEventLog
     */
    private $log;

    /**
     * ExecutedEvent constructor.
     */
    public function __construct(AbstractEventAccessor $config, LeadEventLog $log)
    {
        $this->config = $config;
        $this->log    = $log;
    }

    /**
     * @return AbstractEventAccessor
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return LeadEventLog
     */
    public function getLog()
    {
        return $this->log;
    }
}
