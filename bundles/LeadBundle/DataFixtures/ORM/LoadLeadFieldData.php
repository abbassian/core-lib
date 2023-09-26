<?php

namespace Autoborna\LeadBundle\DataFixtures\ORM;

use Autoborna\InstallBundle\InstallFixtures\ORM\LeadFieldData;

/**
 * Class LoadLeadFieldData.
 */
class LoadLeadFieldData extends LeadFieldData
{
    /**
     * {@inheritdoc}
     */
    public static function getGroups(): array
    {
        return [];
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 4;
    }
}
