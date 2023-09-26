<?php

namespace Autoborna\PageBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Autoborna\CoreBundle\EventListener\CommonStatsSubscriber;
use Autoborna\CoreBundle\Security\Permissions\CorePermissions;
use Autoborna\PageBundle\Entity\Hit;
use Autoborna\PageBundle\Entity\Redirect;
use Autoborna\PageBundle\Entity\Trackable;
use Autoborna\PageBundle\Entity\VideoHit;

class StatsSubscriber extends CommonStatsSubscriber
{
    public function __construct(CorePermissions $security, EntityManager $entityManager)
    {
        parent::__construct($security, $entityManager);
        $this->addContactRestrictedRepositories(
            [
                Hit::class,
                VideoHit::class,
            ]
        );

        $this->repositories[] = $entityManager->getRepository(Redirect::class);
        $this->repositories[] = $entityManager->getRepository(Trackable::class);
    }
}
