<?php

namespace Autoborna\UserBundle\Event;

use Autoborna\CoreBundle\Event\CommonEvent;
use Autoborna\UserBundle\Entity\Role;

/**
 * Class RoleEvent.
 */
class RoleEvent extends CommonEvent
{
    /**
     * @param bool $isNew
     */
    public function __construct(Role &$role, $isNew = false)
    {
        $this->entity = &$role;
        $this->isNew  = $isNew;
    }

    /**
     * Returns the Role entity.
     *
     * @return Role
     */
    public function getRole()
    {
        return $this->entity;
    }

    /**
     * Sets the Role entity.
     */
    public function setRole(Role $role)
    {
        $this->entity = $role;
    }
}
