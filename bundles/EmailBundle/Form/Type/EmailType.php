<?php

namespace Autoborna\EmailBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Autoborna\AssetBundle\Form\Type\AssetListType;
use Autoborna\CategoryBundle\Form\Type\CategoryListType;
use Autoborna\CoreBundle\Form\DataTransformer\EmojiToShortTransformer;
use Autoborna\CoreBundle\Form\DataTransformer\IdToEntityModelTransformer;
use Autoborna\CoreBundle\Form\EventListener\CleanFormSubscriber;
use Autoborna\CoreBundle\Form\EventListener\FormExitSubscriber;
use Autoborna\CoreBundle\Form\Type\DynamicContentTrait;
use Autoborna\CoreBundle\Form\Type\FormButtonsType;
use Autoborna\CoreBundle\Form\Type\SortableListType;
use Autoborna\CoreBundle\Form\Type\ThemeListType;
use Autoborna\CoreBundle\Form\Type\YesNoButtonGroupType;
use Autoborna\CoreBundle\Helper\CoreParametersHelper;
use Autoborna\CoreBundle\Helper\ThemeHelperInterface;
use Autoborna\EmailBundle\Entity\Email;
use Autoborna\FormBundle\Form\Type\FormListType;
use Autoborna\LeadBundle\Form\Type\LeadListType;
use Autoborna\LeadBundle\Helper\FormFieldHelper;
use Autoborna\PageBundle\Form\Type\PreferenceCenterListType;
use Autoborna\StageBundle\Model\StageModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

class EmailType extends AbstractType
{
    use DynamicContentTrait;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var StageModel
     */
    private $stageModel;

    private CoreParametersHelper $coreParametersHelper;

    /**
     * @var ThemeHelperInterface
     */
    private $themeHelper;

    public function __construct(
        TranslatorInterface $translator,
        EntityManager $entityManager,
        StageModel $stageModel,
        CoreParametersHelper $coreParametersHelper,
        ThemeHelperInterface $themeHelper
    ) {
        $this->translator           = $translator;
        $this->em                   = $entityManager;
        $this->stageModel           = $stageModel;
        $this->coreParametersHelper = $coreParametersHelper;
        $this->themeHelper          = $themeHelper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new CleanFormSubscriber(['content' => 'html', 'customHtml' => 'html', 'headers' => 'clean']));
        $builder->addEventSubscriber(new FormExitSubscriber('email.email', $options));

        $builder->add(
            'name',
            TextType::class,
            [
                'label'      => 'autoborna.email.form.internal.name',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => ['class' => 'form-control'],
            ]
        );

        $emojiTransformer = new EmojiToShortTransformer();
        $builder->add(
            $builder->create(
                'subject',
                TextType::class,
                [
                    'label'      => 'autoborna.email.subject',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => ['class' => 'form-control'],
                ]
            )->addModelTransformer($emojiTransformer)
        );

        $builder->add(
            'fromName',
            TextType::class,
            [
                'label'      => 'autoborna.email.from_name',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'    => 'form-control',
                    'preaddon' => 'fa fa-user',
                    'tooltip'  => 'autoborna.email.from_name.tooltip',
                ],
                'required' => false,
            ]
        );

        $builder->add(
            'fromAddress',
            TextType::class,
            [
                'label'      => 'autoborna.email.from_email',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'    => 'form-control',
                    'preaddon' => 'fa fa-envelope',
                    'tooltip'  => 'autoborna.email.from_email.tooltip',
                ],
                'required' => false,
            ]
        );

        $builder->add(
            'replyToAddress',
            TextType::class,
            [
                'label'      => 'autoborna.email.reply_to_email',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'    => 'form-control',
                    'preaddon' => 'fa fa-envelope',
                    'tooltip'  => 'autoborna.email.reply_to_email.tooltip',
                ],
                'required' => false,
            ]
        );

        $builder->add(
            'bccAddress',
            TextType::class,
            [
                'label'      => 'autoborna.email.bcc',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'    => 'form-control',
                    'preaddon' => 'fa fa-envelope',
                    'tooltip'  => 'autoborna.email.bcc.tooltip',
                ],
                'required' => false,
            ]
        );

        $builder->add(
            'useOwnerAsMailer',
            YesNoButtonGroupType::class,
            [
                'label'      => 'autoborna.email.use.owner.as.mailer',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'   => 'form-control',
                    'tooltip' => 'autoborna.email.use.owner.as.mailer.tooltip',
                ],
                'data'     => (bool) (is_null($options['data']->getUseOwnerAsMailer()) ? $this->coreParametersHelper->get('mailer_is_owner') : $options['data']->getUseOwnerAsMailer()),
                'required' => false,
            ]
        );

        $builder->add(
            'utmTags',
            EmailUtmTagsType::class,
            [
                'label'      => 'autoborna.email.utm_tags',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'   => 'form-control',
                    'tooltip' => 'autoborna.email.utm_tags.tooltip',
                ],
                'required' => false,
            ]
        );

        $builder->add(
            'headers',
            SortableListType::class,
            [
                'required'        => false,
                'label'           => 'autoborna.email.custom_headers',
                'attr'            => [
                    'tooltip' => 'autoborna.email.custom_headers.tooltip',
                ],
                'option_required' => false,
                'with_labels'     => true,
                'key_value_pairs' => true, // do not store under a `list` key and use label as the key
            ]
        );

        $template = $options['data']->getTemplate() ?? 'blank';
        // If theme does not exist, set empty
        $template = $this->themeHelper->getCurrentTheme($template, 'email');

        $builder->add(
            'template',
            ThemeListType::class,
            [
                'feature' => 'email',
                'attr'    => [
                    'class'   => 'form-control not-chosen hidden',
                    'tooltip' => 'autoborna.email.form.template.help',
                ],
                'data' => $template,
            ]
        );

        $builder->add('isPublished', YesNoButtonGroupType::class);

        $builder->add(
            'publishUp',
            DateTimeType::class,
            [
                'widget'     => 'single_text',
                'label'      => 'autoborna.core.form.publishup',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                    'data-toggle' => 'datetime',
                ],
                'format'   => 'yyyy-MM-dd HH:mm',
                'required' => false,
            ]
        );

        $builder->add(
            'publishDown',
            DateTimeType::class,
            [
                'widget'     => 'single_text',
                'label'      => 'autoborna.core.form.publishdown',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                    'data-toggle' => 'datetime',
                    'tooltip'     => 'autoborna.email.form.publishdown.help',
                ],
                'format'   => 'yyyy-MM-dd HH:mm',
                'required' => false,
            ]
        );

        $builder->add(
            'plainText',
            TextareaType::class,
            [
                'label'      => 'autoborna.email.form.plaintext',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'tooltip'              => 'autoborna.email.form.plaintext.help',
                    'class'                => 'form-control',
                    'rows'                 => '15',
                    'data-token-callback'  => 'email:getBuilderTokens',
                    'data-token-activator' => '{',
                    'data-token-visual'    => 'false',
                ],
                'required' => false,
            ]
        );

        $builder->add(
            $builder->create(
                'customHtml',
                TextareaType::class,
                [
                    'label'      => 'autoborna.email.form.body',
                    'label_attr' => ['class' => 'control-label'],
                    'required'   => false,
                    'attr'       => [
                        'tooltip'              => 'autoborna.email.form.body.help',
                        'class'                => 'form-control editor-builder-tokens builder-html editor-email',
                        'data-token-callback'  => 'email:getBuilderTokens',
                        'data-token-activator' => '{',
                        'rows'                 => '15',
                    ],
                ]
            )->addModelTransformer($emojiTransformer)
        );

        $transformer = new IdToEntityModelTransformer($this->em, 'AutobornaFormBundle:Form', 'id');
        $builder->add(
            $builder->create(
                'unsubscribeForm',
                FormListType::class,
                [
                    'label'      => 'autoborna.email.form.unsubscribeform',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class'            => 'form-control',
                        'tooltip'          => 'autoborna.email.form.unsubscribeform.tooltip',
                        'data-placeholder' => $this->translator->trans('autoborna.core.form.chooseone'),
                    ],
                    'required'    => false,
                    'multiple'    => false,
                    'placeholder' => '',
                ]
            )
                ->addModelTransformer($transformer)
        );

        $transformer = new IdToEntityModelTransformer($this->em, 'AutobornaPageBundle:Page', 'id');
        $builder->add(
            $builder->create(
                'preferenceCenter',
                PreferenceCenterListType::class,
                [
                    'label'      => 'autoborna.email.form.preference_center',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class'            => 'form-control',
                        'tooltip'          => 'autoborna.email.form.preference_center.tooltip',
                        'data-placeholder' => $this->translator->trans('autoborna.core.form.chooseone'),
                    ],
                    'required'    => false,
                    'multiple'    => false,
                    'placeholder' => '',
                ]
            )
                ->addModelTransformer($transformer)
        );

        $transformer = new IdToEntityModelTransformer($this->em, 'AutobornaEmailBundle:Email');
        $builder->add(
            $builder->create(
                'variantParent',
                HiddenType::class
            )->addModelTransformer($transformer)
        );

        $builder->add(
            $builder->create(
                'translationParent',
                HiddenType::class
            )->addModelTransformer($transformer)
        );

        $variantParent     = $options['data']->getVariantParent();
        $translationParent = $options['data']->getTranslationParent();
        $builder->add(
            'segmentTranslationParent',
            EmailListType::class,
            [
                'label'      => 'autoborna.core.form.translation_parent',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'   => 'form-control',
                    'tooltip' => 'autoborna.core.form.translation_parent.help',
                ],
                'required'       => false,
                'multiple'       => false,
                'email_type'     => 'list',
                'placeholder'    => 'autoborna.core.form.translation_parent.empty',
                'top_level'      => 'translation',
                'variant_parent' => ($variantParent) ? $variantParent->getId() : null,
                'ignore_ids'     => [(int) $options['data']->getId()],
                'mapped'         => false,
                'data'           => ($translationParent) ? $translationParent->getId() : null,
            ]
        );

        $builder->add(
            'templateTranslationParent',
            EmailListType::class,
            [
                'label'      => 'autoborna.core.form.translation_parent',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'   => 'form-control',
                    'tooltip' => 'autoborna.core.form.translation_parent.help',
                ],
                'required'       => false,
                'multiple'       => false,
                'placeholder'    => 'autoborna.core.form.translation_parent.empty',
                'top_level'      => 'translation',
                'variant_parent' => ($variantParent) ? $variantParent->getId() : null,
                'email_type'     => 'template',
                'ignore_ids'     => [(int) $options['data']->getId()],
                'mapped'         => false,
                'data'           => ($translationParent) ? $translationParent->getId() : null,
            ]
        );

        $variantSettingsModifier = function (FormEvent $event, $isVariant) {
            if ($isVariant) {
                $event->getForm()->add(
                    'variantSettings',
                    VariantType::class,
                    [
                        'label' => false,
                    ]
                );
            }
        };

        // Building the form
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($variantSettingsModifier) {
                $variantSettingsModifier(
                    $event,
                    $event->getData()->getVariantParent()
                );
            }
        );

        // After submit
        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($variantSettingsModifier) {
                $data = $event->getData();
                $variantSettingsModifier(
                    $event,
                    !empty($data['variantParent'])
                );

                if (isset($data['emailType']) && 'list' == $data['emailType']) {
                    $data['translationParent'] = isset($data['segmentTranslationParent']) ? $data['segmentTranslationParent'] : null;
                } else {
                    $data['translationParent'] = isset($data['templateTranslationParent']) ? $data['templateTranslationParent'] : null;
                }

                $event->setData($data);
            }
        );

        $builder->add(
            'category',
            CategoryListType::class,
            [
                'bundle' => 'email',
            ]
        );

        $transformer = new IdToEntityModelTransformer($this->em, 'AutobornaLeadBundle:LeadList', 'id', true);
        $builder->add(
            $builder->create(
                'lists',
                LeadListType::class,
                [
                    'label'      => 'autoborna.email.form.list',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class'        => 'form-control',
                        'data-show-on' => '{"emailform_segmentTranslationParent":[""]}',
                    ],
                    'multiple' => true,
                    'expanded' => false,
                    'required' => true,
                ]
            )
                ->addModelTransformer($transformer)
        );

        $builder->add(
            'language',
            LocaleType::class,
            [
                'label'      => 'autoborna.core.language',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class' => 'form-control',
                ],
                'required' => true,
            ]
        );

        $transformer = new IdToEntityModelTransformer(
            $this->em,
            'AutobornaAssetBundle:Asset',
            'id',
            true
        );
        $builder->add(
            $builder->create(
                'assetAttachments',
                AssetListType::class,
                [
                    'label'      => 'autoborna.email.attachments',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class'    => 'form-control',
                        'onchange' => 'Autoborna.getTotalAttachmentSize();',
                        'tooltip'  => 'autoborna.email.attachments.help',
                    ],
                    'multiple' => true,
                    'expanded' => false,
                ]
            )
                ->addModelTransformer($transformer)
        );

        $builder->add('sessionId', HiddenType::class);
        $builder->add('emailType', HiddenType::class);
        $builder->add(
            'buttons',
            FormButtonsType::class,
            [
                'pre_extra_buttons' => [
                    [
                        'name'  => 'builder',
                        'label' => 'autoborna.core.builder',
                        'attr'  => [
                            'class'   => 'btn btn-default btn-dnd btn-nospin text-primary btn-builder',
                            'icon'    => 'fa fa-cube',
                            'onclick' => "Autoborna.launchBuilder('{$this->getBlockPrefix()}', 'email');",
                        ],
                    ],
                ],
            ]
        );

        if (!empty($options['update_select'])) {
            $builder->add(
                'updateSelect',
                HiddenType::class,
                [
                    'data'   => $options['update_select'],
                    'mapped' => false,
                ]
            );
        }

        $this->addDynamicContentField($builder);

        if (!empty($options['action'])) {
            $builder->setAction($options['action']);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Email::class,
            ]
        );

        $resolver->setDefined(['update_select']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $stages       = $this->stageModel->getRepository()->getSimpleList();
        $stageChoices = [];

        foreach ($stages as $stage) {
            $stageChoices[$stage['value']] = $stage['label'];
        }

        $view->vars['countries'] = FormFieldHelper::getCountryChoices();
        $view->vars['regions']   = FormFieldHelper::getRegionChoices();
        $view->vars['timezones'] = FormFieldHelper::getTimezonesChoices();
        $view->vars['locales']   = FormFieldHelper::getLocaleChoices();
        $view->vars['stages']    = $stageChoices;
    }

    public function getBlockPrefix()
    {
        return 'emailform';
    }
}
