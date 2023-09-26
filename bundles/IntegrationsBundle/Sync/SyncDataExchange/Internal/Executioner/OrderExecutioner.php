<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Internal\Executioner;

use Autoborna\IntegrationsBundle\Event\InternalObjectCreateEvent;
use Autoborna\IntegrationsBundle\Event\InternalObjectUpdateEvent;
use Autoborna\IntegrationsBundle\IntegrationEvents;
use Autoborna\IntegrationsBundle\Sync\DAO\Sync\Order\OrderDAO;
use Autoborna\IntegrationsBundle\Sync\Exception\ObjectNotFoundException;
use Autoborna\IntegrationsBundle\Sync\Helper\MappingHelper;
use Autoborna\IntegrationsBundle\Sync\Logger\DebugLogger;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Internal\ObjectProvider;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\AutobornaSyncDataExchange;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class OrderExecutioner
{
    /**
     * @var MappingHelper
     */
    private $mappingHelper;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var ObjectProvider
     */
    private $objectProvider;

    public function __construct(
        MappingHelper $mappingHelper,
        EventDispatcherInterface $dispatcher,
        ObjectProvider $objectProvider
    ) {
        $this->mappingHelper  = $mappingHelper;
        $this->dispatcher     = $dispatcher;
        $this->objectProvider = $objectProvider;
    }

    public function execute(OrderDAO $syncOrderDAO): void
    {
        $identifiedObjects   = $syncOrderDAO->getIdentifiedObjects();
        $unidentifiedObjects = $syncOrderDAO->getUnidentifiedObjects();

        foreach ($identifiedObjects as $objectName => $updateObjects) {
            $this->updateObjects($objectName, $updateObjects, $syncOrderDAO);
        }

        foreach ($unidentifiedObjects as $objectName => $createObjects) {
            $this->createObjects($objectName, $createObjects);
        }
    }

    private function updateObjects(string $objectName, array $updateObjects, OrderDAO $syncOrderDAO): void
    {
        $updateCount = count($updateObjects);
        DebugLogger::log(
            AutobornaSyncDataExchange::NAME,
            sprintf(
                'Updating %d %s object(s)',
                $updateCount,
                $objectName
            ),
            __CLASS__.':'.__FUNCTION__
        );

        if (0 === $updateCount) {
            return;
        }

        try {
            $event = new InternalObjectUpdateEvent(
                $this->objectProvider->getObjectByName($objectName),
                $syncOrderDAO->getIdentifiedObjectIds($objectName),
                $updateObjects
            );
        } catch (ObjectNotFoundException $e) {
            DebugLogger::log(
                AutobornaSyncDataExchange::NAME,
                $objectName,
                __CLASS__.':'.__FUNCTION__
            );
        }

        $this->dispatcher->dispatch(IntegrationEvents::INTEGRATION_UPDATE_INTERNAL_OBJECTS, $event);
        $this->mappingHelper->updateObjectMappings($event->getUpdatedObjectMappings());
    }

    private function createObjects(string $objectName, array $createObjects): void
    {
        $createCount = count($createObjects);

        DebugLogger::log(
            AutobornaSyncDataExchange::NAME,
            sprintf(
                'Creating %d %s object(s)',
                $createCount,
                $objectName
            ),
            __CLASS__.':'.__FUNCTION__
        );

        if (0 === $createCount) {
            return;
        }

        try {
            $event = new InternalObjectCreateEvent(
                $this->objectProvider->getObjectByName($objectName),
                $createObjects
            );
        } catch (ObjectNotFoundException $e) {
            DebugLogger::log(
                AutobornaSyncDataExchange::NAME,
                $objectName,
                __CLASS__.':'.__FUNCTION__
            );
        }

        $this->dispatcher->dispatch(IntegrationEvents::INTEGRATION_CREATE_INTERNAL_OBJECTS, $event);
        $this->mappingHelper->saveObjectMappings($event->getObjectMappings());
    }
}
