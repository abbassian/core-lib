<?php

namespace Autoborna\LeadBundle\Form\Type;

use Autoborna\CoreBundle\Form\EventListener\CleanFormSubscriber;
use Autoborna\CoreBundle\Form\Type\FormButtonsType;
use Autoborna\CoreBundle\Helper\UserHelper;
use Autoborna\EmailBundle\Form\Type\EmailListType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class EmailType extends AbstractType
{
    /**
     * @var UserHelper
     */
    private $userHelper;

    public function __construct(UserHelper $userHelper)
    {
        $this->userHelper = $userHelper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new CleanFormSubscriber(['body' => 'html']));

        $builder->add(
            'subject',
            TextType::class,
            [
                'label'      => 'autoborna.email.subject',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => ['class' => 'form-control'],
                'required'   => false,
            ]
        );

        $user = $this->userHelper->getUser();

        $default = (empty($options['data']['fromname'])) ? $user->getFirstName().' '.$user->getLastName() : $options['data']['fromname'];
        $builder->add(
            'fromname',
            TextType::class,
             [
                'label'      => 'autoborna.lead.email.from_name',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => ['class' => 'form-control'],
                'required'   => false,
                'data'       => $default,
            ]
        );

        $default = (empty($options['data']['from'])) ? $user->getEmail() : $options['data']['from'];
        $builder->add(
            'from',
            TextType::class,
            [
                'label'       => 'autoborna.lead.email.from_email',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => ['class' => 'form-control'],
                'required'    => false,
                'data'        => $default,
                'constraints' => [
                    new NotBlank([
                        'message' => 'autoborna.core.email.required',
                    ]),
                    new Email([
                        'message' => 'autoborna.core.email.required',
                    ]),
                ],
            ]
        );

        $builder->add(
            'body',
            TextareaType::class,
            [
                'label'      => 'autoborna.email.form.body',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'                => 'form-control editor editor-basic-fullpage editor-builder-tokens editor-email',
                    'data-token-callback'  => 'email:getBuilderTokens',
                    'data-token-activator' => '{',
                ],
            ]
        );

        $builder->add('list', HiddenType::class);

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
                'multiple' => false,
            ]
        );

        $builder->add('buttons', FormButtonsType::class, [
            'apply_text'  => false,
            'save_text'   => 'autoborna.email.send',
            'save_class'  => 'btn btn-primary',
            'save_icon'   => 'fa fa-send',
            'cancel_icon' => 'fa fa-times',
        ]);

        if (!empty($options['action'])) {
            $builder->setAction($options['action']);
        }
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'lead_quickemail';
    }
}
