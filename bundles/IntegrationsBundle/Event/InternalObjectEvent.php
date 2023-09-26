<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Event;

use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Internal\Object\ObjectInterface;
use Symfony\Component\EventDispatcher\Event;

class InternalObjectEvent extends Event
{
    /**
     * @var array
     */
    private $objects = [];

    /**
     * @return Integration
     */
    public function addObject(ObjectInterface $object): void
    {
        $this->objects[] = $object;
    }

    /**
     * @return ObjectInterface[]
     */
    public function getObjects(): array
    {
        return $this->objects;
    }
}
