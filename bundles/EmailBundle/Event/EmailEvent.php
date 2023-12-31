<?php

namespace Autoborna\EmailBundle\Event;

use Autoborna\CoreBundle\Event\CommonEvent;
use Autoborna\EmailBundle\Entity\Email;

/**
 * Class EmailEvent.
 */
class EmailEvent extends CommonEvent
{
    /**
     * @param bool $isNew
     */
    public function __construct(Email &$email, $isNew = false)
    {
        $this->entity = &$email;
        $this->isNew  = $isNew;
    }

    /**
     * Returns the Email entity.
     *
     * @return Email
     */
    public function getEmail()
    {
        return $this->entity;
    }

    /**
     * Sets the Email entity.
     */
    public function setEmail(Email $email)
    {
        $this->entity = $email;
    }
}
