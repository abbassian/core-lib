<?php

namespace Autoborna\CampaignBundle\Form\Type;

use Autoborna\CoreBundle\Form\Type\ButtonGroupType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class CampaignEventLeadChangeType.
 */
class CampaignEventLeadChangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = (isset($options['data']['action'])) ? $options['data']['action'] : 'added';
        $builder->add('action', ButtonGroupType::class, [
            'choices' => [
                'autoborna.campaign.form.trigger_leadchanged_added'   => 'added',
                'autoborna.campaign.form.trigger_leadchanged_removed' => 'removed',
            ],
            'expanded'          => true,
            'multiple'          => false,
            'label_attr'        => ['class' => 'control-label'],
            'label'             => 'autoborna.campaign.form.trigger_leadchanged',
            'placeholder'       => false,
            'required'          => false,
            'data'              => $data,
        ]);

        $builder->add('campaigns', CampaignListType::class, [
            'label'      => 'autoborna.campaign.form.limittocampaigns',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => [
                'class'   => 'form-control',
                'tooltip' => 'autoborna.campaign.form.limittocampaigns_descr',
            ],
            'required' => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'campaignevent_leadchange';
    }
}
