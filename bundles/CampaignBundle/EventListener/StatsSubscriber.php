<?php

namespace Autoborna\CampaignBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Autoborna\CampaignBundle\Entity\Lead;
use Autoborna\CampaignBundle\Entity\LeadEventLog;
use Autoborna\CoreBundle\EventListener\CommonStatsSubscriber;
use Autoborna\CoreBundle\Security\Permissions\CorePermissions;

class StatsSubscriber extends CommonStatsSubscriber
{
    public function __construct(CorePermissions $security, EntityManager $entityManager)
    {
        parent::__construct($security, $entityManager);
        $this->addContactRestrictedRepositories(
            [
                Lead::class,
                LeadEventLog::class,
            ]
        );
    }
}
