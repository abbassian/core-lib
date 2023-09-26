<?php

namespace Autoborna\EmailBundle\Form\Type;

use Autoborna\ChannelBundle\Entity\MessageQueue;
use Autoborna\CoreBundle\Form\Type\ButtonGroupType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class EmailSendType extends AbstractType
{
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'email',
            EmailListType::class,
            [
                'label'      => 'autoborna.email.send.selectemails',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'    => 'form-control',
                    'tooltip'  => 'autoborna.email.choose.emails_descr',
                    'onchange' => 'Autoborna.disabledEmailAction(window, this)',
                ],
                'multiple'    => false,
                'required'    => true,
                'constraints' => [
                    new NotBlank(
                        ['message' => 'autoborna.email.chooseemail.notblank']
                    ),
                ],
            ]
        );

        if (!empty($options['with_email_types'])) {
            $builder->add(
                'email_type',
                ButtonGroupType::class,
                [
                    'choices'           => [
                        'autoborna.email.send.emailtype.transactional' => 'transactional',
                        'autoborna.email.send.emailtype.marketing'     => 'marketing',
                    ],
                    'label'      => 'autoborna.email.send.emailtype',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class'   => 'form-control email-type',
                        'tooltip' => 'autoborna.email.send.emailtype.tooltip',
                    ],
                    'data' => (!isset($options['data']['email_type'])) ? 'transactional' : $options['data']['email_type'],
                ]
            );
        }

        if (!empty($options['update_select'])) {
            $windowUrl = $this->router->generate(
                'autoborna_email_action',
                [
                    'objectAction' => 'new',
                    'contentOnly'  => 1,
                    'updateSelect' => $options['update_select'],
                ]
            );

            $builder->add(
                'newEmailButton',
                ButtonType::class,
                [
                    'attr' => [
                        'class'   => 'btn btn-default btn-nospin',
                        'onclick' => 'Autoborna.loadNewWindow({
                            "windowUrl": "'.$windowUrl.'"
                        })',
                        'icon' => 'fa fa-plus',
                    ],
                    'label' => 'autoborna.email.send.new.email',
                ]
            );

            // create button edit email
            $windowUrlEdit = $this->router->generate(
                'autoborna_email_action',
                [
                    'objectAction' => 'edit',
                    'objectId'     => 'emailId',
                    'contentOnly'  => 1,
                    'updateSelect' => $options['update_select'],
                ]
            );

            $builder->add(
                'editEmailButton',
                ButtonType::class,
                [
                    'attr' => [
                        'class'    => 'btn btn-default btn-nospin',
                        'onclick'  => 'Autoborna.loadNewWindow(Autoborna.standardEmailUrl({"windowUrl": "'.$windowUrlEdit.'","origin":"#'.$options['update_select'].'"}))',
                        'disabled' => !isset($options['data']['email']),
                        'icon'     => 'fa fa-edit',
                    ],
                    'label' => 'autoborna.email.send.edit.email',
                ]
            );

            // create button preview email
            $windowUrlPreview = $this->router->generate('autoborna_email_preview', ['objectId' => 'emailId']);

            $builder->add(
                'previewEmailButton',
                ButtonType::class,
                [
                    'attr' => [
                        'class'    => 'btn btn-default btn-nospin',
                        'onclick'  => 'Autoborna.loadNewWindow(Autoborna.standardEmailUrl({"windowUrl": "'.$windowUrlPreview.'","origin":"#'.$options['update_select'].'"}))',
                        'disabled' => !isset($options['data']['email']),
                        'icon'     => 'fa fa-external-link',
                    ],
                    'label' => 'autoborna.email.send.preview.email',
                ]
            );
            if (!empty($options['with_email_types'])) {
                $data = (!isset($options['data']['priority'])) ? 2 : (int) $options['data']['priority'];
                $builder->add(
                    'priority',
                    ChoiceType::class,
                    [
                        'choices'           => [
                            'autoborna.channel.message.send.priority.normal' => MessageQueue::PRIORITY_NORMAL,
                            'autoborna.channel.message.send.priority.high'   => MessageQueue::PRIORITY_HIGH,
                        ],
                        'label'    => 'autoborna.channel.message.send.priority',
                        'required' => false,
                        'attr'     => [
                            'class'        => 'form-control',
                            'tooltip'      => 'autoborna.channel.message.send.priority.tooltip',
                            'data-show-on' => '{"campaignevent_properties_email_type_1":"checked"}',
                        ],
                        'data'        => $data,
                        'placeholder' => false,
                    ]
                );

                $data = (!isset($options['data']['attempts'])) ? 3 : (int) $options['data']['attempts'];
                $builder->add(
                    'attempts',
                    NumberType::class,
                    [
                        'label' => 'autoborna.channel.message.send.attempts',
                        'attr'  => [
                            'class'        => 'form-control',
                            'tooltip'      => 'autoborna.channel.message.send.attempts.tooltip',
                            'data-show-on' => '{"campaignevent_properties_email_type_1":"checked"}',
                        ],
                        'data'       => $data,
                        'empty_data' => 0,
                        'required'   => false,
                    ]
                );
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'with_email_types' => false,
            ]
        );

        $resolver->setDefined(['update_select', 'with_email_types']);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'emailsend_list';
    }
}
