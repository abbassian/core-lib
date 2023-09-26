<?php

namespace Autoborna\PluginBundle\Entity;

use Autoborna\CoreBundle\Entity\CommonRepository;

/**
 * IntegrationRepository.
 */
class IntegrationRepository extends CommonRepository
{
    public function getIntegrations()
    {
        $services = $this->createQueryBuilder('i')
            ->join('i.plugin', 'p')
            ->getQuery()
            ->getResult();

        $results = [];
        foreach ($services as $s) {
            $results[$s->getName()] = $s;
        }

        return $results;
    }

    /**
     * Get core (no plugin) integrations.
     */
    public function getCoreIntegrations()
    {
        $services = $this->createQueryBuilder('i')
            ->getQuery()
            ->getResult();

        $results = [];
        foreach ($services as $s) {
            $results[$s->getName()] = $s;
        }

        return $results;
    }
}
