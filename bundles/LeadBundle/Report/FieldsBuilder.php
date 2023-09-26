<?php

namespace Autoborna\LeadBundle\Report;

use Autoborna\LeadBundle\Entity\LeadField;
use Autoborna\LeadBundle\Model\FieldModel;
use Autoborna\LeadBundle\Model\ListModel;
use Autoborna\UserBundle\Model\UserModel;

class FieldsBuilder
{
    /**
     * @var FieldModel
     */
    private $fieldModel;

    /**
     * @var ListModel
     */
    private $listModel;

    /**
     * @var UserModel
     */
    private $userModel;

    public function __construct(FieldModel $fieldModel, ListModel $listModel, UserModel $userModel)
    {
        $this->fieldModel = $fieldModel;
        $this->listModel  = $listModel;
        $this->userModel  = $userModel;
    }

    /**
     * @param string $prefix
     *
     * @return array
     */
    public function getLeadFieldsColumns($prefix)
    {
        $baseColumns  = $this->getBaseLeadColumns();
        $leadFields   = $this->fieldModel->getLeadFields();
        $fieldColumns = $this->getFieldColumns($leadFields, $prefix);

        return array_merge($baseColumns, $fieldColumns);
    }

    /**
     * @param string $prefix
     * @param string $segmentPrefix
     *
     * @return array
     */
    public function getLeadFilter($prefix, $segmentPrefix)
    {
        $filters = $this->getLeadFieldsColumns($prefix);

        $segmentPrefix = $this->sanitizePrefix($segmentPrefix);
        $prefix        = $this->sanitizePrefix($prefix);

        // Append segment filters
        $userSegments = $this->listModel->getUserLists();

        $list = [];
        foreach ($userSegments as $segment) {
            $list[$segment['id']] = $segment['name'];
        }

        $segmentKey           = $segmentPrefix.'leadlist_id';
        $filters[$segmentKey] = [
            'alias'     => 'segment_id',
            'label'     => 'autoborna.core.filter.lists',
            'type'      => 'select',
            'list'      => $list,
            'operators' => [
                'eq' => 'autoborna.core.operator.equals',
            ],
        ];

        $ownerPrefix           = $prefix.'owner_id';
        $ownersList            = [];
        $owners                = $this->userModel->getUserList('', 0);
        foreach ($owners as $owner) {
            $ownersList[$owner['id']] = sprintf('%s %s', $owner['firstName'], $owner['lastName']);
        }
        $filters[$ownerPrefix] = [
            'label' => 'autoborna.lead.list.filter.owner',
            'type'  => 'select',
            'list'  => $ownersList,
        ];

        return $filters;
    }

    /**
     * @param string $prefix
     *
     * @return array
     */
    public function getCompanyFieldsColumns($prefix)
    {
        $baseColumns   = $this->getBaseCompanyColumns();
        $companyFields = $this->fieldModel->getCompanyFields();
        $fieldColumns  = $this->getFieldColumns($companyFields, $prefix);

        return array_merge($baseColumns, $fieldColumns);
    }

    /**
     * @return array
     */
    private function getBaseLeadColumns()
    {
        return [
            'l.id' => [
                'label' => 'autoborna.lead.report.contact_id',
                'type'  => 'int',
                'link'  => 'autoborna_contact_action',
            ],
            'i.ip_address' => [
                'label' => 'autoborna.core.ipaddress',
                'type'  => 'text',
            ],
            'l.date_identified' => [
                'label'          => 'autoborna.lead.report.date_identified',
                'type'           => 'datetime',
                'groupByFormula' => 'DATE(l.date_identified)',
            ],
            'l.points' => [
                'label' => 'autoborna.lead.points',
                'type'  => 'int',
            ],
            'l.owner_id' => [
                'label' => 'autoborna.lead.report.owner_id',
                'type'  => 'int',
                'link'  => 'autoborna_user_action',
            ],
            'u.first_name' => [
                'label' => 'autoborna.lead.report.owner_firstname',
                'type'  => 'string',
            ],
            'u.last_name' => [
                'label' => 'autoborna.lead.report.owner_lastname',
                'type'  => 'string',
            ],
        ];
    }

    /**
     * @return array
     */
    private function getBaseCompanyColumns()
    {
        return [
            'comp.id' => [
                'label' => 'autoborna.lead.report.company.company_id',
                'type'  => 'int',
                'link'  => 'autoborna_company_action',
            ],
            'comp.companyname' => [
                'label' => 'autoborna.lead.report.company.company_name',
                'type'  => 'string',
                'link'  => 'autoborna_company_action',
            ],
            'comp.companycity' => [
                'label' => 'autoborna.lead.report.company.company_city',
                'type'  => 'string',
                'link'  => 'autoborna_company_action',
            ],
            'comp.companystate' => [
                'label' => 'autoborna.lead.report.company.company_state',
                'type'  => 'string',
                'link'  => 'autoborna_company_action',
            ],
            'comp.companycountry' => [
                'label' => 'autoborna.lead.report.company.company_country',
                'type'  => 'string',
                'link'  => 'autoborna_company_action',
            ],
            'comp.companyindustry' => [
                'label' => 'autoborna.lead.report.company.company_industry',
                'type'  => 'string',
                'link'  => 'autoborna_company_action',
            ],
        ];
    }

    /**
     * @param LeadField[] $fields
     * @param string      $prefix
     *
     * @return array
     */
    private function getFieldColumns($fields, $prefix)
    {
        $prefix = $this->sanitizePrefix($prefix);

        $columns = [];
        foreach ($fields as $field) {
            switch ($field->getType()) {
                case 'boolean':
                    $type = 'bool';
                    break;
                case 'date':
                    $type = 'date';
                    break;
                case 'datetime':
                    $type = 'datetime';
                    break;
                case 'time':
                    $type = 'time';
                    break;
                case 'url':
                    $type = 'url';
                    break;
                case 'email':
                    $type = 'email';
                    break;
                case 'number':
                    $type = 'float';
                    break;
                default:
                    $type = 'string';
                    break;
            }
            $columns[$prefix.$field->getAlias()] = [
                'label' => $field->getLabel(),
                'type'  => $type,
            ];
        }

        return $columns;
    }

    /**
     * @param string $prefix
     *
     * @return string
     */
    private function sanitizePrefix($prefix)
    {
        if (false === strpos($prefix, '.')) {
            $prefix .= '.';
        }

        return $prefix;
    }
}
