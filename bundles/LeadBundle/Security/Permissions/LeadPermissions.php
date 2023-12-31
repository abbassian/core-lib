<?php

namespace Autoborna\LeadBundle\Security\Permissions;

use Autoborna\CoreBundle\Security\Permissions\AbstractPermissions;
use Autoborna\UserBundle\Form\Type\PermissionListType;
use Symfony\Component\Form\FormBuilderInterface;

class LeadPermissions extends AbstractPermissions
{
    public function __construct($params)
    {
        parent::__construct($params);

        $this->permissions = [
            'lists' => [
                'viewother'   => 2,
                'editother'   => 8,
                'deleteother' => 64,
                'full'        => 1024,
            ],
            'fields' => [
                'full' => 1024,
                'view' => 1,
            ],
        ];
        $this->addExtendedPermissions('leads', false);
        $this->addStandardPermissions('imports');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'lead';
    }

    public function buildForm(FormBuilderInterface &$builder, array $options, array $data)
    {
        $this->addExtendedFormFields('lead', 'leads', $builder, $data, false);

        $builder->add(
            'lead:lists',
            PermissionListType::class,
            [
                'choices' => [
                    'autoborna.core.permissions.viewother'   => 'viewother',
                    'autoborna.core.permissions.editother'   => 'editother',
                    'autoborna.core.permissions.deleteother' => 'deleteother',
                    'autoborna.core.permissions.full'        => 'full',
                ],
                'label'             => 'autoborna.lead.permissions.lists',
                'data'              => (!empty($data['lists']) ? $data['lists'] : []),
                'bundle'            => 'lead',
                'level'             => 'lists',
            ]
        );

        $builder->add(
            'lead:fields',
            PermissionListType::class,
            [
                'choices' => [
                    'autoborna.core.permissions.manage' => 'full',
                    'autoborna.core.permissions.view'   => 'view',
                ],
                'label'             => 'autoborna.lead.permissions.fields',
                'data'              => (!empty($data['fields']) ? $data['fields'] : []),
                'bundle'            => 'lead',
                'level'             => 'fields',
            ]
        );

        $this->addStandardFormFields($this->getName(), 'imports', $builder, $data);
    }

    public function analyzePermissions(array &$permissions, $allPermissions, $isSecondRound = false)
    {
        parent::analyzePermissions($permissions, $allPermissions, $isSecondRound);

        //make sure the user has access to own leads as well if they have access to lists, notes or fields
        $viewPerms = ['viewown', 'viewother', 'full'];
        if (
            (!isset($permissions['leads']) || (array_intersect($viewPerms, $permissions['leads']) == $viewPerms)) &&
            (isset($permissions['lists']) || isset($permissions['fields']))
        ) {
            $permissions['leads'][] = 'viewown';
        }

        return false;
    }

    /**
     * @return array
     */
    protected function getSynonym($name, $level)
    {
        if ('fields' === $name) {
            //set some synonyms
            switch ($level) {
                case 'publishown':
                case 'publishother':
                    $level = 'full';
                    break;
            }
        }

        if ('lists' === $name) {
            switch ($level) {
                case 'view':
                case 'viewown':
                    $name = 'leads';
                    break;
            }
        }

        return parent::getSynonym($name, $level);
    }
}
