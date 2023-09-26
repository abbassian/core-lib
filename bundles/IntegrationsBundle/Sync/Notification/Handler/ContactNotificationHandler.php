<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Sync\Notification\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Autoborna\IntegrationsBundle\Sync\DAO\Sync\Order\NotificationDAO;
use Autoborna\IntegrationsBundle\Sync\Notification\Helper\UserSummaryNotificationHelper;
use Autoborna\IntegrationsBundle\Sync\Notification\Writer;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Internal\Object\Contact;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\AutobornaSyncDataExchange;
use Autoborna\LeadBundle\Entity\Lead;
use Autoborna\LeadBundle\Entity\LeadEventLog;
use Autoborna\LeadBundle\Entity\LeadEventLogRepository;

class ContactNotificationHandler implements HandlerInterface
{
    /**
     * @var Writer
     */
    private $writer;

    /**
     * @var LeadEventLogRepository
     */
    private $leadEventRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var UserSummaryNotificationHelper
     */
    private $userNotificationHelper;

    /**
     * @var string
     */
    private $integrationDisplayName;

    /**
     * @var string
     */
    private $objectDisplayName;

    public function __construct(
        Writer $writer,
        LeadEventLogRepository $leadEventRepository,
        EntityManagerInterface $em,
        UserSummaryNotificationHelper $userNotificationHelper
    ) {
        $this->writer                 = $writer;
        $this->leadEventRepository    = $leadEventRepository;
        $this->em                     = $em;
        $this->userNotificationHelper = $userNotificationHelper;
    }

    public function getIntegration(): string
    {
        return AutobornaSyncDataExchange::NAME;
    }

    public function getSupportedObject(): string
    {
        return Contact::NAME;
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    public function writeEntry(NotificationDAO $notificationDAO, string $integrationDisplayName, string $objectDisplayName): void
    {
        $this->integrationDisplayName = $integrationDisplayName;
        $this->objectDisplayName      = $objectDisplayName;

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

        $this->writeEventLogEntry($notificationDAO->getIntegration(), $notificationDAO->getAutobornaObjectId(), $notificationDAO->getMessage());

        // Store these so we can send one notice to the user
        $this->userNotificationHelper->storeSummaryNotification($integrationDisplayName, $objectDisplayName, $notificationDAO->getAutobornaObjectId());
    }

    public function finalize(): void
    {
        $this->userNotificationHelper->writeNotifications(
            Contact::NAME,
            'autoborna.integration.sync.user_notification.contact_message'
        );
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    private function writeEventLogEntry(string $integration, int $contactId, string $message): void
    {
        $eventLog = new LeadEventLog();
        $eventLog
            ->setLead($this->em->getReference(Lead::class, $contactId))
            ->setBundle('integrations')
            ->setObject($integration)
            ->setAction('sync')
            ->setProperties(
                [
                    'message'     => $message,
                    'integration' => $this->integrationDisplayName,
                    'object'      => $this->objectDisplayName,
                ]
            );

        $this->leadEventRepository->saveEntity($eventLog);
        $this->leadEventRepository->detachEntity($eventLog);
    }
}
