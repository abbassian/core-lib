<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Sync\Notification\Handler;

use Autoborna\IntegrationsBundle\Sync\DAO\Sync\Order\NotificationDAO;
use Autoborna\IntegrationsBundle\Sync\Notification\Helper\CompanyHelper;
use Autoborna\IntegrationsBundle\Sync\Notification\Helper\UserNotificationHelper;
use Autoborna\IntegrationsBundle\Sync\Notification\Writer;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\AutobornaSyncDataExchange;

class CompanyNotificationHandler implements HandlerInterface
{
    /**
     * @var Writer
     */
    private $writer;

    /**
     * @var UserNotificationHelper
     */
    private $userNotificationHelper;

    /**
     * @var CompanyHelper
     */
    private $companyHelper;

    public function __construct(Writer $writer, UserNotificationHelper $userNotificationHelper, CompanyHelper $companyHelper)
    {
        $this->writer                 = $writer;
        $this->userNotificationHelper = $userNotificationHelper;
        $this->companyHelper          = $companyHelper;
    }

    public function getIntegration(): string
    {
        return AutobornaSyncDataExchange::NAME;
    }

    public function getSupportedObject(): string
    {
        return AutobornaSyncDataExchange::OBJECT_COMPANY;
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Autoborna\IntegrationsBundle\Sync\Exception\ObjectNotSupportedException
     */
    public function writeEntry(NotificationDAO $notificationDAO, string $integrationDisplayName, string $objectDisplayName): void
    {
        $this->writer->writeAuditLogEntry(
            $notificationDAO->getIntegration(),
            $notificationDAO->getAutobornaObject(),
            $notificationDAO->getAutobornaObjectId(),
            'sync',
            [
                'integrationObject'   => $notificationDAO->getIntegrationObject(),
                'integrationObjectId' => $notificationDAO->getIntegrationObjectId(),
                'message'             => $notificationDAO->getMessage(),
            ]
        );

        $this->userNotificationHelper->writeNotification(
            $notificationDAO->getMessage(),
            $integrationDisplayName,
            $objectDisplayName,
            $notificationDAO->getAutobornaObject(),
            $notificationDAO->getAutobornaObjectId(),
            (string) $this->companyHelper->getCompanyName($notificationDAO->getAutobornaObjectId())
        );
    }

    public function finalize(): void
    {
        // Nothing to do
    }
}
