<?php

namespace Autoborna\EmailBundle\EventListener;

use Autoborna\EmailBundle\Helper\StatsCollectionHelper;
use Autoborna\StatsBundle\Event\AggregateStatRequestEvent;
use Autoborna\StatsBundle\StatEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GraphAggregateStatsSubscriber implements EventSubscriberInterface
{
    /**
     * @var StatsCollectionHelper
     */
    private $statsCollectionHelper;

    /**
     * GraphAggregateStatsSubscriber constructor.
     */
    public function __construct(StatsCollectionHelper $statsCollectionHelper)
    {
        $this->statsCollectionHelper = $statsCollectionHelper;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            StatEvents::AGGREGATE_STAT_REQUEST => ['onStatRequest', 0],
        ];
    }

    public function onStatRequest(AggregateStatRequestEvent $event)
    {
        if (!$event->checkContextPrefix(StatsCollectionHelper::GENERAL_STAT_PREFIX.'-')) {
            return;
        }

        $this->statsCollectionHelper->generateStats(
            $event->getStatName(),
            $event->getFromDateTime(),
            $event->getToDateTime(),
            $event->getOptions(),
            $event->getStatCollection()
        );

        $event->statsCollected();
    }
}
