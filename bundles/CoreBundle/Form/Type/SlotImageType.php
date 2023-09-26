<?php

namespace Autoborna\CoreBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class SlotImageType.
 */
class SlotImageType extends SlotType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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

        parent::buildForm($builder, $options);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'slot_image';
    }
}
