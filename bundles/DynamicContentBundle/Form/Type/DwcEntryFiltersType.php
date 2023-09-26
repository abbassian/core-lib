<?php

namespace Autoborna\DynamicContentBundle\Form\Type;

use Autoborna\LeadBundle\Form\Type\FilterTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class DwcEntryFiltersType.
 */
class DwcEntryFiltersType extends AbstractType
{
    use FilterTrait;

    private $translator;

    /**
     * DwcEntryFiltersType constructor.
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'glue',
            ChoiceType::class,
            [
                'label'   => false,
                'choices' => [
                    'autoborna.lead.list.form.glue.and' => 'and',
                    'autoborna.lead.list.form.glue.or'  => 'or',
                ],
                'attr'              => [
                    'class'    => 'form-control not-chosen glue-select',
                    'onchange' => 'Autoborna.updateFilterPositioning(this)',
                ],
            ]
        );

        $formModifier = function (FormEvent $event, $eventName) {
            $this->buildFiltersForm($eventName, $event, $this->translator);
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $formModifier($event, FormEvents::PRE_SET_DATA);
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $formModifier($event, FormEvents::PRE_SUBMIT);
            }
        );

        $builder->add('field', HiddenType::class);
        $builder->add('object', HiddenType::class);
        $builder->add('type', HiddenType::class);
    }

    /**
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(
            [
                'countries',
                'regions',
                'timezones',
                'locales',
                'fields',
                'deviceTypes',
                'deviceBrands',
                'deviceOs',
                'tags',
            ]
        );

        $resolver->setDefaults(
            [
                'label'          => false,
                'error_bubbling' => false,
            ]
        );
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'dwc_entry_filters';
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['fields'] = $options['fields'];
    }
}
