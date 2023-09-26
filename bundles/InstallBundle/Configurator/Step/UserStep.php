<?php

namespace Autoborna\InstallBundle\Configurator\Step;

use Autoborna\CoreBundle\Configurator\Step\StepInterface;
use Autoborna\InstallBundle\Configurator\Form\UserStepType;

class UserStep implements StepInterface
{
    /**
     * User's first name.
     */
    public $firstname;

    /**
     * User's last name.
     */
    public $lastname;

    /**
     * User's e-mail address.
     */
    public $email;

    /**
     * User's username.
     */
    public $username;

    /**
     * User's password.
     */
    public $password;

    /**
     * {@inheritdoc}
     */
    public function getFormType()
    {
        return UserStepType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function checkRequirements()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function checkOptionalSettings()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate()
    {
        return 'AutobornaInstallBundle:Install:user.html.php';
    }

    /**
     * {@inheritdoc}
     */
    public function update(StepInterface $data)
    {
        return [];
    }
}
