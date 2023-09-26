<?php

namespace Autoborna\FormBundle\Form\Type;

use Autoborna\CoreBundle\Form\Type\YesNoButtonGroupType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

class FormFieldEmailType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * FormFieldTelType constructor.
     */
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
            'donotsubmit',
            YesNoButtonGroupType::class,
            [
                'label' => 'autoborna.form.field.type.donotsubmit',
                'data'  => isset($options['data']['donotsubmit']) ? $options['data']['donotsubmit'] : false,
            ]
        );

        $builder->add(
            'donotsubmit_validationmsg',
            TextType::class,
            [
                'label'      => 'autoborna.form.field.form.validationmsg',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'        => 'form-control',
                    'data-show-on' => '{"formfield_validation_donotsubmit_1": "checked"}',
                ],
                'data'     => isset($options['data']['donotsubmit_validationmsg']) ? $options['data']['donotsubmit_validationmsg'] : $this->translator->trans('autoborna.form.submission.email.donotsubmit.invalid', [], 'validators'),
                'required' => false,
            ]
        );
    }
}
