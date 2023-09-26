<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Sync\DAO\Sync\Order;

class NotificationDAO
{
    /**
     * @var ObjectChangeDAO
     */
    private $objectChangeDAO;

    /**
     * @var string
     */
    private $message;

    public function __construct(ObjectChangeDAO $objectChangeDAO, string $message)
    {
        $this->objectChangeDAO = $objectChangeDAO;
        $this->message         = $message;
    }

    /**
     * @return ObjectChangeDAO
     */
    public function getAutobornaObject(): string
    {
        return $this->objectChangeDAO->getMappedObject();
    }

    public function getAutobornaObjectId(): int
    {
        return (int) $this->objectChangeDAO->getMappedObjectId();
    }

    public function getIntegration(): string
    {
        return $this->objectChangeDAO->getIntegration();
    }

    public function getIntegrationObject(): string
    {
        return $this->objectChangeDAO->getObject();
    }

    /**
     * @return mixed
     */
    public function getIntegrationObjectId()
    {
        return $this->objectChangeDAO->getObjectId();
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
