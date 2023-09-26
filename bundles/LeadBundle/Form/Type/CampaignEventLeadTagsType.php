<?php

namespace Autoborna\LeadBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

class CampaignEventLeadTagsType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'tags',
            TagType::class,
            [
                'add_transformer' => true,
                'by_reference'    => false,
                'attr'            => [
                    'data-placeholder'     => $this->translator->trans('autoborna.lead.tags.select_or_create'),
                    'data-no-results-text' => $this->translator->trans('autoborna.lead.tags.enter_to_create'),
                    'data-allow-add'       => 'true',
                    'onchange'             => 'Autoborna.createLeadTag(this)',
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'campaignevent_lead_tags';
    }
}
