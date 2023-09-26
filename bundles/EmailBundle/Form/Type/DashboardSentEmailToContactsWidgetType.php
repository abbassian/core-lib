<?php

namespace Autoborna\EmailBundle\Form\Type;

use Autoborna\CampaignBundle\Form\Type\CampaignListType;
use Autoborna\LeadBundle\Form\Type\CompanyListType;
use Autoborna\LeadBundle\Form\Type\LeadListType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class DashboardSentEmailToContactsWidgetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'companyId',
            CompanyListType::class,
            [
                'label'       => 'autoborna.email.companyId.filter',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => ['class' => 'form-control'],
                'empty_data'  => '',
                'required'    => false,
                'multiple'    => false,
                'modal_route' => null, // disable "Add new" option in ajax lookup
            ]
        );

        $builder->add(
            'campaignId',
            CampaignListType::class,
            [
                'label'       => 'autoborna.email.campaignId.filter',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => ['class' => 'form-control'],
                'empty_data'  => '',
                'placeholder' => '',
                'required'    => false,
                'multiple'    => false,
            ]
        );

        $builder->add(
            'segmentId',
            LeadListType::class,
            [
                'label'      => 'autoborna.email.segmentId.filter',
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
        return 'email_dashboard_sent_email_to_contacts_widget';
    }
}
