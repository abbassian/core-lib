<?php

namespace Autoborna\LeadBundle\Event;

use Autoborna\CoreBundle\Event\CommonEvent;
use Autoborna\LeadBundle\Entity\LeadField;

/**
 * Class LeadFieldEvent.
 */
class LeadFieldEvent extends CommonEvent
{
    /**
     * @param bool $isNew
     */
    public function __construct(LeadField &$field, $isNew = false)
    {
        $this->entity = &$field;
        $this->isNew  = $isNew;
    }

    /**
     * Returns the Field entity.
     *
     * @return LeadField
     */
    public function getField()
    {
        return $this->entity;
    }

    /**
     * Sets the LeadField entity.
     */
    public function setField(LeadField $field)
    {
        $this->entity = $field;
    }
}
