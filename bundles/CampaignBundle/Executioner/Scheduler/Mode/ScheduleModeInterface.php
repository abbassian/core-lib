<?php

namespace Autoborna\CampaignBundle\Executioner\Scheduler\Mode;

use Autoborna\CampaignBundle\Entity\Event;

interface ScheduleModeInterface
{
    /**
     * @return \DateTime
     */
    public function getExecutionDateTime(Event $event, \DateTime $now, \DateTime $comparedToDateTime);
}
