<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Sync\SyncService;

use GuzzleHttp\Exception\ClientException;
use Autoborna\IntegrationsBundle\Helper\SyncIntegrationsHelper;
use Autoborna\IntegrationsBundle\Sync\DAO\Sync\InputOptionsDAO;
use Autoborna\IntegrationsBundle\Sync\Helper\MappingHelper;
use Autoborna\IntegrationsBundle\Sync\Helper\RelationsHelper;
use Autoborna\IntegrationsBundle\Sync\Helper\SyncDateHelper;
use Autoborna\IntegrationsBundle\Sync\Logger\DebugLogger;
use Autoborna\IntegrationsBundle\Sync\Notification\Notifier;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\AutobornaSyncDataExchange;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\SyncDataExchangeInterface;
use Autoborna\IntegrationsBundle\Sync\SyncProcess\Direction\Integration\IntegrationSyncProcess;
use Autoborna\IntegrationsBundle\Sync\SyncProcess\Direction\Internal\AutobornaSyncProcess;
use Autoborna\IntegrationsBundle\Sync\SyncProcess\SyncProcess;
use Psr\Log\LogLevel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class SyncService implements SyncServiceInterface
{
    /**
     * @var SyncDataExchangeInterface
     */
    private $internalSyncDataExchange;

    /**
     * @var SyncDateHelper
     */
    private $syncDateHelper;

    /**
     * @var MappingHelper
     */
    private $mappingHelper;

    /**
     * @var RelationsHelper
     */
    private $relationsHelper;

    /**
     * @var IntegrationSyncProcess
     */
    private $integratinSyncProcess;

    /**
     * @var AutobornaSyncProcess
     */
    private $autobornaSyncProcess;

    /**
     * @var SyncIntegrationsHelper
     */
    private $syncIntegrationsHelper;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var Notifier
     */
    private $notifier;

    public function __construct(
        AutobornaSyncDataExchange $internalSyncDataExchange,
        SyncDateHelper $syncDateHelper,
        MappingHelper $mappingHelper,
        RelationsHelper $relationsHelper,
        SyncIntegrationsHelper $syncIntegrationsHelper,
        EventDispatcherInterface $eventDispatcher,
        Notifier $notifier,
        IntegrationSyncProcess $integrationSyncProcess,
        AutobornaSyncProcess $autobornaSyncProcess
    ) {
        $this->internalSyncDataExchange = $internalSyncDataExchange;
        $this->syncDateHelper           = $syncDateHelper;
        $this->mappingHelper            = $mappingHelper;
        $this->relationsHelper          = $relationsHelper;
        $this->syncIntegrationsHelper   = $syncIntegrationsHelper;
        $this->eventDispatcher          = $eventDispatcher;
        $this->notifier                 = $notifier;
        $this->integratinSyncProcess    = $integrationSyncProcess;
        $this->autobornaSyncProcess        = $autobornaSyncProcess;
    }

    /**
     * @throws \Autoborna\IntegrationsBundle\Exception\IntegrationNotFoundException
     */
    public function processIntegrationSync(InputOptionsDAO $inputOptionsDAO): void
    {
        $integrationSyncProcess = new SyncProcess(
            $this->syncDateHelper,
            $this->mappingHelper,
            $this->relationsHelper,
            $this->integratinSyncProcess,
            $this->autobornaSyncProcess,
            $this->eventDispatcher,
            $this->notifier,
            $this->syncIntegrationsHelper->getMappingManual($inputOptionsDAO->getIntegration()),
            $this->internalSyncDataExchange,
            $this->syncIntegrationsHelper->getSyncDataExchange($inputOptionsDAO->getIntegration()),
            $inputOptionsDAO,
            $this
        );

        DebugLogger::log(
            $inputOptionsDAO->getIntegration(),
            sprintf(
                'Starting %s sync from %s date/time',
                $inputOptionsDAO->isFirstTimeSync() ? 'first time' : 'subsequent',
                $inputOptionsDAO->getStartDateTime() ? $inputOptionsDAO->getStartDateTime()->format('Y-m-d H:i:s') : 'yet to be determined'
            ),
            __CLASS__.':'.__FUNCTION__
        );

        try {
            $integrationSyncProcess->execute();
        } catch (ClientException $exception) {
            // The sync failed to communicate with the integration so log it
            DebugLogger::log($inputOptionsDAO->getIntegration(), $exception->getMessage(), null, [], LogLevel::ERROR);
        }
    }

    public function initiateDebugLogger(DebugLogger $logger): void
    {
        // Yes it's a hack to prevent from having to pass the logger as a dependency into dozens of classes
        // So not doing anything with the logger, just need Symfony to initiate the service
    }
}
