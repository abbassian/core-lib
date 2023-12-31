<?php

namespace Autoborna\CampaignBundle\Event;

use Doctrine\Common\Collections\ArrayCollection;
use Autoborna\CampaignBundle\Entity\Event;
use Autoborna\CampaignBundle\EventCollector\Accessor\Event\AbstractEventAccessor;

class ScheduledBatchEvent extends AbstractLogCollectionEvent
{
    /**
     * @var bool
     */
    private $isReschedule;

    /**
     * ScheduledBatchEvent constructor.
     *
     * @param bool $isReschedule
     */
    public function __construct(AbstractEventAccessor $config, Event $event, ArrayCollection $logs, $isReschedule = false)
    {
        parent::__construct($config, $event, $logs);

        $this->isReschedule = $isReschedule;
    }

    /**
     * @return ArrayCollection
     */
    public function getScheduled()
    {
        return $this->logs;
    }

    /**
     * @return bool
     */
    public function isReschedule()
    {
        return $this->isReschedule;
    }
}
