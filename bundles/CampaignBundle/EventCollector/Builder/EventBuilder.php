<?php

namespace Autoborna\CampaignBundle\EventCollector\Builder;

use Autoborna\CampaignBundle\EventCollector\Accessor\Event\ActionAccessor;
use Autoborna\CampaignBundle\EventCollector\Accessor\Event\ConditionAccessor;
use Autoborna\CampaignBundle\EventCollector\Accessor\Event\DecisionAccessor;

class EventBuilder
{
    /**
     * @return array
     */
    public static function buildActions(array $actions)
    {
        $converted = [];
        foreach ($actions as $key => $actionArray) {
            $converted[$key] = new ActionAccessor($actionArray);
        }

        return $converted;
    }

    /**
     * @return array
     */
    public static function buildConditions(array $conditions)
    {
        $converted = [];
        foreach ($conditions as $key => $conditionArray) {
            $converted[$key] = new ConditionAccessor($conditionArray);
        }

        return $converted;
    }

    /**
     * @return array
     */
    public static function buildDecisions(array $decisions)
    {
        $converted = [];
        foreach ($decisions as $key => $decisionArray) {
            $converted[$key] = new DecisionAccessor($decisionArray);
        }

        return $converted;
    }
}
