<?php

namespace Autoborna\PageBundle\Security\Permissions;

use Autoborna\CoreBundle\Security\Permissions\AbstractPermissions;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class PagePermissions.
 */
class PagePermissions extends AbstractPermissions
{
    /**
     * {@inheritdoc}
     */
    public function __construct($params)
    {
        parent::__construct($params);
        $this->addExtendedPermissions('pages');
        $this->addStandardPermissions('categories');
        $this->addExtendedPermissions('preference_center');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'page';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface &$builder, array $options, array $data)
    {
        $this->addStandardFormFields('page', 'categories', $builder, $data);
        $this->addExtendedFormFields('page', 'pages', $builder, $data);
        $this->addExtendedFormFields('page', 'preference_center', $builder, $data);
    }
}
