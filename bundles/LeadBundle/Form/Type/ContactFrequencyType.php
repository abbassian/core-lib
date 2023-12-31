<?php

namespace Autoborna\LeadBundle\Form\Type;

use Autoborna\CoreBundle\Form\Type\FormButtonsType;
use Autoborna\CoreBundle\Helper\CoreParametersHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactFrequencyType extends AbstractType
{
    /**
     * @var CoreParametersHelper
     */
    protected $coreParametersHelper;

    public function __construct(CoreParametersHelper $coreParametersHelper)
    {
        $this->coreParametersHelper = $coreParametersHelper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $showContactCategories = $this->coreParametersHelper->get('show_contact_categories');
        $showContactSegments   = $this->coreParametersHelper->get('show_contact_segments');

        if (!empty($options['channels'])) {
            $builder->add(
                'lead_channels',
                ContactChannelsType::class,
                [
                    'label'       => false,
                    'channels'    => $options['channels'],
                    'data'        => $options['data']['lead_channels'],
                    'public_view' => $options['public_view'],
                ]
            );
        }

        if (!$options['public_view']) {
            $builder->add(
                'lead_lists',
                LeadListType::class,
                [
                    'label'      => 'autoborna.lead.form.list',
                    'label_attr' => ['class' => 'control-label'],
                    'multiple'   => true,
                    'expanded'   => $options['public_view'],
                    'required'   => false,
                ]
            );
        } elseif ($showContactSegments) {
            $builder->add(
                'lead_lists',
                LeadListType::class,
                [
                    'preference_center_only' => $options['preference_center_only'],
                    'label'                  => 'autoborna.lead.form.list',
                    'label_attr'             => ['class' => 'control-label'],
                    'multiple'               => true,
                    'expanded'               => true,
                    'required'               => false,
                ]
            );
        }

        if (!$options['public_view'] || $showContactCategories) {
            $builder->add(
                'global_categories',
                LeadCategoryType::class,
                [
                    'label'      => 'autoborna.lead.form.categories',
                    'label_attr' => ['class' => 'control-label'],
                    'multiple'   => true,
                    'expanded'   => $options['public_view'],
                    'required'   => false,
                ]
            );
        }

        $builder->add(
            'buttons',
            FormButtonsType::class,
            [
                'apply_text'     => false,
                'save_text'      => 'autoborna.core.form.save',
                'cancel_onclick' => 'javascript:void(0);',
                'cancel_attr'    => [
                    'data-dismiss' => 'modal',
                ],
            ]
        );

        if (!empty($options['action'])) {
            $builder->setAction($options['action']);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['channels']);
        $resolver->setDefaults(
            [
                'public_view'               => false,
                'preference_center_only'    => false,
            ]
        );
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'lead_contact_frequency_rules';
    }
}
