<?php

namespace Autoborna\CoreBundle\Form\Type;

use Autoborna\CategoryBundle\Form\Type\CategoryListType;
use Autoborna\CoreBundle\Form\EventListener\CleanFormSubscriber;
use Autoborna\CoreBundle\Form\EventListener\FormExitSubscriber;
use Autoborna\CoreBundle\Security\Permissions\CorePermissions;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class AbstractFormStandardType.
 */
abstract class AbstractFormStandardType extends AbstractType
{
    /**
     * @var CorePermissions
     */
    protected $security;

    public function setSecurity(CorePermissions $security)
    {
        $this->security = $security;
    }

    /**
     * @throws \Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!isset($options['data'])) {
            throw new \Exception('$options[\'data\'] must be defined');
        }

        $masks = ['description' => 'strict_html'];
        if (isset($options['clean_masks'])) {
            $masks = array_merge($masks, $options['clean_masks']);
        }
        $builder->addEventSubscriber(new CleanFormSubscriber($masks));

        if (isset($options['model_name']) && method_exists($options['data'], 'getCheckedOutBy')) {
            $builder->addEventSubscriber(new FormExitSubscriber($options['model_name'], $options));
        }

        if (!$builder->has('name') && method_exists($options['data'], 'getName')) {
            $builder->add(
                'name',
                TextType::class,
                [
                    'label'      => 'autoborna.core.name',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => ['class' => 'form-control'],
                ]
            );
        }

        if (!$builder->has('description') && method_exists($options['data'], 'getDescription')) {
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
        }

        if (!$builder->has('category') && method_exists($options['data'], 'getCategory')) {
            $builder->add(
                'category',
                CategoryListType::class,
                [
                    'bundle' => isset($options['category_bundle']) ? $options['category_bundle'] : 'global',
                ]
            );
        }

        if (!$builder->has('isPublished') && method_exists($options['data'], 'getIsPublished')) {
            $readonly = false;
            $data     = $options['data']->isPublished(false);

            if ($this->security instanceof CorePermissions && isset($options['permission_base'])) {
                if (!empty($options['data']) && $options['data']->getId()) {
                    $readonly = !$this->security->hasEntityAccess(
                        $options['permission_base'].':publishown',
                        $options['permission_base'].':publishother',
                        $options['data']->getCreatedBy()
                    );
                } elseif (!$this->security->isGranted($options['permission_base'].':publishown')) {
                    $readonly = true;
                    $data     = false;
                } else {
                    $readonly = false;
                    $data     = true;
                }
            }

            $builder->add(
                'isPublished',
                YesNoButtonGroupType::class,
                [
                    'data' => $data,
                    'attr' => [
                        'readonly' => $readonly,
                    ],
                ]
            );

            if (!$builder->has('publishUp') && method_exists($options['data'], 'getPublishUp')) {
                $builder->add(
                    'publishUp',
                    DateTimeType::class,
                    [
                        'widget'     => 'single_text',
                        'label'      => 'autoborna.core.form.publishup',
                        'label_attr' => ['class' => 'control-label'],
                        'attr'       => [
                            'class'       => 'form-control',
                            'data-toggle' => 'datetime',
                            'readonly'    => $readonly,
                        ],
                        'format'     => 'yyyy-MM-dd HH:mm',
                        'required'   => false,
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
                            'readonly'    => $readonly,
                        ],
                        'format'     => 'yyyy-MM-dd HH:mm',
                        'required'   => false,
                    ]
                );
            }
        }

        if (!$builder->has('language') && method_exists($options['data'], 'getLanguage')) {
            $builder->add(
                'language',
                LocaleType::class,
                [
                    'label'      => 'autoborna.core.language',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class' => 'form-control',
                    ],
                    'required'   => false,
                ]
            );
        }

        $buttonOptions = isset($options['button_options']) ? $options['button_options'] : [];
        if (!empty($options['update_select'])) {
            if (!$builder->has('buttons')) {
                $builder->add(
                    'buttons',
                    FormButtonsType::class,
                    array_merge(
                        ['apply_text' => false],
                        $buttonOptions
                    )
                );
            }

            $builder->add(
                'updateSelect',
                HiddenType::class,
                array_merge(
                    [
                        'data'   => $options['update_select'],
                        'mapped' => false,
                    ],
                    $buttonOptions
                )
            );
        } elseif (!$builder->has('buttons')) {
            $builder->add(
                'buttons',
                FormButtonsType::class,
                $buttonOptions
            );
        }

        if (!empty($options['action'])) {
            $builder->setAction($options['action']);
        }
    }
}
