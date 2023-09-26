<?php

namespace Autoborna\EmailBundle\Form\Type;

use Autoborna\UserBundle\Form\Type\UserListType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class FormSubmitActionUserEmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('useremail',
            EmailSendType::class,
            [
                'label' => 'autoborna.email.emails',
                'attr'  => [
                    'class'   => 'form-control',
                    'tooltip' => 'autoborna.email.choose.emails_descr',
                ],
                'update_select' => 'formaction_properties_useremail_email',
            ]
        );

        $builder->add(
            'user_id',
            UserListType::class,
            [
                'label'      => 'autoborna.email.form.users',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'   => 'form-control',
                    'tooltip' => 'autoborna.core.help.autocomplete',
                ],
                'required'    => true,
                'constraints' => new NotBlank(
                    [
                        'message' => 'autoborna.core.value.required',
                    ]
                ),
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => false,
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'email_submitaction_useremail';
    }
}
