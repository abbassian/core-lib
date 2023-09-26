<?php

namespace Autoborna\CampaignBundle\Form\Type;

use Autoborna\CoreBundle\Form\EventListener\CleanFormSubscriber;
use Autoborna\CoreBundle\Form\Type\ButtonGroupType;
use Autoborna\CoreBundle\Form\Type\FormButtonsType;
use Autoborna\CoreBundle\Form\Type\PropertiesTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class EventType.
 */
class EventType extends AbstractType
{
    use PropertiesTrait;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $masks = [];

        $builder->add(
            'name',
            TextType::class,
            [
                'label'      => 'autoborna.core.name',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => ['class' => 'form-control'],
                'required'   => false,
            ]
        );

        $builder->add(
            'anchor',
            HiddenType::class,
            [
                'label' => false,
            ]
        );

        if (in_array($options['data']['eventType'], ['action', 'condition'])) {
            $label = 'autoborna.campaign.form.type';

            $choices = [
                'immediate' => 'autoborna.campaign.form.type.immediate',
                'interval'  => 'autoborna.campaign.form.type.interval',
                'date'      => 'autoborna.campaign.form.type.date',
            ];

            if ('no' == $options['data']['anchor'] && 'condition' != $options['data']['anchorEventType']
                && 'condition' != $options['data']['eventType']
            ) {
                $label .= '_inaction';

                unset($choices['immediate']);
                $choices['interval'] = $choices['interval'].'_inaction';
                $choices['date']     = $choices['date'].'_inaction';
            }

            reset($choices);
            $default = key($choices);

            $triggerMode = (empty($options['data']['triggerMode'])) ? $default : $options['data']['triggerMode'];
            $builder->add(
                'triggerMode',
                ButtonGroupType::class,
                [
                    'choices'           => array_flip($choices),
                    'expanded'          => true,
                    'multiple'          => false,
                    'label_attr'        => ['class' => 'control-label'],
                    'label'             => $label,
                    'placeholder'       => false,
                    'required'          => false,
                    'attr'              => [
                        'onchange' => 'Autoborna.campaignToggleTimeframes();',
                        'tooltip'  => 'autoborna.campaign.form.type.help',
                    ],
                    'data'        => $triggerMode,
                ]
            );

            $builder->add(
                'triggerDate',
                DateTimeType::class,
                [
                    'label'  => false,
                    'attr'   => [
                        'class'       => 'form-control',
                        'preaddon'    => 'fa fa-calendar',
                        'data-toggle' => 'datetime',
                    ],
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd HH:mm',
                ]
            );

            $data = (!isset($options['data']['triggerInterval']) || '' === $options['data']['triggerInterval']
                || null === $options['data']['triggerInterval']) ? 1 : (int) $options['data']['triggerInterval'];
            $builder->add(
                'triggerInterval',
                NumberType::class,
                [
                    'label' => false,
                    'attr'  => [
                        'class'    => 'form-control',
                        'preaddon' => 'symbol-hashtag',
                    ],
                    'data'  => $data,
                ]
            );

            $data = (!empty($options['data']['triggerIntervalUnit'])) ? $options['data']['triggerIntervalUnit'] : 'd';
            $builder->add(
                'triggerIntervalUnit',
                ChoiceType::class,
                [
                    'choices'     => [
                        'autoborna.campaign.event.intervalunit.choice.i' => 'i',
                        'autoborna.campaign.event.intervalunit.choice.h' => 'h',
                        'autoborna.campaign.event.intervalunit.choice.d' => 'd',
                        'autoborna.campaign.event.intervalunit.choice.m' => 'm',
                        'autoborna.campaign.event.intervalunit.choice.y' => 'y',
                    ],
                    'multiple'          => false,
                    'label_attr'        => ['class' => 'control-label'],
                    'label'             => false,
                    'attr'              => [
                        'class' => 'form-control',
                    ],
                    'placeholder' => false,
                    'required'    => false,
                    'data'        => $data,
                ]
            );

            // I could not get Doctrine TimeType does not play well with Symfony TimeType so hacking this workaround
            $data = $this->getTimeValue($options['data'], 'triggerHour');
            $builder->add(
                'triggerHour',
                TextType::class,
                [
                    'label' => false,
                    'attr'  => [
                        'class'        => 'form-control',
                        'data-toggle'  => 'time',
                        'data-format'  => 'H:i',
                        'autocomplete' => 'off',
                    ],
                    'data'  => ($data) ? $data->format('H:i') : $data,
                ]
            );

            $data = $this->getTimeValue($options['data'], 'triggerRestrictedStartHour');
            $builder->add(
                'triggerRestrictedStartHour',
                TextType::class,
                [
                    'label' => false,
                    'attr'  => [
                        'class'        => 'form-control',
                        'data-toggle'  => 'time',
                        'data-format'  => 'H:i',
                        'autocomplete' => 'off',
                    ],
                    'data'  => ($data) ? $data->format('H:i') : $data,
                ]
            );

            $data = $this->getTimeValue($options['data'], 'triggerRestrictedStopHour');
            $builder->add(
                'triggerRestrictedStopHour',
                TextType::class,
                [
                    'label' => false,
                    'attr'  => [
                        'class'        => 'form-control',
                        'data-toggle'  => 'time',
                        'data-format'  => 'H:i',
                        'autocomplete' => 'off',
                    ],
                    'data'  => ($data) ? $data->format('H:i') : $data,
                ]
            );

            $builder->add(
                'triggerRestrictedDaysOfWeek',
                ChoiceType::class,
                [
                    'label'    => true,
                    'attr'     => [
                        'data-toggle' => 'time',
                        'data-format' => 'H:i',
                    ],
                    'choices'  => [
                        'autoborna.report.schedule.day.monday'     => 1,
                        'autoborna.report.schedule.day.tuesday'    => 2,
                        'autoborna.report.schedule.day.wednesday'  => 3,
                        'autoborna.report.schedule.day.thursday'   => 4,
                        'autoborna.report.schedule.day.friday'     => 5,
                        'autoborna.report.schedule.day.saturday'   => 6,
                        'autoborna.report.schedule.day.sunday'     => 0,
                        'autoborna.report.schedule.day.week_days'  => -1,
                    ],
                    'expanded'          => true,
                    'multiple'          => true,
                    'required'          => false,
                ]
            );
        }

        if (!empty($options['settings']['formType'])) {
            $this->addPropertiesType($builder, $options, $masks);
        }

        $builder->add('type', HiddenType::class);
        $builder->add('eventType', HiddenType::class);
        $builder->add(
            'anchorEventType',
            HiddenType::class,
            [
                'mapped' => false,
                'data'   => (isset($options['data']['anchorEventType'])) ? $options['data']['anchorEventType'] : '',
            ]
        );

        $builder->add(
            'canvasSettings',
            EventCanvasSettingsType::class,
            [
                'label' => false,
            ]
        );

        $update = !empty($options['data']['properties']);
        if (!empty($update)) {
            $btnValue = 'autoborna.core.form.update';
            $btnIcon  = 'fa fa-pencil';
        } else {
            $btnValue = 'autoborna.core.form.add';
            $btnIcon  = 'fa fa-plus';
        }

        $builder->add(
            'buttons',
            FormButtonsType::class,
            [
                'save_text'       => $btnValue,
                'save_icon'       => $btnIcon,
                'save_onclick'    => 'Autoborna.submitCampaignEvent(event)',
                'apply_text'      => false,
                'container_class' => 'bottom-form-buttons',
            ]
        );

        $builder->add(
            'campaignId',
            HiddenType::class,
            [
                'mapped' => false,
            ]
        );

        $builder->addEventSubscriber(new CleanFormSubscriber($masks));

        if (!empty($options['action'])) {
            $builder->setAction($options['action']);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['settings']);
    }

    /**
     * @param $name
     *
     * @return \DateTime|mixed|null
     */
    private function getTimeValue(array $data, $name)
    {
        if (empty($data[$name])) {
            return null;
        }

        if ($data[$name] instanceof \DateTime) {
            return $data[$name];
        }

        return new \DateTime($data[$name]);
    }

    public function getBlockPrefix()
    {
        return 'campaignevent';
    }
}
