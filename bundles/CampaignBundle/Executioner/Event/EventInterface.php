<?php

namespace Autoborna\CampaignBundle\Executioner\Event;

use Doctrine\Common\Collections\ArrayCollection;
use Autoborna\CampaignBundle\EventCollector\Accessor\Event\AbstractEventAccessor;
use Autoborna\CampaignBundle\Executioner\Result\EvaluatedContacts;

interface EventInterface
{
    /**
     * @return EvaluatedContacts
     */
    public function execute(AbstractEventAccessor $config, ArrayCollection $logs);
}
