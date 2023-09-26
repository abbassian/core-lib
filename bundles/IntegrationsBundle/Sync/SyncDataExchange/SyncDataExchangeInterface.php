<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Sync\SyncDataExchange;

use Autoborna\IntegrationsBundle\Sync\DAO\Sync\Order\OrderDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Sync\Report\ReportDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Sync\Request\RequestDAO;

interface SyncDataExchangeInterface
{
    /**
     * Sync to integration.
     */
    public function getSyncReport(RequestDAO $requestDAO): ReportDAO;

    /**
     * Sync from integration.
     */
    public function executeSyncOrder(OrderDAO $syncOrderDAO);
}
