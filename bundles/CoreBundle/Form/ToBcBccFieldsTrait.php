<?php

namespace Autoborna\CoreBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;

trait ToBcBccFieldsTrait
{
    protected function addToBcBccFields(FormBuilderInterface $builder)
    {
        $builder->add(
            'to',
            TextType::class,
            [
                'label'      => 'autoborna.core.send.email.to',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                    'placeholder' => 'autoborna.core.optional',
                    'tooltip'     => 'autoborna.core.send.email.to.multiple.addresses',
                ],
                'required'    => false,
                'constraints' => new Email(
                    [
                        'message' => 'autoborna.core.email.required',
                    ]
                ),
            ]
        );

        $builder->add(
            'cc',
            TextType::class,
            [
                'label'      => 'autoborna.core.send.email.cc',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                    'placeholder' => 'autoborna.core.optional',
                    'tooltip'     => 'autoborna.core.send.email.to.multiple.addresses',
                ],
                'required'    => false,
                'constraints' => new Email(
                    [
                        'message' => 'autoborna.core.email.required',
                    ]
                ),
            ]
        );

        $builder->add(
            'bcc',
            TextType::class,
            [
                'label'      => 'autoborna.core.send.email.bcc',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                    'placeholder' => 'autoborna.core.optional',
                    'tooltip'     => 'autoborna.core.send.email.to.multiple.addresses',
                ],
                'required'    => false,
                'constraints' => new Email(
                    [
                        'message' => 'autoborna.core.email.required',
                    ]
                ),
            ]
        );
    }
}
