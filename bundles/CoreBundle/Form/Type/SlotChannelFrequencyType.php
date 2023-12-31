<?php

namespace Autoborna\CoreBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

class SlotChannelFrequencyType extends SlotType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * ConfigType constructor.
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'label-text',
            TextType::class,
            [
                'label'      => 'autoborna.lead.field.label',
                'label_attr' => ['class' => 'control-label'],
                'required'   => false,
                'attr'       => [
                    'class'           => 'form-control',
                    'data-slot-param' => 'label-text',
                ],
                'data'       => $this->translator->trans('autoborna.lead.contact.me.label'),
            ]
        );

        $builder->add(
            'label-text1',
            TextType::class,
            [
                'label'      => 'autoborna.page.form.frequency.label1',
                'label_attr' => ['class' => 'control-label'],
                'required'   => false,
                'attr'       => [
                    'class'           => 'form-control',
                    'data-slot-param' => 'label-text1',
                ],
                'data'       => $this->translator->trans('autoborna.lead.list.frequency.number'),
            ]
        );

        $builder->add(
            'label-text2',
            TextType::class,
            [
                'label'      => 'autoborna.page.form.frequency.label2',
                'label_attr' => ['class' => 'control-label'],
                'required'   => false,
                'attr'       => [
                    'class'           => 'form-control',
                    'data-slot-param' => 'label-text2',
                ],
                'data'       => $this->translator->trans('autoborna.lead.list.frequency.times'),
            ]
        );

        $builder->add(
            'label-text3',
            TextType::class,
            [
                'label'      => 'autoborna.page.form.pause.label1',
                'label_attr' => ['class' => 'control-label'],
                'required'   => false,
                'attr'       => [
                    'class'           => 'form-control',
                    'data-slot-param' => 'label-text3',
                ],
                'data'       => $this->translator->trans('autoborna.lead.frequency.dates.label'),
            ]
        );

        $builder->add(
            'label-text4',
            TextType::class,
            [
                'label'      => 'autoborna.page.form.pause.label2',
                'label_attr' => ['class' => 'control-label'],
                'required'   => false,
                'attr'       => [
                    'class'           => 'form-control',
                    'data-slot-param' => 'label-text4',
                ],
                'data'       => $this->translator->trans('autoborna.lead.frequency.contact.end.date'),
            ]
        );

        parent::buildForm($builder, $options);
    }

    /**
     * @return mixed
     */
    public function getBlockPrefix()
    {
        return 'slot_channelfrequency';
    }
}
