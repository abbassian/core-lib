<?php

namespace Autoborna\LeadBundle\Event;

use Autoborna\LeadBundle\Entity\LeadList;
use Autoborna\LeadBundle\Segment\Query\QueryBuilder;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class LeadListQueryBuilderGeneratedEvent.
 */
class LeadListQueryBuilderGeneratedEvent extends Event
{
    /**
     * @var LeadList
     */
    private $segment;

    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    public function __construct(LeadList $segment, QueryBuilder $queryBuilder)
    {
        $this->segment      = $segment;
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @return LeadList
     */
    public function getSegment()
    {
        return $this->segment;
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }
}
