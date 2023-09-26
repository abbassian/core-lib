<?php

namespace Autoborna\PointBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Autoborna\CoreBundle\EventListener\CommonStatsSubscriber;
use Autoborna\CoreBundle\Security\Permissions\CorePermissions;
use Autoborna\PointBundle\Entity\LeadPointLog;
use Autoborna\PointBundle\Entity\LeadTriggerLog;

class StatsSubscriber extends CommonStatsSubscriber
{
    public function __construct(CorePermissions $security, EntityManager $entityManager)
    {
        parent::__construct($security, $entityManager);
        $this->addContactRestrictedRepositories(
            [
                LeadPointLog::class,
                LeadTriggerLog::class,
            ]
        );
    }
}
