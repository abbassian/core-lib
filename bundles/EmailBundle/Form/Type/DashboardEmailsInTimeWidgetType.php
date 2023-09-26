<?php

namespace Autoborna\EmailBundle\Form\Type;

use Autoborna\CampaignBundle\Form\Type\CampaignListType;
use Autoborna\LeadBundle\Form\Type\CompanyListType;
use Autoborna\LeadBundle\Form\Type\LeadListType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class DashboardEmailsInTimeWidgetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'flag',
            ChoiceType::class,
            [
                'label'             => 'autoborna.email.flag.filter',
                'choices'           => [
                    'autoborna.email.flag.sent'                       => '',
                    'autoborna.email.flag.opened'                     => 'opened',
                    'autoborna.email.flag.failed'                     => 'failed',
                    'autoborna.email.flag.sent.and.opened'            => 'sent_and_opened',
                    'autoborna.email.flag.sent.and.opened.and.failed' => 'sent_and_opened_and_failed',
                ],
                'label_attr' => ['class' => 'control-label'],
                'attr'       => ['class' => 'form-control'],
                'empty_data' => '',
                'required'   => false,
            ]
        );

        $builder->add(
            'companyId',
            CompanyListType::class,
            [
                'label'       => 'autoborna.email.companyId.filter',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => ['class' => 'form-control'],
                'placeholder' => '',
                'required'    => false,
                'multiple'    => false,
                'modal_route' => null,
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
                'label'       => 'autoborna.email.segmentId.filter',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => ['class' => 'form-control'],
                'placeholder' => '',
                'required'    => false,
            ]
        );
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'email_dashboard_emails_in_time_widget';
    }
}
