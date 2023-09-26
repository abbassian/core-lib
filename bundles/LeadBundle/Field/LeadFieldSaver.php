<?php

declare(strict_types=1);

namespace Autoborna\LeadBundle\Field;

use Autoborna\LeadBundle\Entity\LeadField;
use Autoborna\LeadBundle\Entity\LeadFieldRepository;
use Autoborna\LeadBundle\Exception\NoListenerException;
use Autoborna\LeadBundle\Field\Dispatcher\FieldSaveDispatcher;

class LeadFieldSaver
{
    /**
     * @var LeadFieldRepository
     */
    private $leadFieldRepository;

    /**
     * @var FieldSaveDispatcher
     */
    private $fieldSaveDispatcher;

    public function __construct(LeadFieldRepository $leadFieldRepository, FieldSaveDispatcher $fieldSaveDispatcher)
    {
        $this->leadFieldRepository = $leadFieldRepository;
        $this->fieldSaveDispatcher = $fieldSaveDispatcher;
    }

    public function saveLeadFieldEntity(LeadField $leadField, bool $isNew): void
    {
        try {
            $this->fieldSaveDispatcher->dispatchPreSaveEvent($leadField, $isNew);
        } catch (NoListenerException $e) {
        }

        $this->leadFieldRepository->saveEntity($leadField);

        try {
            $this->fieldSaveDispatcher->dispatchPostSaveEvent($leadField, $isNew);
        } catch (NoListenerException $e) {
        }
    }

    public function saveLeadFieldEntityWithoutColumnCreated(LeadField $leadField): void
    {
        $leadField->setColumnIsNotCreated();

        $this->saveLeadFieldEntity($leadField, true);
    }
}
