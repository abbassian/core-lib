<?php

declare(strict_types=1);

namespace Autoborna\LeadBundle\EventListener;

use Autoborna\CoreBundle\Helper\ArrayHelper;
use Autoborna\CoreBundle\Security\Permissions\CorePermissions;
use Autoborna\LeadBundle\Entity\Tag;
use Autoborna\LeadBundle\Event\ImportInitEvent;
use Autoborna\LeadBundle\Event\ImportMappingEvent;
use Autoborna\LeadBundle\Event\ImportProcessEvent;
use Autoborna\LeadBundle\Event\ImportValidateEvent;
use Autoborna\LeadBundle\Field\FieldList;
use Autoborna\LeadBundle\LeadEvents;
use Autoborna\LeadBundle\Model\CompanyModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Translation\TranslatorInterface;

final class ImportCompanySubscriber implements EventSubscriberInterface
{
    private FieldList $fieldList;
    private CorePermissions $corePermissions;
    private CompanyModel $companyModel;
    private TranslatorInterface $translator;

    public function __construct(
        FieldList $fieldList,
        CorePermissions $corePermissions,
        CompanyModel $companyModel,
        TranslatorInterface $translator
    ) {
        $this->fieldList       = $fieldList;
        $this->corePermissions = $corePermissions;
        $this->companyModel    = $companyModel;
        $this->translator      = $translator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LeadEvents::IMPORT_ON_INITIALIZE    => ['onImportInit'],
            LeadEvents::IMPORT_ON_FIELD_MAPPING => ['onFieldMapping'],
            LeadEvents::IMPORT_ON_PROCESS       => ['onImportProcess'],
            LeadEvents::IMPORT_ON_VALIDATE      => ['onValidateImport'],
        ];
    }

    /**
     * @throws AccessDeniedException
     */
    public function onImportInit(ImportInitEvent $event): void
    {
        if ($event->importIsForRouteObject('companies')) {
            if (!$this->corePermissions->isGranted('lead:imports:create')) {
                throw new AccessDeniedException('You do not have permission to import companies');
            }

            $event->objectSingular = 'company';
            $event->objectName     = 'autoborna.lead.lead.companies';
            $event->activeLink     = '#autoborna_company_index';
            $event->setIndexRoute('autoborna_company_index');
            $event->stopPropagation();
        }
    }

    public function onFieldMapping(ImportMappingEvent $event): void
    {
        if ($event->importIsForRouteObject('companies')) {
            $specialFields = [
                'dateAdded'      => 'autoborna.lead.import.label.dateAdded',
                'createdByUser'  => 'autoborna.lead.import.label.createdByUser',
                'dateModified'   => 'autoborna.lead.import.label.dateModified',
                'modifiedByUser' => 'autoborna.lead.import.label.modifiedByUser',
            ];

            $event->fields = [
                'autoborna.lead.company'        => $this->fieldList->getFieldList(false, false, ['isPublished' => true, 'object' => 'company']),
                'autoborna.lead.special_fields' => $specialFields,
            ];
        }
    }

    public function onImportProcess(ImportProcessEvent $event): void
    {
        if ($event->importIsForObject('company')) {
            $merged = $this->companyModel->import(
                $event->import->getMatchedFields(),
                $event->rowData,
                $event->import->getDefault('owner'),
                $event->import->getDefault('skip_if_exists')
            );
            $event->setWasMerged((bool) $merged);
            $event->stopPropagation();
        }
    }

    public function onValidateImport(ImportValidateEvent $event): void
    {
        if (false === $event->importIsForRouteObject('companies')) {
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
        $tags = is_array($tags) ? $tags : [];

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
            'object'      => 'company',
            'isRequired'  => true,
        ]);

        $missingRequiredFields = array_diff_key($requiredFields, array_flip($matchedFields));

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
