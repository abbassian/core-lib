<?php

namespace Autoborna\WebhookBundle\Form\Type;

use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class ConfigType.
 */
class ConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('queue_mode', ChoiceType::class, [
            'choices' => [
                'autoborna.webhook.config.immediate_process' => 'immediate_process',
                'autoborna.webhook.config.cron_process'      => 'command_process',
            ],
            'label' => 'autoborna.webhook.config.form.queue.mode',
            'attr'  => [
                'class'   => 'form-control',
                'tooltip' => 'autoborna.webhook.config.form.queue.mode.tooltip',
            ],
            'placeholder' => false,
            'constraints' => [
                new NotBlank(
                    [
                        'message' => 'autoborna.core.value.required',
                    ]
                ),
            ],
            ]);

        $builder->add('events_orderby_dir', ChoiceType::class, [
            'choices' => [
                'autoborna.webhook.config.event.orderby.chronological'         => Criteria::ASC,
                'autoborna.webhook.config.event.orderby.reverse.chronological' => Criteria::DESC,
            ],
            'label' => 'autoborna.webhook.config.event.orderby',
            'attr'  => [
                'class'   => 'form-control',
                'tooltip' => 'autoborna.webhook.config.event.orderby.tooltip',
            ],
            'required'          => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'webhookconfig';
    }
}
