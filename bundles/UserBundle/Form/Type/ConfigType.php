<?php

namespace Autoborna\UserBundle\Form\Type;

use Autoborna\ConfigBundle\Form\Type\ConfigFileType;
use Autoborna\CoreBundle\Helper\CoreParametersHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\File;

class ConfigType extends AbstractType
{
    /**
     * @var CoreParametersHelper
     */
    protected $parameters;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(CoreParametersHelper $parametersHelper, TranslatorInterface $translator)
    {
        $this->parameters = $parametersHelper;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'saml_idp_metadata',
            ConfigFileType::class,
            [
                'label'      => 'autoborna.user.config.form.saml.idp.metadata',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'   => 'form-control',
                    'tooltip' => 'autoborna.user.config.form.saml.idp.metadata.tooltip',
                    'rows'    => 10,
                ],
                'required'    => false,
                'constraints' => [
                    new File(
                        [
                            'mimeTypes'        => ['text/plain', 'text/xml', 'application/xml'],
                            'mimeTypesMessage' => 'autoborna.core.invalid_file_type',
                        ]
                    ),
                ],
            ]
        );

        $builder->add(
            'saml_idp_own_certificate',
            ConfigFileType::class,
            [
                'label'      => 'autoborna.user.config.form.saml.idp.own_certificate',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'   => 'form-control',
                    'tooltip' => 'autoborna.user.config.form.saml.idp.own_certificate.tooltip',
                ],
                'required'    => false,
                'constraints' => [
                    new File(
                        [
                            'mimeTypes'        => ['text/plain'],
                            'mimeTypesMessage' => 'autoborna.core.invalid_file_type',
                        ]
                    ),
                ],
            ]
        );

        $builder->add(
            'saml_idp_own_private_key',
            ConfigFileType::class,
            [
                'label'      => 'autoborna.user.config.form.saml.idp.own_private_key',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'   => 'form-control',
                    'tooltip' => 'autoborna.user.config.form.saml.idp.own_private_key.tooltip',
                ],
                'required'    => false,
                'constraints' => [
                    new File(
                        [
                            'mimeTypes'        => ['text/plain'],
                            'mimeTypesMessage' => 'autoborna.core.invalid_file_type',
                        ]
                    ),
                ],
            ]
        );

        $builder->add(
            'saml_idp_own_password',
            PasswordType::class,
            [
                'label'      => 'autoborna.user.config.form.saml.idp.own_password',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'   => 'form-control',
                    'tooltip' => 'autoborna.user.config.form.saml.idp.own_password.tooltip',
                ],
                'required' => false,
            ]
        );

        $builder->add(
            'saml_idp_email_attribute',
            TextType::class,
            [
                'label'      => 'autoborna.user.config.form.saml.idp.attribute_email',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class' => 'form-control',
                ],
                'empty_data' => 'EmailAddress',
            ]
        );

        $builder->add(
            'saml_idp_username_attribute',
            TextType::class,
            [
                'label'      => 'autoborna.user.config.form.saml.idp.attribute_username',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class' => 'form-control',
                ],
                'required' => false,
            ]
        );

        $builder->add(
            'saml_idp_firstname_attribute',
            TextType::class,
            [
                'label'      => 'autoborna.user.config.form.saml.idp.attribute_firstname',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class' => 'form-control',
                ],
                'empty_data' => 'FirstName',
            ]
        );

        $builder->add(
            'saml_idp_lastname_attribute',
            TextType::class,
            [
                'label'      => 'autoborna.user.config.form.saml.idp.attribute_lastname',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class' => 'form-control',
                ],
                'empty_data' => 'LastName',
            ]
        );

        $builder->add(
            'saml_idp_default_role',
            RoleListType::class,
            [
                'label'      => 'autoborna.user.config.form.saml.idp.default_role',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'            => 'form-control',
                    'data-placeholder' => $this->translator->trans('autoborna.user.config.form.saml.idp.disable_creation'),
                    'tooltip'          => 'autoborna.user.config.form.saml.idp.default_role.tooltip',
                ],
                'required'    => false,
                'placeholder' => '',
            ]
        );
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['entityId'] = $this->parameters->get('autoborna.saml_idp_entity_id');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'userconfig';
    }
}
