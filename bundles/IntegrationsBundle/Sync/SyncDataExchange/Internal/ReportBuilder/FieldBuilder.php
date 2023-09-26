<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Internal\ReportBuilder;

use Autoborna\IntegrationsBundle\Sync\DAO\Sync\Report\FieldDAO as ReportFieldDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Sync\Request\ObjectDAO as RequestObjectDAO;
use Autoborna\IntegrationsBundle\Sync\DAO\Value\NormalizedValueDAO;
use Autoborna\IntegrationsBundle\Sync\Exception\FieldNotFoundException;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Helper\FieldHelper;
use Autoborna\IntegrationsBundle\Sync\SyncDataExchange\Internal\ObjectHelper\ContactObjectHelper;
use Autoborna\IntegrationsBundle\Sync\ValueNormalizer\ValueNormalizer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Router;

class FieldBuilder
{
    /**
     * @var ValueNormalizer
     */
    private $valueNormalizer;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var FieldHelper
     */
    private $fieldHelper;

    /**
     * @var ContactObjectHelper
     */
    private $contactObjectHelper;

    /**
     * @var array
     */
    private $autobornaObject;

    /**
     * @var RequestObjectDAO
     */
    private $requestObject;

    public function __construct(Router $router, FieldHelper $fieldHelper, ContactObjectHelper $contactObjectHelper)
    {
        $this->valueNormalizer = new ValueNormalizer();

        $this->router              = $router;
        $this->fieldHelper         = $fieldHelper;
        $this->contactObjectHelper = $contactObjectHelper;
    }

    /**
     * @return ReportFieldDAO
     *
     * @throws FieldNotFoundException
     */
    public function buildObjectField(
        string $field,
        array $autobornaObject,
        RequestObjectDAO $requestObject,
        string $integration,
        string $defaultState = ReportFieldDAO::FIELD_CHANGED
    ) {
        $this->autobornaObject  = $autobornaObject;
        $this->requestObject = $requestObject;

        // Special handling of the ID field
        if ('autoborna_internal_id' === $field) {
            return $this->addContactIdField($field);
        }

        // Special handling of the owner ID field
        if ('owner_id' === $field) {
            return $this->createOwnerIdReportFieldDAO($field, (int) $autobornaObject['owner_id']);
        }

        // Special handling of DNC fields
        if (0 === strpos($field, 'autoborna_internal_dnc_')) {
            return $this->addDoNotContactField($field);
        }

        // Special handling of timeline URL
        if ('autoborna_internal_contact_timeline' === $field) {
            return $this->addContactTimelineField($integration, $field);
        }

        return $this->addCustomField($field, $defaultState);
    }

    /**
     * @return ReportFieldDAO
     */
    private function addContactIdField(string $field)
    {
        $normalizedValue = new NormalizedValueDAO(
            NormalizedValueDAO::INT_TYPE,
            $this->autobornaObject['id']
        );

        return new ReportFieldDAO($field, $normalizedValue);
    }

    /**
     * @return ReportFieldDAO
     */
    private function createOwnerIdReportFieldDAO(string $field, int $ownerId)
    {
        return new ReportFieldDAO(
            $field,
            new NormalizedValueDAO(
                NormalizedValueDAO::INT_TYPE,
                $ownerId
            )
        );
    }

    /**
     * @return ReportFieldDAO
     */
    private function addDoNotContactField(string $field)
    {
        $channel = str_replace('autoborna_internal_dnc_', '', $field);

        $normalizedValue = new NormalizedValueDAO(
            NormalizedValueDAO::INT_TYPE,
            $this->contactObjectHelper->getDoNotContactStatus((int) $this->autobornaObject['id'], $channel)
        );

        return new ReportFieldDAO($field, $normalizedValue);
    }

    /**
     * @return ReportFieldDAO
     */
    private function addContactTimelineField(string $integration, string $field)
    {
        $normalizedValue = new NormalizedValueDAO(
            NormalizedValueDAO::URL_TYPE,
            $this->router->generate(
                'autoborna_plugin_timeline_view',
                [
                    'integration' => $integration,
                    'leadId'      => $this->autobornaObject['id'],
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            )
        );

        return new ReportFieldDAO($field, $normalizedValue);
    }

    /**
     * @return ReportFieldDAO
     *
     * @throws FieldNotFoundException
     */
    private function addCustomField(string $field, string $defaultState)
    {
        // The rest should be Autoborna custom fields and if not, just ignore
        $autobornaFields = $this->fieldHelper->getFieldList($this->requestObject->getObject());
        if (!isset($autobornaFields[$field])) {
            // Field must have been deleted or something so let's skip
            throw new FieldNotFoundException($field, $this->requestObject->getObject());
        }

        $requiredFields  = $this->requestObject->getRequiredFields();
        $fieldType       = $this->fieldHelper->getNormalizedFieldType($autobornaFields[$field]['type']);
        $normalizedValue = $this->valueNormalizer->normalizeForAutoborna($fieldType, $this->autobornaObject[$field]);

        return new ReportFieldDAO(
            $field,
            $normalizedValue,
            in_array($field, $requiredFields) ? ReportFieldDAO::FIELD_REQUIRED : $defaultState
        );
    }
}
