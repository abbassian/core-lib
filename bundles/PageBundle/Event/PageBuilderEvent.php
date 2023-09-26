<?php

namespace Autoborna\PageBundle\Event;

use Autoborna\CoreBundle\Event\BuilderEvent;
use Autoborna\PageBundle\Entity\Page;

/**
 * Class PageBuilderEvent.
 */
class PageBuilderEvent extends BuilderEvent
{
    /**
     * @return Page|null
     */
    public function getPage()
    {
        return $this->entity;
    }
}
