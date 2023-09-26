<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Integration\Interfaces;

use Autoborna\IntegrationsBundle\Sync\DAO\Mapping\MappingManualDAO;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\SyncDataExchangeInterface;

interface SyncInterface extends IntegrationInterface
{
    public function getMappingManual(): MappingManualDAO;

    public function getSyncDataExchange(): SyncDataExchangeInterface;
}
