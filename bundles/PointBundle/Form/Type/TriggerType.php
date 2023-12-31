<?php

namespace Autoborna\PointBundle\Form\Type;

use Autoborna\CategoryBundle\Form\Type\CategoryListType;
use Autoborna\CoreBundle\Form\EventListener\CleanFormSubscriber;
use Autoborna\CoreBundle\Form\EventListener\FormExitSubscriber;
use Autoborna\CoreBundle\Form\Type\FormButtonsType;
use Autoborna\CoreBundle\Form\Type\YesNoButtonGroupType;
use Autoborna\CoreBundle\Security\Permissions\CorePermissions;
use Autoborna\PointBundle\Entity\Trigger;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TriggerType extends AbstractType
{
    /**
     * @var CorePermissions
     */
    private $security;

    public function __construct(CorePermissions $security)
    {
        $this->security = $security;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new CleanFormSubscriber(['description' => 'html']));
        $builder->addEventSubscriber(new FormExitSubscriber('point', $options));

        $builder->add(
            'name',
            TextType::class,
            [
                'label'      => 'autoborna.core.name',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => ['class' => 'form-control'],
            ]
        );

        $builder->add(
            'description',
            TextareaType::class,
            [
                'label'      => 'autoborna.core.description',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => ['class' => 'form-control editor'],
                'required'   => false,
            ]
        );

        $builder->add(
            'category',
            CategoryListType::class,
            [
                'bundle' => 'point',
            ]
        );

        $builder->add(
            'points',
            NumberType::class,
            [
                'label'      => 'autoborna.point.trigger.form.points',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'   => 'form-control',
                    'tooltip' => 'autoborna.point.trigger.form.points_descr',
                ],
                'required' => false,
            ]
        );

        $color = $options['data']->getColor();

        $builder->add(
            'color',
            TextType::class,
            [
                'label'      => 'autoborna.point.trigger.form.color',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                    'data-toggle' => 'color',
                    'tooltip'     => 'autoborna.point.trigger.form.color_descr',
                ],
                'required'   => false,
                'data'       => (!empty($color)) ? $color : 'a0acb8',
                'empty_data' => 'a0acb8',
            ]
        );

        $builder->add(
            'triggerExistingLeads',
            YesNoButtonGroupType::class,
            [
                'label' => 'autoborna.point.trigger.form.existingleads',
            ]
        );

        if (!empty($options['data']) && $options['data']->getId()) {
            $readonly = !$this->security->isGranted('point:triggers:publish');
            $data     = $options['data']->isPublished(false);
        } elseif (!$this->security->isGranted('point:triggers:publish')) {
            $readonly = true;
            $data     = false;
        } else {
            $readonly = false;
            $data     = false;
        }

        $builder->add(
            'isPublished',
            YesNoButtonGroupType::class,
            [
                'data'      => $data,
                'attr'      => [
                    'readonly' => $readonly,
                ],
            ]
        );

        $builder->add(
            'publishUp',
            DateTimeType::class,
            [
                'widget'         => 'single_text',
                'label'          => 'autoborna.core.form.publishup',
                'label_attr'     => ['class' => 'control-label'],
                    'attr'       => [
                    'class'       => 'form-control',
                    'data-toggle' => 'datetime',
                ],
                'format'   => 'yyyy-MM-dd HH:mm',
                'required' => false,
            ]
        );

        $builder->add(
            'publishDown',
            DateTimeType::class,
            [
                'widget'     => 'single_text',
                'label'      => 'autoborna.core.form.publishdown',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                    'data-toggle' => 'datetime',
                ],
                'format'   => 'yyyy-MM-dd HH:mm',
                'required' => false,
            ]
        );

        $builder->add(
            'sessionId',
            HiddenType::class,
            [
                'mapped' => false,
            ]
        );

        $builder->add('buttons', FormButtonsType::class);

        if (!empty($options['action'])) {
            $builder->setAction($options['action']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Trigger::class,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'pointtrigger';
    }
}
