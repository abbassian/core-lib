<?php

namespace Autoborna\DynamicContentBundle\Security\Permissions;

use Autoborna\CoreBundle\Security\Permissions\AbstractPermissions;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class NotificationPermissions.
 */
class DynamicContentPermissions extends AbstractPermissions
{
    /**
     * {@inheritdoc}
     */
    public function __construct($params)
    {
        parent::__construct($params);

        $this->addStandardPermissions('categories');
        $this->addExtendedPermissions('dynamiccontents');
    }

    /**
     * {@inheritdoc}
     *
     * @return string|void
     */
    public function getName()
    {
        return 'dynamiccontent';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface &$builder, array $options, array $data)
    {
        $this->addStandardFormFields('dynamiccontent', 'categories', $builder, $data);
        $this->addExtendedFormFields('dynamiccontent', 'dynamiccontents', $builder, $data);
    }
}
