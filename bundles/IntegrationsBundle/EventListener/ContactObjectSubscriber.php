<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\EventListener;

use Autoborna\IntegrationsBundle\Event\InternalObjectCreateEvent;
use Autoborna\IntegrationsBundle\Event\InternalObjectEvent;
use Autoborna\IntegrationsBundle\Event\InternalObjectFindEvent;
use Autoborna\IntegrationsBundle\Event\InternalObjectOwnerEvent;
use Autoborna\IntegrationsBundle\Event\InternalObjectRouteEvent;
use Autoborna\IntegrationsBundle\Event\InternalObjectUpdateEvent;
use Autoborna\IntegrationsBundle\IntegrationEvents;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Internal\Object\Contact;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Internal\ObjectHelper\ContactObjectHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Router;

class ContactObjectSubscriber implements EventSubscriberInterface
{
    /**
     * @var ContactObjectHelper
     */
    private $contactObjectHelper;

    /**
     * @var Router
     */
    private $router;

    public function __construct(
        ContactObjectHelper $contactObjectHelper,
        Router $router
    ) {
        $this->contactObjectHelper = $contactObjectHelper;
        $this->router              = $router;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            IntegrationEvents::INTEGRATION_COLLECT_INTERNAL_OBJECTS => ['collectInternalObjects', 0],
            IntegrationEvents::INTEGRATION_UPDATE_INTERNAL_OBJECTS  => ['updateContacts', 0],
            IntegrationEvents::INTEGRATION_CREATE_INTERNAL_OBJECTS  => ['createContacts', 0],
            IntegrationEvents::INTEGRATION_FIND_INTERNAL_RECORDS    => [
                ['findContactsByIds', 0],
                ['findContactsByDateRange', 0],
                ['findContactsByFieldValues', 0],
            ],
            IntegrationEvents::INTEGRATION_FIND_OWNER_IDS              => ['findOwnerIdsForContacts', 0],
            IntegrationEvents::INTEGRATION_BUILD_INTERNAL_OBJECT_ROUTE => ['buildContactRoute', 0],
        ];
    }

    public function collectInternalObjects(InternalObjectEvent $event): void
    {
        $event->addObject(new Contact());
    }

    public function updateContacts(InternalObjectUpdateEvent $event): void
    {
        if (Contact::NAME !== $event->getObject()->getName()) {
            return;
        }

        $event->setUpdatedObjectMappings(
            $this->contactObjectHelper->update(
                $event->getIdentifiedObjectIds(),
                $event->getUpdateObjects()
            )
        );
        $event->stopPropagation();
    }

    public function createContacts(InternalObjectCreateEvent $event): void
    {
        if (Contact::NAME !== $event->getObject()->getName()) {
            return;
        }

        $event->setObjectMappings($this->contactObjectHelper->create($event->getCreateObjects()));
        $event->stopPropagation();
    }

    public function findContactsByIds(InternalObjectFindEvent $event): void
    {
        if (Contact::NAME !== $event->getObject()->getName() || empty($event->getIds())) {
            return;
        }

        $event->setFoundObjects($this->contactObjectHelper->findObjectsByIds($event->getIds()));
        $event->stopPropagation();
    }

    public function findContactsByDateRange(InternalObjectFindEvent $event): void
    {
        if (Contact::NAME !== $event->getObject()->getName() || empty($event->getDateRange())) {
            return;
        }

        $event->setFoundObjects(
            $this->contactObjectHelper->findObjectsBetweenDates(
                $event->getDateRange()->getFromDate(),
                $event->getDateRange()->getToDate(),
                $event->getStart(),
                $event->getLimit()
            )
        );
        $event->stopPropagation();
    }

    public function findContactsByFieldValues(InternalObjectFindEvent $event): void
    {
        if (Contact::NAME !== $event->getObject()->getName() || empty($event->getFieldValues())) {
            return;
        }

        $event->setFoundObjects(
            $this->contactObjectHelper->findObjectsByFieldValues(
                $event->getFieldValues()
            )
        );
        $event->stopPropagation();
    }

    public function findOwnerIdsForContacts(InternalObjectOwnerEvent $event): void
    {
        if (Contact::NAME !== $event->getObject()->getName()) {
            return;
        }

        $event->setOwners(
            $this->contactObjectHelper->findOwnerIds(
                $event->getObjectIds()
            )
        );
        $event->stopPropagation();
    }

    public function buildContactRoute(InternalObjectRouteEvent $event): void
    {
        if (Contact::NAME !== $event->getObject()->getName()) {
            return;
        }

        $event->setRoute(
            $this->router->generate(
                'autoborna_contact_action',
                [
                    'objectAction' => 'view',
                    'objectId'     => $event->getId(),
                ]
            )
        );
        $event->stopPropagation();
    }
}
