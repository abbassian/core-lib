<?php

namespace Autoborna\CoreBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

class SlotPreferredChannelType extends SlotType
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
                'data'       => $this->translator->trans('autoborna.lead.list.frequency.preferred.channel'),
            ]
        );

        parent::buildForm($builder, $options);
    }

    /**
     * @return mixed
     */
    public function getBlockPrefix()
    {
        return 'slot_preferredchannel';
    }
}
