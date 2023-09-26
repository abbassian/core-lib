<?php

namespace Autoborna\LeadBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class DashboardLeadsInTimeWidgetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'flag',
            ChoiceType::class,
            [
                'label'             => 'autoborna.lead.list.filter',
                'choices'           => [
                    'autoborna.lead.show.all'                               => '',
                    'autoborna.lead.show.identified'                        => 'identified',
                    'autoborna.lead.show.anonymous'                         => 'anonymous',
                    'autoborna.lead.show.identified.vs.anonymous'           => 'identifiedVsAnonymous',
                    'autoborna.lead.show.top'                               => 'top',
                    'autoborna.lead.show.top.leads.identified.vs.anonymous' => 'topIdentifiedVsAnonymous',
                ],
                'label_attr' => ['class' => 'control-label'],
                'attr'       => ['class' => 'form-control'],
                'empty_data' => '',
                'required'   => false,
            ]
        );
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'lead_dashboard_leads_in_time_widget';
    }
}
