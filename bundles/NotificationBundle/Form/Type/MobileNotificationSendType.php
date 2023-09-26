<?php

namespace Autoborna\NotificationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class NotificationSendType.
 */
class MobileNotificationSendType extends AbstractType
{
    /**
     * @var RouterInterface
     */
    protected $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'notification',
            MobileNotificationListType::class,
            [
                'label'      => 'autoborna.notification.send.selectnotifications',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'    => 'form-control',
                    'tooltip'  => 'autoborna.notification.choose.notifications',
                    'onchange' => 'Autoborna.disabledNotificationAction()',
                ],
                'multiple'    => false,
                'constraints' => [
                    new NotBlank(
                        ['message' => 'autoborna.notification.choosenotification.notblank']
                    ),
                ],
            ]
        );

        if (!empty($options['update_select'])) {
            $windowUrl = $this->router->generate('autoborna_mobile_notification_action', [
                'objectAction' => 'new',
                'contentOnly'  => 1,
                'updateSelect' => $options['update_select'],
            ]);

            $builder->add(
                'newNotificationButton',
                ButtonType::class,
                [
                    'attr' => [
                        'class'   => 'btn btn-primary btn-nospin',
                        'onclick' => 'Autoborna.loadNewWindow({
                            "windowUrl": "'.$windowUrl.'"
                        })',
                        'icon' => 'fa fa-plus',
                    ],
                    'label' => 'autoborna.notification.send.new.notification',
                ]
            );

            $notification = $options['data']['notification'];

            // create button edit notification
            $windowUrlEdit = $this->router->generate('autoborna_mobile_notification_action', [
                'objectAction' => 'edit',
                'objectId'     => 'notificationId',
                'contentOnly'  => 1,
                'updateSelect' => $options['update_select'],
            ]);

            $builder->add(
                'editNotificationButton',
                ButtonType::class,
                [
                    'attr' => [
                        'class'    => 'btn btn-primary btn-nospin',
                        'onclick'  => 'Autoborna.loadNewWindow(Autoborna.standardNotificationUrl({"windowUrl": "'.$windowUrlEdit.'"}))',
                        'disabled' => !isset($notification),
                        'icon'     => 'fa fa-edit',
                    ],
                    'label' => 'autoborna.notification.send.edit.notification',
                ]
            );
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined(['update_select']);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'mobilenotificationsend_list';
    }
}
