<?php

namespace Autoborna\AssetBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Autoborna\AssetBundle\Entity\Download;
use Autoborna\CoreBundle\EventListener\CommonStatsSubscriber;
use Autoborna\CoreBundle\Security\Permissions\CorePermissions;

class StatsSubscriber extends CommonStatsSubscriber
{
    public function __construct(CorePermissions $security, EntityManager $entityManager)
    {
        parent::__construct($security, $entityManager);
        $this->addContactRestrictedRepositories([Download::class]);
    }
}
