<?php

namespace Autoborna\UserBundle\Security\Permissions;

use Autoborna\CoreBundle\Security\Permissions\AbstractPermissions;
use Autoborna\UserBundle\Form\Type\PermissionListType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class UserPermissions.
 */
class UserPermissions extends AbstractPermissions
{
    /**
     * {@inheritdoc}
     */
    public function __construct($params)
    {
        parent::__construct($params);
        $this->permissions = [
            'profile' => [
                'editusername' => 1,
                'editemail'    => 2,
                'editposition' => 4,
                'editname'     => 8,
                'full'         => 1024,
            ],
        ];
        $this->addStandardPermissions('users', false);
        $this->addStandardPermissions('roles', false);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface &$builder, array $options, array $data)
    {
        $this->addStandardFormFields('user', 'users', $builder, $data, false);
        $this->addStandardFormFields('user', 'roles', $builder, $data, false);

        $builder->add(
            'user:profile',
            PermissionListType::class,
            [
                'choices'           => [
                    'autoborna.user.account.permissions.editname'     => 'editname',
                    'autoborna.user.account.permissions.editusername' => 'editusername',
                    'autoborna.user.account.permissions.editemail'    => 'editemail',
                    'autoborna.user.account.permissions.editposition' => 'editposition',
                    'autoborna.user.account.permissions.editall'      => 'full',
                ],
                'label'  => 'autoborna.user.permissions.profile',
                'data'   => (!empty($data['profile']) ? $data['profile'] : []),
                'bundle' => 'user',
                'level'  => 'profile',
            ]
        );
    }
}
