<?php

namespace Autoborna\FormBundle\Form\Type;

use Autoborna\CoreBundle\Form\Type\YesNoButtonGroupType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class FormFieldTextType.
 */
class FormFieldTelType extends AbstractType
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
            'international',
            YesNoButtonGroupType::class,
            [
                'label' => 'autoborna.form.field.type.tel.international',
                'data'  => isset($options['data']['international']) ? $options['data']['international'] : false,
            ]
        );

        $builder->add(
            'international_validationmsg',
            TextType::class,
            [
                'label'      => 'autoborna.form.field.form.validationmsg',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'        => 'form-control',
                    'tooltip'      => $this->translator->trans('autoborna.core.form.default').': '.$this->translator->trans('autoborna.form.submission.phone.invalid', [], 'validators'),
                    'data-show-on' => '{"formfield_validation_international_1": "checked"}',
                ],
                'required' => false,
            ]
        );
    }
}
