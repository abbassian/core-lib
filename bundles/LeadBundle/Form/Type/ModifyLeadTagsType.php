<?php

namespace Autoborna\LeadBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ModifyLeadTagsType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'add_tags',
            TagType::class,
            [
                'label' => 'autoborna.lead.tags.add',
                'attr'  => [
                    'data-placeholder'     => $this->translator->trans('autoborna.lead.tags.select_or_create'),
                    'data-no-results-text' => $this->translator->trans('autoborna.lead.tags.enter_to_create'),
                    'data-allow-add'       => 'true',
                    'onchange'             => 'Autoborna.createLeadTag(this)',
                ],
                'data'            => (isset($options['data']['add_tags'])) ? $options['data']['add_tags'] : null,
                'add_transformer' => true,
            ]
        );

        $builder->add(
            'remove_tags',
            TagType::class,
            [
                'label' => 'autoborna.lead.tags.remove',
                'attr'  => [
                    'data-placeholder'     => $this->translator->trans('autoborna.lead.tags.select_or_create'),
                    'data-no-results-text' => $this->translator->trans('autoborna.lead.tags.enter_to_create'),
                    'data-allow-add'       => 'true',
                    'onchange'             => 'Autoborna.createLeadTag(this)',
                ],
                'data'            => (isset($options['data']['remove_tags'])) ? $options['data']['remove_tags'] : null,
                'add_transformer' => true,
            ]
        );
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'modify_lead_tags';
    }
}
