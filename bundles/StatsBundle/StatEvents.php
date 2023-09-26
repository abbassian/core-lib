<?php

namespace Autoborna\StatsBundle;

final class StatEvents
{
    /**
     * The autoborna.aggregate_stat_request event is dispatched when an aggregate stat is requested.
     *
     * The event listener receives a \Autoborna\StatsBundle\Event\AggregateStatRequestEvent instance.
     *
     * @var string
     */
    const AGGREGATE_STAT_REQUEST = 'autoborna.aggregate_stat_request';
}
