<?php

namespace Autoborna\EmailBundle\Event;

use Autoborna\CoreBundle\Event\BuilderEvent;
use Autoborna\EmailBundle\Entity\Email;

/**
 * Class EmailBuilderEvent.
 */
class EmailBuilderEvent extends BuilderEvent
{
    /**
     * @return Email|null
     */
    public function getEmail()
    {
        return $this->entity;
    }
}
