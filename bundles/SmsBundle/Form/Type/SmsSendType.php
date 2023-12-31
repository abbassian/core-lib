<?php

namespace Autoborna\SmsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class SmsSendType.
 */
class SmsSendType extends AbstractType
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
            'sms',
            SmsListType::class,
            [
                'label'      => 'autoborna.sms.send.selectsmss',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'    => 'form-control',
                    'tooltip'  => 'autoborna.sms.choose.smss',
                    'onchange' => 'Autoborna.disabledSmsAction()',
                ],
                'multiple'    => false,
                'required'    => true,
                'constraints' => [
                    new NotBlank(
                        ['message' => 'autoborna.sms.choosesms.notblank']
                    ),
                ],
            ]
        );

        if (!empty($options['update_select'])) {
            $windowUrl = $this->router->generate(
                'autoborna_sms_action',
                [
                    'objectAction' => 'new',
                    'contentOnly'  => 1,
                    'updateSelect' => $options['update_select'],
                ]
            );

            $builder->add(
                'newSmsButton',
                ButtonType::class,
                [
                    'attr' => [
                        'class'   => 'btn btn-primary btn-nospin',
                        'onclick' => 'Autoborna.loadNewWindow({
                        "windowUrl": "'.$windowUrl.'"
                    })',
                        'icon' => 'fa fa-plus',
                    ],
                    'label' => 'autoborna.sms.send.new.sms',
                ]
            );

            $sms = $options['data']['sms'];

            // create button edit sms
            $windowUrlEdit = $this->router->generate(
                'autoborna_sms_action',
                [
                    'objectAction' => 'edit',
                    'objectId'     => 'smsId',
                    'contentOnly'  => 1,
                    'updateSelect' => $options['update_select'],
                ]
            );

            $builder->add(
                'editSmsButton',
                ButtonType::class,
                [
                    'attr' => [
                        'class'    => 'btn btn-primary btn-nospin',
                        'onclick'  => 'Autoborna.loadNewWindow(Autoborna.standardSmsUrl({"windowUrl": "'.$windowUrlEdit.'"}))',
                        'disabled' => !isset($sms),
                        'icon'     => 'fa fa-edit',
                    ],
                    'label' => 'autoborna.sms.send.edit.sms',
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
        return 'smssend_list';
    }
}
