<?php

namespace Autoborna\LeadBundle\Segment\Query\Filter;

use Autoborna\LeadBundle\Segment\ContactSegmentFilter;
use Autoborna\LeadBundle\Segment\Query\QueryBuilder;

interface FilterQueryBuilderInterface
{
    /**
     * @return QueryBuilder
     */
    public function applyQuery(QueryBuilder $queryBuilder, ContactSegmentFilter $filter);

    /**
     * @return string returns the service id in the DIC container
     */
    public static function getServiceId();
}
