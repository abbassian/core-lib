<?php

namespace Autoborna\LeadBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Autoborna\CoreBundle\EventListener\CommonStatsSubscriber;
use Autoborna\CoreBundle\Security\Permissions\CorePermissions;
use Autoborna\LeadBundle\Entity\CompanyChangeLog;
use Autoborna\LeadBundle\Entity\CompanyLead;
use Autoborna\LeadBundle\Entity\DoNotContact;
use Autoborna\LeadBundle\Entity\FrequencyRule;
use Autoborna\LeadBundle\Entity\LeadCategory;
use Autoborna\LeadBundle\Entity\LeadDevice;
use Autoborna\LeadBundle\Entity\LeadEventLog;
use Autoborna\LeadBundle\Entity\ListLead;
use Autoborna\LeadBundle\Entity\PointsChangeLog;
use Autoborna\LeadBundle\Entity\StagesChangeLog;
use Autoborna\LeadBundle\Entity\UtmTag;

class StatsSubscriber extends CommonStatsSubscriber
{
    public function __construct(CorePermissions $security, EntityManager $entityManager)
    {
        parent::__construct($security, $entityManager);
        $this->addContactRestrictedRepositories(
            [
                CompanyChangeLog::class,
                PointsChangeLog::class,
                StagesChangeLog::class,
                CompanyLead::class,
                LeadCategory::class,
                LeadDevice::class,
                LeadEventLog::class,
                ListLead::class,
                DoNotContact::class,
                FrequencyRule::class,
                UtmTag::class,
            ]
        );
    }
}
