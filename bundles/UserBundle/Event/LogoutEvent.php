<?php

namespace Autoborna\UserBundle\Event;

use Autoborna\UserBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class LogoutEvent.
 */
class LogoutEvent extends Event
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var array
     */
    private $session = [];

    /**
     * @var Request
     */
    private $request;

    /**
     * LogoutEvent constructor.
     */
    public function __construct(User $user, Request $request)
    {
        $this->user    = $user;
        $this->request = $request;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add value to session after it's been cleared.
     *
     * @param $key
     * @param $value
     */
    public function setPostSessionItem($key, $value)
    {
        $this->session[$key] = $value;
    }

    /**
     * Get session items to be added after session has been cleared.
     *
     * @return array
     */
    public function getPostSessionItems()
    {
        return $this->session;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
