<?php

namespace Autoborna\FormBundle\Form\Type;

use Autoborna\CoreBundle\Form\ToBcBccFieldsTrait;
use Autoborna\CoreBundle\Form\Type\YesNoButtonGroupType;
use Autoborna\CoreBundle\Helper\CoreParametersHelper;
use Autoborna\EmailBundle\Form\Type\EmailListType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class SubmitActionEmailType.
 */
class SubmitActionEmailType extends AbstractType
{
    use FormFieldTrait;
    use ToBcBccFieldsTrait;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var CoreParametersHelper
     */
    protected $coreParametersHelper;

    /**
     * SubmitActionEmailType constructor.
     */
    public function __construct(TranslatorInterface $translator, CoreParametersHelper $coreParametersHelper)
    {
        $this->translator           = $translator;
        $this->coreParametersHelper = $coreParametersHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = (isset($options['data']['subject']))
            ? $options['data']['subject']
            : $this->translator->trans(
                'autoborna.form.action.sendemail.subject.default'
            );
        $builder->add(
            'subject',
            TextType::class,
            [
                'label'      => 'autoborna.form.action.sendemail.subject',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => ['class' => 'form-control'],
                'required'   => false,
                'data'       => $data,
            ]
        );

        if (!isset($options['data']['message'])) {
            $fields  = $this->getFormFields($options['attr']['data-formid']);
            $message = '';

            foreach ($fields as $token => $label) {
                $message .= "<strong>$label</strong>: $token<br />";
            }
        } else {
            $message = $options['data']['message'];
        }

        $builder->add(
            'message',
            TextareaType::class,
            [
                'label'      => 'autoborna.form.action.sendemail.message',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => ['class' => 'form-control editor editor-basic'],
                'required'   => false,
                'data'       => $message,
            ]
        );

        if ('file' == $this->coreParametersHelper->get('mailer_spool_type')) {
            $default = isset($options['data']['immediately']) ? $options['data']['immediately'] : false;
            $builder->add(
                'immediately',
                YesNoButtonGroupType::class,
                [
                    'label' => 'autoborna.form.action.sendemail.immediately',
                    'data'  => $default,
                    'attr'  => [
                        'tooltip' => 'autoborna.form.action.sendemail.immediately.desc',
                    ],
                ]
            );
        } else {
            $builder->add(
                'immediately',
                HiddenType::class,
                [
                    'data' => false,
                ]
            );
        }

        $default = isset($options['data']['copy_lead']) ? $options['data']['copy_lead'] : false;
        $builder->add(
            'copy_lead',
            YesNoButtonGroupType::class,
            [
                'label' => 'autoborna.form.action.sendemail.copytolead',
                'data'  => $default,
            ]
        );

        $default = isset($options['data']['set_replyto']) ? $options['data']['set_replyto'] : true;
        $builder->add(
            'set_replyto',
            YesNoButtonGroupType::class,
            [
                'label' => 'autoborna.form.action.sendemail.setreplyto',
                'data'  => $default,
                'attr'  => [
                    'tooltip' => 'autoborna.form.action.sendemail.setreplyto_tooltip',
                ],
            ]
        );

        $default = isset($options['data']['email_to_owner']) ? $options['data']['email_to_owner'] : false;
        $builder->add(
            'email_to_owner',
            YesNoButtonGroupType::class,
            [
                'label' => 'autoborna.form.action.sendemail.emailtoowner',
                'data'  => $default,
            ]
        );

        $builder->add(
            'templates',
            EmailListType::class,
            [
                'label'      => 'autoborna.lead.email.template',
                'label_attr' => ['class' => 'control-label'],
                'required'   => false,
                'attr'       => [
                    'class'    => 'form-control',
                    'onchange' => 'Autoborna.getLeadEmailContent(this)',
                ],
                'multiple'   => false,
            ]
        );

        $this->addToBcBccFields($builder);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'form_submitaction_sendemail';
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['formFields'] = $this->getFormFields($options['attr']['data-formid']);
    }
}
