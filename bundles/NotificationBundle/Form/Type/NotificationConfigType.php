<?php

declare(strict_types=1);

namespace Autoborna\NotificationBundle\Form\Type;

use Autoborna\CoreBundle\Form\Type\YesNoButtonGroupType;
use Autoborna\EmailBundle\Validator\MultipleEmailsValid;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class NotificationConfigType extends AbstractType
{
    /**
     * @param FormBuilderInterface<int,FormBuilderInterface> $builder
     * @param array<string,mixed>                            $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'campaign_send_notification_to_author',
            YesNoButtonGroupType::class,
            [
                'label' => 'autoborna.notification.form.config.send_notification_to_author',
                'attr'  => [
                    'class'    => 'form-control',
                    'tooltip'  => 'autoborna.notification.form.config.send_notification_to_author.tooltip',
                    'onchange' => 'Autoborna.resetEmailsToNotification(this)',
                ],
                'required' => true,
            ]
        );
        $builder->add(
            'campaign_notification_email_addresses',
            TextType::class,
            [
                'label'      => 'autoborna.notification.form.config.notification_email_addresses',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'        => 'form-control notification_email_addresses',
                    'tooltip'      => 'autoborna.notification.form.config.notification_email_addresses.tooltip',
                    'data-show-on' => '{"config_notification_config_campaign_send_notification_to_author_0":"checked"}',
                ],
                'constraints' => [
                    new NotBlank(['groups' => ['campaign_email_list']]),
                    new MultipleEmailsValid(),
                ],
            ]
        );

        $builder->add(
            'webhook_send_notification_to_author',
            YesNoButtonGroupType::class,
            [
                'label' => 'autoborna.notification.form.config.send_notification_to_author',
                'attr'  => [
                    'class'    => 'form-control',
                    'tooltip'  => 'autoborna.notification.form.config.send_notification_to_author.tooltip',
                    'onchange' => 'Autoborna.resetEmailsToNotification(this)',
                ],
                'required' => true,
            ]
        );
        $builder->add(
            'webhook_notification_email_addresses',
            TextType::class,
            [
                'label'      => 'autoborna.notification.form.config.notification_email_addresses',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'        => 'form-control notification_email_addresses',
                    'tooltip'      => 'autoborna.notification.form.config.notification_email_addresses.tooltip',
                    'data-show-on' => '{"config_notification_config_webhook_send_notification_to_author_0":"checked"}',
                ],
                'constraints' => [
                    new MultipleEmailsValid(),
                    new NotBlank(['groups' => ['webhook_email_list']]),
                ],
            ]
        );
    }

    /**
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'validation_groups' => function (FormInterface $form) {
                $data = $form->getData();
                $groups = ['Default'];
                if (!$data['webhook_send_notification_to_author']) {
                    $groups[] = 'webhook_email_list';
                }
                if (!$data['campaign_send_notification_to_author']) {
                    $groups[] = 'campaign_email_list';
                }

                return $groups;
            },
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'notification_config';
    }
}
