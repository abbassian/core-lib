<?php

namespace Autoborna\FormBundle\Form\Type;

use Autoborna\CoreBundle\Form\Type\YesNoButtonGroupType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FormFieldSelectType.
 */
class FormFieldSelectType extends AbstractType
{
    use SortableListTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ('select' === $options['field_type']) {
            $this->addSortableList($builder, $options);
        }

        $builder->add(
            'placeholder',
            TextType::class,
            [
                'label'      => 'autoborna.form.field.form.emptyvalue',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => ['class' => 'form-control'],
                'required'   => false,
            ]
        );

        if (!empty($options['parentData'])) {
            $default = (empty($options['parentData']['properties']['multiple'])) ? false : true;
        } else {
            $default = false;
        }
        $builder->add(
            'multiple',
            YesNoButtonGroupType::class,
            [
                'label' => 'autoborna.form.field.form.multiple',
                'data'  => $default,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'field_type' => 'select',
                'parentData' => [],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'formfield_select';
    }
}
