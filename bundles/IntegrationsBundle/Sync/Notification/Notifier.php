<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Sync\Notification;

use Autoborna\IntegrationsBundle\Exception\IntegrationNotFoundException;
use Autoborna\IntegrationsBundle\Helper\ConfigIntegrationsHelper;
use Autoborna\IntegrationsBundle\Helper\SyncIntegrationsHelper;
use Autoborna\IntegrationsBundle\Integration\Interfaces\ConfigFormSyncInterface;
use Autoborna\IntegrationsBundle\Sync\DAO\Sync\Order\NotificationDAO;
use Autoborna\IntegrationsBundle\Sync\Exception\HandlerNotSupportedException;
use Autoborna\IntegrationsBundle\Sync\Notification\Handler\HandlerContainer;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\AutobornaSyncDataExchange;

class Notifier
{
    /**
     * @var HandlerContainer
     */
    private $handlerContainer;

    /**
     * @var SyncIntegrationsHelper
     */
    private $syncIntegrationsHelper;

    /**
     * @var ConfigIntegrationsHelper
     */
    private $configIntegrationsHelper;

    public function __construct(
        HandlerContainer $handlerContainer,
        SyncIntegrationsHelper $syncIntegrationsHelper,
        ConfigIntegrationsHelper $configIntegrationsHelper
    ) {
        $this->handlerContainer         = $handlerContainer;
        $this->syncIntegrationsHelper   = $syncIntegrationsHelper;
        $this->configIntegrationsHelper = $configIntegrationsHelper;
    }

    /**
     * @param NotificationDAO[] $notifications
     * @param string            $integrationHandler
     *
     * @throws HandlerNotSupportedException
     * @throws IntegrationNotFoundException
     */
    public function noteAutobornaSyncIssue(array $notifications, $integrationHandler = AutobornaSyncDataExchange::NAME): void
    {
        foreach ($notifications as $notification) {
            $handler = $this->handlerContainer->getHandler($integrationHandler, $notification->getAutobornaObject());

            $integrationDisplayName = $this->syncIntegrationsHelper->getIntegration($notification->getIntegration())->getDisplayName();
            $objectDisplayName      = $this->getObjectDisplayName($notification->getIntegration(), $notification->getIntegrationObject());

            $handler->writeEntry($notification, $integrationDisplayName, $objectDisplayName);
        }
    }

    /**
     * Finalizes notifications such as pushing summary entries to the user notifications.
     */
    public function finalizeNotifications(): void
    {
        foreach ($this->handlerContainer->getHandlers() as $handler) {
            $handler->finalize();
        }
    }

    /**
     * @return string
     */
    private function getObjectDisplayName(string $integration, string $object)
    {
        try {
            $configIntegration = $this->configIntegrationsHelper->getIntegration($integration);
        } catch (IntegrationNotFoundException $exception) {
            return ucfirst($object);
        }

        if (!$configIntegration instanceof ConfigFormSyncInterface) {
            return ucfirst($object);
        }

        $objects = $configIntegration->getSyncConfigObjects();

        if (!isset($objects[$object])) {
            return ucfirst($object);
        }

        return $objects[$object];
    }
}
