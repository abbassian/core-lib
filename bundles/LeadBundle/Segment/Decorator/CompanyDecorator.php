<?php

namespace Autoborna\LeadBundle\Segment\Decorator;

use Autoborna\LeadBundle\Segment\ContactSegmentFilterCrate;
use Autoborna\LeadBundle\Segment\Query\Filter\ComplexRelationValueFilterQueryBuilder;

/**
 * Class CompanyDecorator.
 */
class CompanyDecorator extends BaseDecorator
{
    /**
     * @return string
     */
    public function getRelationJoinTable()
    {
        return MAUTIC_TABLE_PREFIX.'companies_leads';
    }

    /**
     * @return string
     */
    public function getRelationJoinTableField()
    {
        return 'company_id';
    }

    /**
     * @return string
     */
    public function getQueryType(ContactSegmentFilterCrate $contactSegmentFilterCrate)
    {
        return ComplexRelationValueFilterQueryBuilder::getServiceId();
    }
}
