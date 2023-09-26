<?php

namespace Autoborna\AssetBundle\Form\Type;

use Autoborna\AssetBundle\Entity\Asset;
use Autoborna\AssetBundle\Model\AssetModel;
use Autoborna\CategoryBundle\Form\Type\CategoryListType;
use Autoborna\CoreBundle\Form\EventListener\CleanFormSubscriber;
use Autoborna\CoreBundle\Form\EventListener\FormExitSubscriber;
use Autoborna\CoreBundle\Form\Type\ButtonGroupType;
use Autoborna\CoreBundle\Form\Type\FormButtonsType;
use Autoborna\CoreBundle\Form\Type\YesNoButtonGroupType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AssetType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var AssetModel
     */
    private $assetModel;

    public function __construct(TranslatorInterface $translator, AssetModel $assetModel)
    {
        $this->translator = $translator;
        $this->assetModel = $assetModel;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new CleanFormSubscriber(['description' => 'html']));
        $builder->addEventSubscriber(new FormExitSubscriber('asset.asset', $options));

        $builder->add('storageLocation', ButtonGroupType::class, [
            'label'   => 'autoborna.asset.asset.form.storageLocation',
            'choices' => [
                'autoborna.asset.asset.form.storageLocation.local'  => 'local',
                'autoborna.asset.asset.form.storageLocation.remote' => 'remote',
            ],
            'attr'              => [
                'onchange' => 'Autoborna.changeAssetStorageLocation();',
            ],
        ]);

        $maxUploadSize = $this->assetModel->getMaxUploadSize('', true);
        $builder->add(
            'tempName',
            HiddenType::class,
            [
                'label'      => $this->translator->trans('autoborna.asset.asset.form.file.upload', ['%max%' => $maxUploadSize]),
                'label_attr' => ['class' => 'control-label'],
                'required'   => false,
            ]
        );

        $builder->add(
            'originalFileName',
            HiddenType::class,
            [
                'required' => false,
            ]
        );
        $builder->add(
            'disallow',
            YesNoButtonGroupType::class,
            [
                'label' => 'autoborna.asset.asset.form.disallow.crawlers',
                'attr'  => [
                    'tooltip'      => 'autoborna.asset.asset.form.disallow.crawlers.descr',
                    'data-show-on' => '{"asset_storageLocation_0":"checked"}',
                ],
                'data'=> empty($options['data']->getDisallow()) ? false : true,
            ]
        );

        $builder->add(
            'remotePath',
            TextType::class,
            [
                'label'      => 'autoborna.asset.asset.form.remotePath',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => ['class' => 'form-control'],
                'required'   => false,
            ]
        );

        $builder->add(
            'title',
            TextType::class,
            [
                'label'      => 'autoborna.core.title',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => ['class' => 'form-control'],
            ]
        );

        $builder->add(
            'alias',
            TextType::class,
            [
                'label'      => 'autoborna.core.alias',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'   => 'form-control',
                    'tooltip' => 'autoborna.asset.asset.help.alias',
                ],
                'required' => false,
            ]
        );

        $builder->add(
            'description',
            TextareaType::class,
            [
                'label'      => 'autoborna.core.description',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => ['class' => 'form-control editor'],
                'required'   => false,
            ]
        );

        $builder->add(
            'category',
            CategoryListType::class,
            [
                'bundle' => 'asset',
            ]
        );

        $builder->add('language', LocaleType::class, [
            'label'      => 'autoborna.core.language',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => [
                'class'   => 'form-control',
                'tooltip' => 'autoborna.asset.asset.form.language.help',
            ],
            'required'    => true,
            'constraints' => [
                new NotBlank(
                    [
                        'message' => 'autoborna.core.value.required',
                    ]
                ),
            ],
        ]);

        $builder->add('isPublished', YesNoButtonGroupType::class);

        $builder->add('publishUp', DateTimeType::class, [
            'widget'     => 'single_text',
            'label'      => 'autoborna.core.form.publishup',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => [
                'class'       => 'form-control',
                'data-toggle' => 'datetime',
            ],
            'format'   => 'yyyy-MM-dd HH:mm',
            'required' => false,
        ]);

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
                ],
                'format'   => 'yyyy-MM-dd HH:mm',
                'required' => false,
            ]
        );

        $builder->add(
            'tempId',
            HiddenType::class,
            [
                'required' => false,
            ]
        );

        $builder->add('buttons', FormButtonsType::class, []);

        if (!empty($options['action'])) {
            $builder->setAction($options['action']);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Asset::class]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'asset';
    }
}
