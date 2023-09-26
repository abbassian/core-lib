<?php

namespace Autoborna\InstallBundle\Configurator\Form;

use Autoborna\CoreBundle\Form\Type\FormButtonsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Validator\Constraints as Assert;

class UserStepType extends AbstractType
{
    /**
     * @var Session
     */
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $storedData = $this->session->get('autoborna.installer.user', new \stdClass());

        $builder->add(
            'firstname',
            TextType::class,
            [
                'label'       => 'autoborna.core.firstname',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => ['class' => 'form-control'],
                'required'    => true,
                'data'        => (!empty($storedData->firstname)) ? $storedData->firstname : '',
                'constraints' => [
                    new Assert\NotBlank(
                        [
                            'message' => 'autoborna.core.value.required',
                        ]
                    ),
                ],
            ]
        );

        $builder->add(
            'lastname',
            TextType::class,
            [
                'label'       => 'autoborna.core.lastname',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => ['class' => 'form-control'],
                'required'    => true,
                'data'        => (!empty($storedData->lastname)) ? $storedData->lastname : '',
                'constraints' => [
                    new Assert\NotBlank(
                        [
                            'message' => 'autoborna.core.value.required',
                        ]
                    ),
                ],
            ]
        );

        $builder->add(
            'email',
            EmailType::class,
            [
                'label'      => 'autoborna.install.form.user.email',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'    => 'form-control',
                    'preaddon' => 'fa fa-envelope',
                ],
                'required'    => true,
                'data'        => (!empty($storedData->email)) ? $storedData->email : '',
                'constraints' => [
                    new Assert\NotBlank(
                        [
                            'message' => 'autoborna.core.value.required',
                        ]
                    ),
                    new Assert\Email(
                        [
                            'message' => 'autoborna.core.email.required',
                        ]
                    ),
                ],
            ]
        );

        $builder->add(
            'username',
            TextType::class,
            [
                'label'      => 'autoborna.install.form.user.username',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class' => 'form-control',
                ],
                'required'    => true,
                'data'        => (!empty($storedData->username)) ? $storedData->username : '',
                'constraints' => [
                    new Assert\NotBlank(
                        [
                            'message' => 'autoborna.core.value.required',
                        ]
                    ),
                ],
            ]
        );

        $builder->add(
            'password',
            PasswordType::class,
            [
                'label'      => 'autoborna.install.form.user.password',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'    => 'form-control',
                    'tooltip'  => 'autoborna.user.user.form.help.passwordrequirements',
                    'preaddon' => 'fa fa-lock',
                ],
                'required'    => true,
                'constraints' => [
                    new Assert\NotBlank(
                        [
                            'message' => 'autoborna.core.value.required',
                        ]
                    ),
                    new Assert\Length(
                        [
                            'min'        => 6,
                            'minMessage' => 'autoborna.install.password.minlength',
                        ]
                    ),
                ],
            ]
        );

        $builder->add(
            'buttons',
            FormButtonsType::class,
            [
                'pre_extra_buttons' => [
                    [
                        'name'  => 'next',
                        'label' => 'autoborna.install.next.step',
                        'type'  => 'submit',
                        'attr'  => [
                            'class'   => 'btn btn-success pull-right btn-next',
                            'icon'    => 'fa fa-arrow-circle-right',
                            'onclick' => 'AutobornaInstaller.showWaitMessage(event);',
                        ],
                    ],
                ],
                'apply_text'  => '',
                'save_text'   => '',
                'cancel_text' => '',
            ]
        );

        if (!empty($options['action'])) {
            $builder->setAction($options['action']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'install_user_step';
    }
}
