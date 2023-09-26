<?php

namespace Autoborna\CampaignBundle\Event;

use Autoborna\CampaignBundle\Entity\Campaign;
use Autoborna\CoreBundle\Event\CommonEvent;

/**
 * Class CampaignEvent.
 */
class CampaignEvent extends CommonEvent
{
    /**
     * @param bool $isNew
     */
    public function __construct(Campaign &$campaign, $isNew = false)
    {
        $this->entity = &$campaign;
        $this->isNew  = $isNew;
    }

    /**
     * Returns the Campaign entity.
     *
     * @return Campaign
     */
    public function getCampaign()
    {
        return $this->entity;
    }

    /**
     * Sets the Campaign entity.
     */
    public function setCampaign(Campaign $campaign)
    {
        $this->entity = $campaign;
    }
}
