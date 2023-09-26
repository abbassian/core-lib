<?php

declare(strict_types=1);

namespace Autoborna\LeadBundle\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use Autoborna\CoreBundle\Helper\ArrayHelper;
use Autoborna\CoreBundle\Security\Permissions\CorePermissions;
use Autoborna\LeadBundle\Entity\Tag;
use Autoborna\LeadBundle\Event\ImportInitEvent;
use Autoborna\LeadBundle\Event\ImportMappingEvent;
use Autoborna\LeadBundle\Event\ImportProcessEvent;
use Autoborna\LeadBundle\Event\ImportValidateEvent;
use Autoborna\LeadBundle\Field\FieldList;
use Autoborna\LeadBundle\LeadEvents;
use Autoborna\LeadBundle\Model\LeadModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Translation\TranslatorInterface;

final class ImportContactSubscriber implements EventSubscriberInterface
{
    private FieldList $fieldList;
    private CorePermissions $corePermissions;
    private LeadModel $contactModel;
    private TranslatorInterface $translator;

    public function __construct(
        FieldList $fieldList,
        CorePermissions $corePermissions,
        LeadModel $contactModel,
        TranslatorInterface $translator
    ) {
        $this->fieldList       = $fieldList;
        $this->corePermissions = $corePermissions;
        $this->contactModel    = $contactModel;
        $this->translator      = $translator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LeadEvents::IMPORT_ON_INITIALIZE    => 'onImportInit',
            LeadEvents::IMPORT_ON_FIELD_MAPPING => 'onFieldMapping',
            LeadEvents::IMPORT_ON_PROCESS       => 'onImportProcess',
            LeadEvents::IMPORT_ON_VALIDATE      => 'onValidateImport',
        ];
    }

    /**
     * @throws AccessDeniedException
     */
    public function onImportInit(ImportInitEvent $event): void
    {
        if ($event->importIsForRouteObject('contacts')) {
            if (!$this->corePermissions->isGranted('lead:imports:create')) {
                throw new AccessDeniedException('You do not have permission to import contacts');
            }

            $event->objectSingular = 'lead';
            $event->objectName     = 'autoborna.lead.leads';
            $event->activeLink     = '#autoborna_contact_index';
            $event->setIndexRoute('autoborna_contact_index');
            $event->stopPropagation();
        }
    }

    public function onFieldMapping(ImportMappingEvent $event): void
    {
        if ($event->importIsForRouteObject('contacts')) {
            $specialFields = [
                'dateAdded'      => 'autoborna.lead.import.label.dateAdded',
                'createdByUser'  => 'autoborna.lead.import.label.createdByUser',
                'dateModified'   => 'autoborna.lead.import.label.dateModified',
                'modifiedByUser' => 'autoborna.lead.import.label.modifiedByUser',
                'lastActive'     => 'autoborna.lead.import.label.lastActive',
                'dateIdentified' => 'autoborna.lead.import.label.dateIdentified',
                'ip'             => 'autoborna.lead.import.label.ip',
                'stage'          => 'autoborna.lead.import.label.stage',
                'doNotEmail'     => 'autoborna.lead.import.label.doNotEmail',
                'ownerusername'  => 'autoborna.lead.import.label.ownerusername',
            ];

            // Add ID to lead fields to allow matching import contacts by identifier
            $contactFields = array_merge(['id' => 'autoborna.lead.import.label.id'], $this->fieldList->getFieldList(false, false));

            $event->fields = [
                'autoborna.lead.contact'        => $contactFields,
                'autoborna.lead.company'        => $this->fieldList->getFieldList(false, false, ['isPublished' => true, 'object' => 'company']),
                'autoborna.lead.special_fields' => $specialFields,
            ];
        }
    }

    public function onImportProcess(ImportProcessEvent $event): void
    {
        if ($event->importIsForObject('lead')) {
            $merged = $this->contactModel->import(
                $event->import->getMatchedFields(),
                $event->rowData,
                $event->import->getDefault('owner'),
                $event->import->getDefault('list'),
                $event->import->getDefault('tags'),
                true,
                $event->eventLog,
                (int) $event->import->getId(),
                $event->import->getDefault('skip_if_exists')
            );
            $event->setWasMerged((bool) $merged);
            $event->stopPropagation();
        }
    }

    public function onValidateImport(ImportValidateEvent $event): void
    {
        if (false === $event->importIsForRouteObject('contacts')) {
            return;
        }

        $matchedFields = $event->getForm()->getData();

        $event->setOwnerId($this->handleValidateOwner($matchedFields));
        $event->setList($this->handleValidateList($matchedFields));
        $event->setTags($this->handleValidateTags($matchedFields));

        $matchedFields = array_map(
            fn ($value) => is_string($value) ? trim($value) : $value,
            array_filter($matchedFields)
        );

        if (empty($matchedFields)) {
            $event->getForm()->addError(
                new FormError(
                    $this->translator->trans('autoborna.lead.import.matchfields', [], 'validators')
                )
            );
        }

        $this->handleValidateRequired($event, $matchedFields);

        $event->setMatchedFields($matchedFields);
    }

    /**
     * @param mixed[] $matchedFields
     */
    private function handleValidateOwner(array &$matchedFields): ?int
    {
        $owner = ArrayHelper::pickValue('owner', $matchedFields);

        return $owner ? $owner->getId() : null;
    }

    /**
     * @param mixed[] $matchedFields
     */
    private function handleValidateList(array &$matchedFields): ?int
    {
        return ArrayHelper::pickValue('list', $matchedFields);
    }

    /**
     * @param mixed[] $matchedFields
     *
     * @return mixed[]
     */
    private function handleValidateTags(array &$matchedFields): array
    {
        // In case $matchedFields['tags'] === null ...
        $tags = ArrayHelper::pickValue('tags', $matchedFields, []);
        // ...we must ensure we pass an [] to array_map
        $tags = $tags instanceof ArrayCollection ? $tags->toArray() : [];

        return array_map(fn (Tag $tag) => $tag->getTag(), $tags);
    }

    /**
     * Validate required fields.
     *
     * Required fields come through as ['alias' => 'label'], and
     * $matchedFields is a zero indexed array, so to calculate the
     * diff, we must array_flip($matchedFields) and compare on key.
     *
     * @param mixed[] $matchedFields
     */
    private function handleValidateRequired(ImportValidateEvent $event, array &$matchedFields): void
    {
        $requiredFields = $this->fieldList->getFieldList(false, false, [
            'isPublished' => true,
            'object'      => 'lead',
            'isRequired'  => true,
        ]);

        $missingRequiredFields = array_diff_key($requiredFields, array_flip($matchedFields));

        // Check for the presense of company mapped fields
        $companyFields = array_filter($matchedFields, fn ($fieldname) => is_string($fieldname) && 0 === strpos($fieldname, 'company'));

        // If we have any, ensure all required company fields are mapped.
        if (count($companyFields)) {
            $companyRequiredFields = $this->fieldList->getFieldList(false, false, [
                'isPublished' => true,
                'object'      => 'company',
                'isRequired'  => true,
            ]);

            $companyMissingRequiredFields = array_diff_key($companyRequiredFields, array_flip($matchedFields));

            if (count($companyMissingRequiredFields)) {
                $missingRequiredFields = array_merge($missingRequiredFields, $companyMissingRequiredFields);
            }
        }

        if (count($missingRequiredFields)) {
            $event->getForm()->addError(
                new FormError(
                    $this->translator->trans(
                        'autoborna.import.missing.required.fields',
                        [
                            '%requiredFields%' => implode(', ', $missingRequiredFields),
                            '%fieldOrFields%'  => 1 === count($missingRequiredFields) ? 'field' : 'fields',
                        ],
                        'validators'
                    )
                )
            );
        }
    }
}
