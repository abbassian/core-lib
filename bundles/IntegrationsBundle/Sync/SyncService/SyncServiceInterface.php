<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Sync\SyncService;

use Autoborna\IntegrationsBundle\Sync\DAO\Sync\InputOptionsDAO;

interface SyncServiceInterface
{
    public function processIntegrationSync(InputOptionsDAO $inputOptionsDAO);
}
