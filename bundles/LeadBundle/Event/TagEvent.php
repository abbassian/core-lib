<?php

namespace Autoborna\LeadBundle\Event;

use Autoborna\CoreBundle\Event\CommonEvent;
use Autoborna\LeadBundle\Entity\Tag;

/**
 * Class TagEvent.
 */
class TagEvent extends CommonEvent
{
    /**
     * @param bool $isNew
     */
    public function __construct(Tag $tag, $isNew = false)
    {
        $this->entity = $tag;
        $this->isNew  = $isNew;
    }

    /**
     * Returns the Tag entity.
     *
     * @return Tag
     */
    public function getTag()
    {
        return $this->entity;
    }
}
