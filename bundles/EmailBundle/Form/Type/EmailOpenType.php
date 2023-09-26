<?php

namespace Autoborna\EmailBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmailOpenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $defaultOptions = [
            'label'      => 'autoborna.email.open.limittoemails',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => [
                'class'   => 'form-control',
                'tooltip' => 'autoborna.email.open.limittoemails_descr',
            ],
            'required'   => false,
            'email_type' => null,
        ];

        if (isset($options['list_options'])) {
            if (isset($options['list_options']['attr'])) {
                $defaultOptions['attr'] = array_merge($defaultOptions['attr'], $options['list_options']['attr']);
                unset($options['list_options']['attr']);
            }

            $defaultOptions = array_merge($defaultOptions, $options['list_options']);
        }

        $builder->add('emails', EmailListType::class, $defaultOptions);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined(['list_options']);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'emailopen_list';
    }
}
