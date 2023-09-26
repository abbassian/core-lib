<?php

namespace Autoborna\LeadBundle\Event;

use Autoborna\CoreBundle\Event\CommonEvent;
use Autoborna\LeadBundle\Entity\Import;

/**
 * Class ImportEvent.
 */
class ImportEvent extends CommonEvent
{
    /**
     * @param bool $isNew
     */
    public function __construct(Import $entity, $isNew)
    {
        $this->entity = $entity;
        $this->isNew  = $isNew;
    }

    /**
     * Returns the Import entity.
     *
     * @return Import
     */
    public function getEntity()
    {
        return $this->entity;
    }
}
