<?php

namespace Autoborna\SmsBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Autoborna\CoreBundle\EventListener\CommonStatsSubscriber;
use Autoborna\CoreBundle\Security\Permissions\CorePermissions;
use Autoborna\SmsBundle\Entity\Stat;

class StatsSubscriber extends CommonStatsSubscriber
{
    public function __construct(CorePermissions $security, EntityManager $entityManager)
    {
        parent::__construct($security, $entityManager);
        $this->addContactRestrictedRepositories([Stat::class]);
    }
}
