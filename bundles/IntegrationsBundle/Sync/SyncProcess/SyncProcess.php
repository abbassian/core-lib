<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Sync\SyncProcess;

use Autoborna\IntegrationsBundle\Event\SyncEvent;
use Autoborna\IntegrationsBundle\Exception\IntegrationNotFoundException;
use Autoborna\IntegrationsBundle\IntegrationEvents;
use Autoborna\IntegrationsBundle\Sync\DAO\Mapping\MappingManualDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Sync\InputOptionsDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Sync\ObjectIdsDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Sync\Order\OrderDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Sync\Report\ReportDAO;
use Autoborna\IntegrationsBundle\Sync\Exception\HandlerNotSupportedException;
use Autoborna\IntegrationsBundle\Sync\Helper\MappingHelper;
use Autoborna\IntegrationsBundle\Sync\Helper\RelationsHelper;
use Autoborna\IntegrationsBundle\Sync\Helper\SyncDateHelper;
use Autoborna\IntegrationsBundle\Sync\Logger\DebugLogger;
use Autoborna\IntegrationsBundle\Sync\Notification\Notifier;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\AutobornaSyncDataExchange;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\SyncDataExchangeInterface;
use Autoborna\IntegrationsBundle\Sync\SyncProcess\Direction\Integration\IntegrationSyncProcess;
use Autoborna\IntegrationsBundle\Sync\SyncProcess\Direction\Internal\AutobornaSyncProcess;
use Autoborna\IntegrationsBundle\Sync\SyncService\SyncService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SyncProcess
{
    /**
     * @var MappingManualDAO
     */
    private $mappingManualDAO;

    /**
     * @var AutobornaSyncDataExchange
     */
    private $internalSyncDataExchange;

    /**
     * @var SyncDataExchangeInterface
     */
    private $integrationSyncDataExchange;

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
    private $integrationSyncProcess;

    /**
     * @var AutobornaSyncProcess
     */
    private $autobornaSyncProcess;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var Notifier
     */
    private $notifier;

    /**
     * @var InputOptionsDAO
     */
    private $inputOptionsDAO;

    /**
     * @var int
     */
    private $syncIteration;

    /**
     * @var SyncService
     */
    private $syncService;

    public function __construct(
        SyncDateHelper $syncDateHelper,
        MappingHelper $mappingHelper,
        RelationsHelper $relationsHelper,
        IntegrationSyncProcess $integrationSyncProcess,
        AutobornaSyncProcess $autobornaSyncProcess,
        EventDispatcherInterface $eventDispatcher,
        Notifier $notifier,
        MappingManualDAO $mappingManualDAO,
        SyncDataExchangeInterface $internalSyncDataExchange,
        SyncDataExchangeInterface $integrationSyncDataExchange,
        InputOptionsDAO $inputOptionsDAO,
        SyncService $syncService
    ) {
        $this->syncDateHelper              = $syncDateHelper;
        $this->mappingHelper               = $mappingHelper;
        $this->relationsHelper             = $relationsHelper;
        $this->integrationSyncProcess      = $integrationSyncProcess;
        $this->autobornaSyncProcess           = $autobornaSyncProcess;
        $this->eventDispatcher             = $eventDispatcher;
        $this->notifier                    = $notifier;
        $this->mappingManualDAO            = $mappingManualDAO;
        $this->internalSyncDataExchange    = $internalSyncDataExchange;
        $this->integrationSyncDataExchange = $integrationSyncDataExchange;
        $this->inputOptionsDAO             = $inputOptionsDAO;
        $this->syncService                 = $syncService;
    }

    /**
     * Execute sync with integration.
     */
    public function execute(): void
    {
        defined('MAUTIC_INTEGRATION_ACTIVE_SYNC') or define('MAUTIC_INTEGRATION_ACTIVE_SYNC', 1);

        // Setup/prepare for the sync
        $this->syncDateHelper->setSyncDateTimes($this->inputOptionsDAO->getStartDateTime(), $this->inputOptionsDAO->getEndDateTime());
        $this->integrationSyncProcess->setupSync($this->inputOptionsDAO, $this->mappingManualDAO, $this->integrationSyncDataExchange);
        $this->autobornaSyncProcess->setupSync($this->inputOptionsDAO, $this->mappingManualDAO, $this->internalSyncDataExchange);

        if ($this->inputOptionsDAO->pullIsEnabled()) {
            $this->executeIntegrationSync();
        }

        if ($this->inputOptionsDAO->pushIsEnabled()) {
            $this->executeInternalSync();
        }

        // Tell listeners sync is done
        $this->eventDispatcher->dispatch(
            IntegrationEvents::INTEGRATION_POST_EXECUTE,
            new SyncEvent($this->inputOptionsDAO)
        );
    }

    private function executeIntegrationSync(): void
    {
        $this->syncIteration = 1;
        do {
            DebugLogger::log(
                $this->mappingManualDAO->getIntegration(),
                sprintf('Integration to Autoborna; syncing iteration %s', $this->syncIteration),
                __CLASS__.':'.__FUNCTION__
            );

            $syncReport = $this->integrationSyncProcess->getSyncReport($this->syncIteration);
            if (!$syncReport->shouldSync()) {
                DebugLogger::log(
                    $this->mappingManualDAO->getIntegration(),
                    'Integration to Autoborna; no objects were mapped to be synced',
                    __CLASS__.':'.__FUNCTION__
                );

                break;
            }

            // Update the mappings in case objects have been converted such as Lead -> Contact
            $this->mappingHelper->remapIntegrationObjects($syncReport->getRemappedObjects());

            // Maps relations, synchronizes missing objects if necessary
            $this->manageRelations($syncReport);

            // Convert the integrations' report into an "order" or instructions for Autoborna
            $syncOrder = $this->autobornaSyncProcess->getSyncOrder($syncReport, $this->inputOptionsDAO->isFirstTimeSync(), $this->mappingManualDAO);
            if (!$syncOrder->shouldSync()) {
                DebugLogger::log(
                    $this->mappingManualDAO->getIntegration(),
                    'Integration to Autoborna; no object changes were recorded possible due to field direction configurations',
                    __CLASS__.':'.__FUNCTION__
                );

                break;
            }

            DebugLogger::log(
                $this->mappingManualDAO->getIntegration(),
                sprintf(
                    'Integration to Autoborna; syncing %d total objects',
                    $syncOrder->getObjectCount()
                ),
                __CLASS__.':'.__FUNCTION__
            );

            // Execute the sync instructions
            $this->internalSyncDataExchange->executeSyncOrder($syncOrder);

            if ($this->shouldStopIntegrationSync()) {
                break;
            }

            // Fetch the next iteration/batch
            ++$this->syncIteration;
        } while (true);
    }

    private function executeInternalSync(): void
    {
        $this->syncIteration = 1;
        do {
            DebugLogger::log(
                $this->mappingManualDAO->getIntegration(),
                sprintf('Autoborna to integration; syncing iteration %s', $this->syncIteration),
                __CLASS__.':'.__FUNCTION__
            );

            $syncReport = $this->autobornaSyncProcess->getSyncReport($this->syncIteration, $this->inputOptionsDAO);

            if (!$syncReport->shouldSync()) {
                DebugLogger::log(
                    $this->mappingManualDAO->getIntegration(),
                    'Autoborna to integration; no objects were mapped to be synced',
                    __CLASS__.':'.__FUNCTION__
                );

                break;
            }

            // Convert the internal report into an "order" or instructions for the integration
            $syncOrder = $this->integrationSyncProcess->getSyncOrder($syncReport, $this->inputOptionsDAO->isFirstTimeSync(), $this->mappingManualDAO);

            if (!$syncOrder->shouldSync()) {
                DebugLogger::log(
                    $this->mappingManualDAO->getIntegration(),
                    'Autoborna to integration; no object changes were recorded possible due to field direction configurations',
                    __CLASS__.':'.__FUNCTION__
                );

                // Finalize notifications such as injecting user notifications
                $this->notifier->finalizeNotifications();

                break;
            }

            DebugLogger::log(
                $this->mappingManualDAO->getIntegration(),
                sprintf(
                    'Autoborna to integration; syncing %d total objects',
                    $syncOrder->getObjectCount()
                ),
                __CLASS__.':'.__FUNCTION__
            );

            // Execute the sync instructions
            $this->integrationSyncDataExchange->executeSyncOrder($syncOrder);

            // Save mappings and cleanup
            $this->finalizeSync($syncOrder);

            // Fetch the next iteration/batch
            ++$this->syncIteration;
        } while (true);
    }

    private function manageRelations(ReportDAO $syncReport): void
    {
        // Map relations
        $this->relationsHelper->processRelations($this->mappingManualDAO, $syncReport);

        // Relation objects we need to synchronize
        $objectsToSynchronize = $this->relationsHelper->getObjectsToSynchronize();

        if (!empty($objectsToSynchronize)) {
            $this->synchronizeMissingObjects($objectsToSynchronize, $syncReport);
        }
    }

    private function synchronizeMissingObjects(array $objectsToSynchronize, ReportDAO $syncReport): void
    {
        $inputOptions = $this->getInputOptionsForObjects($objectsToSynchronize);

        // We need to synchronize missing relation ids
        $this->processParallelSync($inputOptions);

        // Now we can map relations for objects we have just synchronised
        $this->relationsHelper->processRelations($this->mappingManualDAO, $syncReport);
    }

    /**
     * @throws \Autoborna\IntegrationsBundle\Exception\InvalidValueException
     */
    private function getInputOptionsForObjects(array $objectsToSynchronize): InputOptionsDAO
    {
        $autobornaObjectIds = new ObjectIdsDAO();

        foreach ($objectsToSynchronize as $object) {
            $autobornaObjectIds->addObjectId($object->getObject(), $object->getObjectId());
        }

        $integration  = $this->mappingManualDAO->getIntegration();

        return new InputOptionsDAO([
            'integration'           => $integration,
            'integration-object-id' => $autobornaObjectIds,
        ]);
    }

    /**
     * @param $inputOptions
     *
     * @throws IntegrationNotFoundException
     */
    private function processParallelSync($inputOptions): void
    {
        $currentSyncProcess = clone $this->integrationSyncProcess;
        $this->syncService->processIntegrationSync($inputOptions);

        // We need to bring back current $inputOptions which were overwritten by new sync
        $this->integrationSyncProcess = $currentSyncProcess;
    }

    private function shouldStopIntegrationSync(): bool
    {
        // We don't want to iterate sync for specific ids
        return null !== $this->inputOptionsDAO->getIntegrationObjectIds();
    }

    /**
     * @throws IntegrationNotFoundException
     * @throws HandlerNotSupportedException
     */
    private function finalizeSync(OrderDAO $syncOrder): void
    {
        // Save the mappings between Autoborna objects and the integration's objects
        $this->mappingHelper->saveObjectMappings($syncOrder->getObjectMappings());

        // Remap integration objects to Autoborna objects if applicable
        $this->mappingHelper->remapIntegrationObjects($syncOrder->getRemappedObjects());

        // Update last sync dates on existing object mappings
        $this->mappingHelper->updateObjectMappings($syncOrder->getUpdatedObjectMappings());

        // Tell sync that these objects have been deleted and not to continue re-syncing them
        $this->mappingHelper->markAsDeleted($syncOrder->getDeletedObjects());

        // Inject notifications
        $this->notifier->noteAutobornaSyncIssue($syncOrder->getNotifications());

        // Cleanup field tracking for successfully synced objects
        $this->internalSyncDataExchange->cleanupProcessedObjects($syncOrder->getSuccessfullySyncedObjects());
    }
}
