<?php

namespace Autoborna\CoreBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Autoborna\CoreBundle\Entity\AuditLog;
use Autoborna\CoreBundle\Entity\IpAddress;
use Autoborna\CoreBundle\Security\Permissions\CorePermissions;

class StatsSubscriber extends CommonStatsSubscriber
{
    public function __construct(CorePermissions $security, EntityManager $entityManager)
    {
        parent::__construct($security, $entityManager);
        $this->repositories['AutobornaCoreBundle:AuditLog'] = $entityManager->getRepository(AuditLog::class);
        $this->permissions['AutobornaCoreBundle:AuditLog']  = ['admin'];

        $this->repositories['AutobornaCoreBundle:IpAddress'] = $entityManager->getRepository(IpAddress::class);
    }
}
