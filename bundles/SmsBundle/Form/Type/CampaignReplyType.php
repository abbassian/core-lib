<?php

namespace Autoborna\SmsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CampaignReplyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'pattern',
            TextType::class,
            [
                'label'      => 'autoborna.sms.reply_pattern',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'    => 'form-control',
                    'tooltip'  => 'autoborna.sms.reply_pattern.tooltip',
                ],
                'required'    => false,
            ]
        );
    }
}
