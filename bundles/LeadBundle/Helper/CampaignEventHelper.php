<?php

namespace Autoborna\LeadBundle\Helper;

use Autoborna\LeadBundle\Entity\Lead;
use Autoborna\LeadBundle\Event\ListChangeEvent;

/**
 * Class CampaignEventHelper.
 */
class CampaignEventHelper
{
    /**
     * @param $event
     *
     * @return bool
     */
    public static function validatePointChange($event, Lead $lead)
    {
        $properties  = $event['properties'];
        $checkPoints = $properties['points'];

        if (!empty($checkPoints)) {
            $points = $lead->getPoints();
            if ($points < $checkPoints) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $event
     *
     * @return bool
     */
    public static function validateListChange(ListChangeEvent $eventDetails, $event)
    {
        $limitAddTo      = $event['properties']['addedTo'];
        $limitRemoveFrom = $event['properties']['removedFrom'];
        $list            = $eventDetails->getList();

        if ($eventDetails->wasAdded() && !empty($limitAddTo) && !in_array($list->getId(), $limitAddTo)) {
            return false;
        }

        if ($eventDetails->wasRemoved() && !empty($limitRemoveFrom) && !in_array($list->getId(), $limitRemoveFrom)) {
            return false;
        }

        return true;
    }
}
