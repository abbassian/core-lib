<?php

namespace Autoborna\UserBundle\Tests\EventListener;

use Autoborna\CoreBundle\Helper\IpLookupHelper;
use Autoborna\CoreBundle\Model\AuditLogModel;
use Autoborna\UserBundle\Entity\User;
use Autoborna\UserBundle\Event\LoginEvent;
use Autoborna\UserBundle\EventListener\SecuritySubscriber;
use Autoborna\UserBundle\UserEvents;

class SecuritySubscriberTest extends \PHPUnit\Framework\TestCase
{
    public function testGetSubscribedEvents()
    {
        $ipLookupHelper = $this->createMock(IpLookupHelper::class);
        $auditLogModel  = $this->createMock(AuditLogModel::class);
        $subscriber     = new SecuritySubscriber($ipLookupHelper, $auditLogModel);

        $this->assertEquals(
            [
                UserEvents::USER_LOGIN => ['onSecurityInteractiveLogin', 0],
            ],
            $subscriber->getSubscribedEvents()
        );
    }

    public function testOnSecurityInteractiveLogin()
    {
        $userId   = 132564;
        $userName = 'John Doe';
        $ip       = '125.55.45.21';
        $log      = [
            'bundle'    => 'user',
            'object'    => 'security',
            'objectId'  => $userId,
            'action'    => 'login',
            'details'   => ['username' => $userName],
            'ipAddress' => $ip,
        ];

        $ipLookupHelper = $this->createMock(IpLookupHelper::class);
        $ipLookupHelper->expects($this->once())
            ->method('getIpAddressFromRequest')
            ->willReturn($ip);
        $auditLogModel = $this->createMock(AuditLogModel::class);
        $auditLogModel->expects($this->once())
            ->method('writeToLog')
            ->with($log);
        $user = $this->createMock(User::class);
        $user->expects($this->once())
            ->method('getId')
            ->willReturn($userId);
        $user->expects($this->once())
            ->method('getUserName')
            ->willReturn($userName);
        $event = $this->createMock(LoginEvent::class);
        $event->expects($this->exactly(2))
            ->method('getUser')
            ->willReturn($user);
        $subscriber = new SecuritySubscriber($ipLookupHelper, $auditLogModel);

        $subscriber->onSecurityInteractiveLogin($event);
    }
}
