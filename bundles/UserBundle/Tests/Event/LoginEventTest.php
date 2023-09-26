<?php

namespace Autoborna\UserBundle\Tests\Event;

use Autoborna\UserBundle\Entity\User;
use Autoborna\UserBundle\Event\LoginEvent;

class LoginEventTest extends \PHPUnit\Framework\TestCase
{
    public function testGetUser()
    {
        $user  = $this->createMock(User::class);
        $event = new LoginEvent($user);

        $this->assertEquals($user, $event->getUser());
    }
}
