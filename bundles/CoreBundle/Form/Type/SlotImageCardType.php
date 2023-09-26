<?php

namespace Autoborna\CoreBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class SlotImageCardType.
 */
class SlotImageCardType extends SlotType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'caption',
            TextType::class,
            [
                'label'      => 'autoborna.core.image.caption',
                'label_attr' => ['class' => 'control-label'],
                'required'   => false,
                'attr'       => [
                    'class'           => 'form-control',
                    'data-slot-param' => 'cardcaption',
                ],
            ]
        );

        $builder->add(
            'align',
            ButtonGroupType::class,
            [
                'label'             => 'autoborna.core.image.position',
                'label_attr'        => ['class' => 'control-label'],
                'required'          => false,
                'attr'              => [
                    'class'           => 'form-control',
                    'data-slot-param' => 'align',
                ],
                'choices'           => [
                    'autoborna.core.left'   => 0,
                    'autoborna.core.center' => 1,
                    'autoborna.core.right'  => 2,
                ],
                ]
        );

        $builder->add(
            'text-align',
            ButtonGroupType::class,
            [
                'label'             => 'autoborna.core.caption.position',
                'label_attr'        => ['class' => 'control-label'],
                'required'          => false,
                'attr'              => [
                    'class'           => 'form-control',
                    'data-slot-param' => 'text-align',
                ],
                'choices'           => [
                    'autoborna.core.left'   => 0,
                    'autoborna.core.center' => 1,
                    'autoborna.core.right'  => 2,
                ],
                ]
        );

        $builder->add(
            'background-color',
            TextType::class,
            [
                'label'      => 'autoborna.core.background.color',
                'label_attr' => ['class' => 'control-label'],
                'required'   => false,
                'attr'       => [
                    'class'           => 'form-control',
                    'data-slot-param' => 'background-color',
                    'data-toggle'     => 'color',
                ],
            ]
        );

        $builder->add(
            'caption-color',
            TextType::class,
            [
                'label'      => 'autoborna.core.caption.color',
                'label_attr' => ['class' => 'control-label'],
                'required'   => false,
                'attr'       => [
                    'class'           => 'form-control',
                    'data-slot-param' => 'caption-color',
                    'data-toggle'     => 'color',
                ],
            ]
        );

        $builder->add(
            'color',
            TextType::class,
            [
                'label'      => 'autoborna.core.text.color',
                'label_attr' => ['class' => 'control-label'],
                'required'   => false,
                'attr'       => [
                    'class'           => 'form-control',
                    'data-slot-param' => 'color',
                    'data-toggle'     => 'color',
                ],
            ]
        );

        parent::buildForm($builder, $options);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'slot_imagecard';
    }
}
