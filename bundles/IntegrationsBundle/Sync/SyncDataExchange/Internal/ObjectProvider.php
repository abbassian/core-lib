<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Internal;

use Autoborna\IntegrationsBundle\Event\InternalObjectEvent;
use Autoborna\IntegrationsBundle\IntegrationEvents;
use Autoborna\IntegrationsBundle\Sync\Exception\ObjectNotFoundException;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Internal\Object\ObjectInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ObjectProvider
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * Cached internal objects.
     *
     * @var ObjectInterface[]
     */
    private $objects = [];

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @throws ObjectNotFoundException
     */
    public function getObjectByName(string $name): ObjectInterface
    {
        $this->collectObjects();

        foreach ($this->objects as $object) {
            if ($object->getName() === $name) {
                return $object;
            }
        }

        throw new ObjectNotFoundException("Internal object '{$name}' was not found");
    }

    /**
     * @throws ObjectNotFoundException
     */
    public function getObjectByEntityName(string $entityName): ObjectInterface
    {
        $this->collectObjects();

        foreach ($this->objects as $object) {
            if ($object->getEntityName() === $entityName) {
                return $object;
            }
        }

        throw new ObjectNotFoundException("Internal object was not found for entity '{$entityName}'");
    }

    /**
     * Dispatches an event to collect all internal objects.
     * It caches the objects to a local property so it won't dispatch every time but only once.
     */
    private function collectObjects(): void
    {
        if (empty($this->objects)) {
            $event = new InternalObjectEvent();
            $this->dispatcher->dispatch(IntegrationEvents::INTEGRATION_COLLECT_INTERNAL_OBJECTS, $event);
            $this->objects = $event->getObjects();
        }
    }
}
