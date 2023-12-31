<?php

declare(strict_types=1);

namespace Autoborna\FormBundle\Form\Type;

use Autoborna\CoreBundle\Form\EventListener\CleanFormSubscriber;
use Autoborna\CoreBundle\Form\Type\YesNoButtonGroupType;
use Autoborna\FormBundle\Helper\PropertiesAccessor;
use Autoborna\FormBundle\Model\FieldModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormFieldConditionType extends AbstractType
{
    /**
     * @var FieldModel
     */
    private $fieldModel;

    /**
     * @var PropertiesAccessor
     */
    private $propertiesAccessor;

    public function __construct(FieldModel $fieldModel, PropertiesAccessor $propertiesAccessor)
    {
        $this->fieldModel          = $fieldModel;
        $this->propertiesAccessor  = $propertiesAccessor;
    }

    /**
     * @param FormBuilderInterface<string|FormBuilderInterface> $builder
     * @param mixed[]                                           $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventSubscriber(new CleanFormSubscriber(['values' => 'string']));

        $choices = [];
        if (!empty($options['parent'])) {
            $fields = $this->fieldModel->getSessionFields($options['formId']);
            if (isset($fields[$options['parent']])) {
                $choices = $this->propertiesAccessor->getChoices(
                    $this->propertiesAccessor->getProperties($fields[$options['parent']])
                );
            }
        }

        $builder->add(
            'values',
            ChoiceType::class,
            [
                'choices'  => $choices,
                'multiple' => true,
                'label'    => false,
                'attr'     => [
                    'class'        => 'form-control',
                    'data-show-on' => '{"formfield_conditions_any_0": "checked","formfield_conditions_expr": "notIn"}',
                ],
                'required' => false,
            ]
        );

        $builder->add(
            'any',
            YesNoButtonGroupType::class,
            [
                'label' => 'autoborna.form.field.form.condition.any_value',
                'attr'  => [
                    'data-show-on' => '{"formfield_conditions_expr": "in"}',
                ],
                'data' => $options['data']['any'] ?? false,
            ]
        );

        $builder->add(
            'expr',
            ChoiceType::class,
            [
                'choices'  => [
                    'autoborna.core.operator.in'    => 'in',
                    'autoborna.core.operator.notin' => 'notIn',
                ],
                'label'       => false,
                'placeholder' => false,
                'attr'        => [
                    'class' => 'form-control',
                ],
                'required' => false,
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'formId' => null,
                'parent' => null,
            ]
        );
    }
}
