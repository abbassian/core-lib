<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Sync\Helper;

use Autoborna\IntegrationsBundle\Entity\ObjectMapping;
use Autoborna\IntegrationsBundle\Entity\ObjectMappingRepository;
use Autoborna\IntegrationsBundle\Event\InternalObjectFindEvent;
use Autoborna\IntegrationsBundle\IntegrationEvents;
use Autoborna\IntegrationsBundle\Sync\DAO\Mapping\MappingManualDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Mapping\RemappedObjectDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Mapping\UpdatedObjectMappingDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Sync\Order\ObjectChangeDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Sync\Report\ObjectDAO;
use Autoborna\IntegrationsBundle\Sync\Exception\FieldNotFoundException;
use Autoborna\IntegrationsBundle\Sync\Exception\ObjectDeletedException;
use Autoborna\IntegrationsBundle\Sync\Exception\ObjectNotFoundException;
use Autoborna\IntegrationsBundle\Sync\Exception\ObjectNotSupportedException;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Internal\ObjectProvider;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\AutobornaSyncDataExchange;
use Autoborna\LeadBundle\Model\FieldModel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MappingHelper
{
    /**
     * @var FieldModel
     */
    private $fieldModel;

    /**
     * @var ObjectMappingRepository
     */
    private $objectMappingRepository;

    /**
     * @var ObjectProvider
     */
    private $objectProvider;

    /**
     * @var ObjectMappingRepository
     */
    private $dispatcher;

    public function __construct(
        FieldModel $fieldModel,
        ObjectMappingRepository $objectMappingRepository,
        ObjectProvider $objectProvider,
        EventDispatcherInterface $dispatcher
    ) {
        $this->fieldModel              = $fieldModel;
        $this->objectMappingRepository = $objectMappingRepository;
        $this->objectProvider          = $objectProvider;
        $this->dispatcher              = $dispatcher;
    }

    /**
     * @return ObjectDAO
     *
     * @throws ObjectDeletedException
     * @throws ObjectNotFoundException
     * @throws ObjectNotSupportedException
     */
    public function findAutobornaObject(MappingManualDAO $mappingManualDAO, string $internalObjectName, ObjectDAO $integrationObjectDAO)
    {
        // Check if this contact is already tracked
        if ($internalObject = $this->objectMappingRepository->getInternalObject(
            $mappingManualDAO->getIntegration(),
            $integrationObjectDAO->getObject(),
            $integrationObjectDAO->getObjectId(),
            $internalObjectName
        )) {
            if ($internalObject['is_deleted']) {
                throw new ObjectDeletedException();
            }

            return new ObjectDAO(
                $internalObjectName,
                $internalObject['internal_object_id'],
                new \DateTime($internalObject['last_sync_date'], new \DateTimeZone('UTC'))
            );
        }

        // We don't know who this is so search Autoborna
        $uniqueIdentifierFields = $this->fieldModel->getUniqueIdentifierFields(['object' => $internalObjectName]);
        $identifiers            = [];

        foreach ($uniqueIdentifierFields as $field => $fieldLabel) {
            try {
                $integrationField = $mappingManualDAO->getIntegrationMappedField($integrationObjectDAO->getObject(), $internalObjectName, $field);
                if ($integrationValue = $integrationObjectDAO->getField($integrationField)) {
                    $identifiers[$field] = $integrationValue->getValue()->getNormalizedValue();
                }
            } catch (FieldNotFoundException $e) {
            }
        }

        if (empty($identifiers)) {
            // No fields found to search for contact so return null
            return new ObjectDAO($internalObjectName, null);
        }

        try {
            $event = new InternalObjectFindEvent(
                $this->objectProvider->getObjectByName($internalObjectName)
            );
        } catch (ObjectNotFoundException $e) {
            // Throw this exception for BC.
            throw new ObjectNotSupportedException(AutobornaSyncDataExchange::NAME, $internalObjectName);
        }

        $event->setFieldValues($identifiers);

        $this->dispatcher->dispatch(
            IntegrationEvents::INTEGRATION_FIND_INTERNAL_RECORDS,
            $event
        );

        $foundObjects = $event->getFoundObjects();

        if (!$foundObjects) {
            // No contacts were found
            return new ObjectDAO($internalObjectName, null);
        }

        // Match found!
        $objectId = $foundObjects[0]['id'];

        // Let's store the relationship since we know it
        $objectMapping = new ObjectMapping();
        $objectMapping->setLastSyncDate($integrationObjectDAO->getChangeDateTime())
            ->setIntegration($mappingManualDAO->getIntegration())
            ->setIntegrationObjectName($integrationObjectDAO->getObject())
            ->setIntegrationObjectId($integrationObjectDAO->getObjectId())
            ->setInternalObjectName($internalObjectName)
            ->setInternalObjectId($objectId);
        $this->saveObjectMapping($objectMapping);

        return new ObjectDAO($internalObjectName, $objectId);
    }

    /**
     * Returns corresponding Autoborna entity class name for the given Autoborna object.
     *
     * @throws ObjectNotSupportedException
     */
    public function getAutobornaEntityClassName(string $internalObject): string
    {
        try {
            return $this->objectProvider->getObjectByName($internalObject)->getEntityName();
        } catch (ObjectNotFoundException $e) {
            // Throw this exception instead to keep BC.
            throw new ObjectNotSupportedException(AutobornaSyncDataExchange::NAME, $internalObject);
        }
    }

    /**
     * @return ObjectDAO
     *
     * @throws ObjectDeletedException
     */
    public function findIntegrationObject(string $integration, string $integrationObjectName, ObjectDAO $internalObjectDAO)
    {
        if ($integrationObject = $this->objectMappingRepository->getIntegrationObject(
            $integration,
            $internalObjectDAO->getObject(),
            $internalObjectDAO->getObjectId(),
            $integrationObjectName
        )) {
            if ($integrationObject['is_deleted']) {
                throw new ObjectDeletedException();
            }

            return new ObjectDAO(
                $integrationObjectName,
                $integrationObject['integration_object_id'],
                new \DateTime($integrationObject['last_sync_date'], new \DateTimeZone('UTC'))
            );
        }

        return new ObjectDAO($integrationObjectName, null);
    }

    /**
     * @param ObjectMapping[] $mappings
     */
    public function saveObjectMappings(array $mappings): void
    {
        foreach ($mappings as $mapping) {
            $this->saveObjectMapping($mapping);
        }
    }

    public function updateObjectMappings(array $mappings): void
    {
        foreach ($mappings as $mapping) {
            try {
                $this->updateObjectMapping($mapping);
            } catch (ObjectNotFoundException $exception) {
                continue;
            }
        }
    }

    /**
     * @param RemappedObjectDAO[] $mappings
     */
    public function remapIntegrationObjects(array $mappings): void
    {
        foreach ($mappings as $mapping) {
            $this->objectMappingRepository->updateIntegrationObject(
                $mapping->getIntegration(),
                $mapping->getOldObjectName(),
                $mapping->getOldObjectId(),
                $mapping->getNewObjectName(),
                $mapping->getNewObjectId()
            );
        }
    }

    /**
     * @param ObjectChangeDAO[] $objects
     */
    public function markAsDeleted(array $objects): void
    {
        foreach ($objects as $object) {
            $this->objectMappingRepository->markAsDeleted($object->getIntegration(), $object->getObject(), $object->getObjectId());
        }
    }

    private function saveObjectMapping(ObjectMapping $objectMapping): void
    {
        $this->objectMappingRepository->saveEntity($objectMapping);
        $this->objectMappingRepository->clear();
    }

    /**
     * @throws ObjectNotFoundException
     */
    private function updateObjectMapping(UpdatedObjectMappingDAO $updatedObjectMappingDAO): void
    {
        /** @var ObjectMapping $objectMapping */
        $objectMapping = $this->objectMappingRepository->findOneBy(
            [
                'integration'           => $updatedObjectMappingDAO->getIntegration(),
                'integrationObjectName' => $updatedObjectMappingDAO->getIntegrationObjectName(),
                'integrationObjectId'   => $updatedObjectMappingDAO->getIntegrationObjectId(),
            ]
        );

        if (!$objectMapping) {
            throw new ObjectNotFoundException($updatedObjectMappingDAO->getIntegrationObjectName().':'.$updatedObjectMappingDAO->getIntegrationObjectId());
        }

        $objectMapping->setLastSyncDate($updatedObjectMappingDAO->getObjectModifiedDate());

        $this->saveObjectMapping($objectMapping);
    }
}
