<?php

namespace Autoborna\FormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class FormFieldCaptchaType.
 */
class FormFieldCaptchaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'captcha',
            TextType::class,
            [
                'label'      => 'autoborna.form.field.form.property_captcha',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                    'tooltip'     => 'autoborna.form.field.help.captcha',
                    'placeholder' => 'autoborna.form.field.help.captcha_placeholder',
                ],
                'required' => false,
            ]
        );

        $builder->add(
            'placeholder',
            TextType::class,
            [
                'label'      => 'autoborna.form.field.form.property_placeholder',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => ['class' => 'form-control'],
                'required'   => false,
            ]
        );

        $builder->add(
            'errorMessage',
            TextType::class,
            [
                'label'      => 'autoborna.form.field.form.property_captchaerror',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => ['class' => 'form-control'],
                'required'   => false,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'formfield_captcha';
    }
}
