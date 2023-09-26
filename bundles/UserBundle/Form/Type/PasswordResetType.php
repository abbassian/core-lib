<?php

namespace Autoborna\UserBundle\Form\Type;

use Autoborna\CoreBundle\Form\EventListener\CleanFormSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class PasswordResetType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new CleanFormSubscriber([]));

        $builder->add(
            'identifier',
            TextType::class,
            [
                'label'      => 'autoborna.user.auth.form.loginusername',
                'label_attr' => ['class' => 'sr-only'],
                'attr'       => [
                    'class'       => 'form-control',
                    'preaddon'    => 'fa fa-user',
                    'placeholder' => 'autoborna.user.auth.form.loginusername',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'autoborna.user.user.passwordreset.notblank']),
                ],
            ]
        );

        $builder->add(
            'submit',
            SubmitType::class,
            [
                'attr' => [
                    'class' => 'btn btn-lg btn-primary btn-block',
                ],
                'label' => 'autoborna.user.user.passwordreset.reset',
            ]
        );

        if (!empty($options['action'])) {
            $builder->setAction($options['action']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'passwordreset';
    }
}
