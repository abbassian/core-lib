<?php

namespace Autoborna\LeadBundle\Form\Type;

use Autoborna\CoreBundle\Form\Type\FormButtonsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class MergeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'lead_to_merge',
            ChoiceType::class,
            [
                'choices'           => $options['leads'],
                'label'             => 'autoborna.lead.merge.select',
                'label_attr'        => ['class' => 'control-label'],
                'multiple'          => false,
                'placeholder'       => '',
                'attr'              => [
                    'class'   => 'form-control',
                    'tooltip' => 'autoborna.lead.merge.select.modal.tooltip',
                ],
                'constraints' => [
                    new NotBlank(
                        [
                            'message' => 'autoborna.core.value.required',
                        ]
                    ),
                ],
            ]
        );

        $builder->add(
            'buttons',
            FormButtonsType::class,
            [
                'apply_text' => false,
                'save_text'  => 'autoborna.lead.merge',
                'save_icon'  => 'fa fa-user',
            ]
        );

        if (!empty($options['action'])) {
            $builder->setAction($options['action']);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['leads']);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'lead_merge';
    }
}
