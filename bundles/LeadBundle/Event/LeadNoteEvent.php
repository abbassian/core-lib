<?php

namespace Autoborna\LeadBundle\Event;

use Autoborna\CoreBundle\Event\CommonEvent;
use Autoborna\LeadBundle\Entity\Lead;
use Autoborna\LeadBundle\Entity\LeadNote;

/**
 * Class LeadNoteEvent.
 */
class LeadNoteEvent extends CommonEvent
{
    /**
     * @param bool $isNew
     */
    public function __construct(LeadNote $note, $isNew = false)
    {
        $this->entity = $note;
        $this->isNew  = $isNew;
    }

    /**
     * Returns the LeadNote entity.
     *
     * @return LeadNote
     */
    public function getNote()
    {
        return $this->entity;
    }

    /**
     * Sets the LeadNote entity.
     */
    public function setLeadNote(LeadNote $note)
    {
        $this->entity = $note;
    }

    /**
     * Returns the Lead.
     *
     * @return Lead
     */
    public function getLead()
    {
        return $this->entity->getLead();
    }
}
