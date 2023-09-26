<?php

namespace Autoborna\LeadBundle\Form\Type;

use Autoborna\CampaignBundle\Form\Type\CampaignListType;
use Autoborna\CoreBundle\Form\Type\YesNoButtonGroupType;
use Autoborna\LeadBundle\Model\ListModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CampaignEventLeadCampaignsType extends AbstractType
{
    /**
     * @var ListModel
     */
    protected $listModel;

    public function __construct(ListModel $listModel)
    {
        $this->listModel = $listModel;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('campaigns',
            CampaignListType::class, [
            'label'      => 'autoborna.lead.lead.events.campaigns.membership',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => [
                'class' => 'form-control',
            ],
            'required' => true,
        ]);

        $builder->add(
            'dataAddedLimit',
            YesNoButtonGroupType::class,
            [
                'label' => 'autoborna.lead.lead.events.campaigns.date.added.filter',
                'data'  => (isset($options['data']['dataAddedLimit'])) ? $options['data']['dataAddedLimit'] : false,
            ]
        );

        $builder->add(
            'expr',
            ChoiceType::class,
            [
                'label'             => 'autoborna.lead.lead.events.campaigns.expression',
                'multiple'          => false,
                'choices'           => $this->listModel->getOperatorsForFieldType([
                    'include' => [
                        'gt',
                        'lt',
                    ],
                ]),
                'required'   => false,
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'        => 'form-control',
                    'data-show-on' => '{"campaignevent_properties_dataAddedLimit_1":"checked"}',
                ],
            ]
        );

        $builder->add(
            'dateAdded',
            TextType::class,
            [
                'label'      => 'autoborna.lead.lead.events.campaigns.date',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'        => 'form-control',
                    'data-toggle'  => 'datetime',
                    'data-show-on' => '{"campaignevent_properties_dataAddedLimit_1":"checked"}',
                ],
                'required' => false,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'campaignevent_lead_campaigns';
    }
}
