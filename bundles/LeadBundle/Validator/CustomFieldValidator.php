<?php

declare(strict_types=1);

namespace Autoborna\LeadBundle\Validator;

use Autoborna\CoreBundle\Exception\InvalidValueException;
use Autoborna\CoreBundle\Exception\RecordNotFoundException;
use Autoborna\CoreBundle\Exception\RecordNotPublishedException;
use Autoborna\LeadBundle\Entity\LeadField;
use Autoborna\LeadBundle\Model\FieldModel;
use Symfony\Contracts\Translation\TranslatorInterface;

class CustomFieldValidator
{
    private FieldModel $fieldModel;

    private TranslatorInterface $translator;

    public function __construct(FieldModel $fieldModel, TranslatorInterface $translator)
    {
        $this->fieldModel = $fieldModel;
        $this->translator = $translator;
    }

    /**
     * @throws RecordNotFoundException
     * @throws RecordNotPublishedException
     * @throws InvalidValueException
     */
    public function validateFieldType(string $alias, string $fieldType): void
    {
        $field = $this->getPublishedFieldByAlias($alias);

        if ($field->getType() !== $fieldType) {
            throw new InvalidValueException($this->translator->trans('autoborna.lead.contact.wrong.field.type', ['%alias%' => $alias, '%fieldType%' => $field->getType(), '%expectedType%' => $fieldType], 'validators'));
        }
    }

    /**
     * @throws RecordNotFoundException
     * @throws RecordNotPublishedException
     */
    private function getPublishedFieldByAlias(string $alias): LeadField
    {
        $field = $this->getFieldByAlias($alias);

        if (!$field->getIsPublished()) {
            throw new RecordNotPublishedException($this->translator->trans('autoborna.lead.contact.field.not.published', ['%alias%' => $alias], 'validators'));
        }

        return $field;
    }

    /**
     * @throws RecordNotFoundException
     */
    private function getFieldByAlias(string $alias): LeadField
    {
        /** @var LeadField|null */
        $field = $this->fieldModel->getEntityByAlias($alias);

        if (!$field) {
            throw new RecordNotFoundException($this->translator->trans('autoborna.lead.contact.field.not.found', ['%alias%' => $alias], 'validators'));
        }

        return $field;
    }
}
