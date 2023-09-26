<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Sync\Notification\Helper;

use Autoborna\IntegrationsBundle\Event\InternalObjectOwnerEvent;
use Autoborna\IntegrationsBundle\IntegrationEvents;
use Autoborna\IntegrationsBundle\Sync\Exception\ObjectNotFoundException;
use Autoborna\IntegrationsBundle\Sync\Exception\ObjectNotSupportedException;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Internal\Object\ObjectInterface;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Internal\ObjectProvider;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\AutobornaSyncDataExchange;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class OwnerProvider
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var ObjectProvider
     */
    private $objectProvider;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        ObjectProvider $objectProvider
    ) {
        $this->dispatcher     = $dispatcher;
        $this->objectProvider = $objectProvider;
    }

    /**
     * @param int[] $objectIds
     *
     * @return ObjectInterface
     *
     * @throws ObjectNotSupportedException
     */
    public function getOwnersForObjectIds(string $objectName, array $objectIds): array
    {
        if (empty($objectIds)) {
            return [];
        }

        try {
            $object = $this->objectProvider->getObjectByName($objectName);
        } catch (ObjectNotFoundException $e) {
            // Throw this exception for BC.
            throw new ObjectNotSupportedException(AutobornaSyncDataExchange::NAME, $objectName);
        }

        $event = new InternalObjectOwnerEvent($object, $objectIds);

        $this->dispatcher->dispatch(IntegrationEvents::INTEGRATION_FIND_OWNER_IDS, $event);

        return $event->getOwners();
    }
}
