<?php

namespace Autoborna\FormBundle\Form\Type;

use Autoborna\CoreBundle\Form\DataTransformer\ArrayStringTransformer;
use Autoborna\CoreBundle\Form\Type\YesNoButtonGroupType;
use Autoborna\CoreBundle\Helper\CoreParametersHelper;
use Autoborna\CoreBundle\Helper\FileHelper;
use Autoborna\FormBundle\Validator\Constraint\FileExtensionConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

/**
 * Class FormFieldFileType.
 */
class FormFieldFileType extends AbstractType
{
    const PROPERTY_ALLOWED_FILE_EXTENSIONS = 'allowed_file_extensions';
    const PROPERTY_ALLOWED_FILE_SIZE       = 'allowed_file_size';
    const PROPERTY_PREFERED_PROFILE_IMAGE  = 'profile_image';

    /** @var CoreParametersHelper */
    private $coreParametersHelper;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(CoreParametersHelper $coreParametersHelper, TranslatorInterface $translator)
    {
        $this->coreParametersHelper = $coreParametersHelper;
        $this->translator           = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (empty($options['data'][self::PROPERTY_ALLOWED_FILE_EXTENSIONS])) {
            $options['data'][self::PROPERTY_ALLOWED_FILE_EXTENSIONS] = $this->coreParametersHelper->get('allowed_extensions');
        }
        if (empty($options['data'][self::PROPERTY_ALLOWED_FILE_SIZE])) {
            $options['data'][self::PROPERTY_ALLOWED_FILE_SIZE] = $this->coreParametersHelper->get('max_size');
        }

        $arrayStringTransformer = new ArrayStringTransformer();
        $builder->add(
            $builder->create(
                self::PROPERTY_ALLOWED_FILE_EXTENSIONS,
                TextareaType::class,
                [
                    'label'      => 'autoborna.form.field.file.allowed_extensions',
                    'label_attr' => ['class' => 'control-label'],
                    'required'   => false,
                    'attr'       => [
                        'class'   => 'form-control',
                        'tooltip' => 'autoborna.form.field.file.tooltip.allowed_extensions',
                    ],
                    'data'        => $options['data'][self::PROPERTY_ALLOWED_FILE_EXTENSIONS],
                    'constraints' => [new FileExtensionConstraint()],
                ]
            )->addViewTransformer($arrayStringTransformer)
        );

        $maxUploadSize = FileHelper::getMaxUploadSizeInMegabytes();
        $builder->add(
            self::PROPERTY_ALLOWED_FILE_SIZE,
            TextType::class,
            [
                'label'      => 'autoborna.form.field.file.allowed_size',
                'label_attr' => ['class' => 'control-label'],
                'required'   => false,
                'attr'       => [
                    'class'   => 'form-control',
                    'tooltip' => $this->translator->trans('autoborna.form.field.file.tooltip.allowed_size', ['%uploadSize%' => $maxUploadSize]),
                ],
                'data'        => $options['data'][self::PROPERTY_ALLOWED_FILE_SIZE],
                'constraints' => [new LessThanOrEqual(['value' => $maxUploadSize])],
            ]
        );

        $builder->add(
            'public',
            YesNoButtonGroupType::class,
            [
                'label' => 'autoborna.form.field.file.public',
            ]
        );

        $builder->add(
            self::PROPERTY_PREFERED_PROFILE_IMAGE,
            YesNoButtonGroupType::class,
            [
                'label'       => 'autoborna.form.field.file.set_as_profile_image',
                'data'        => isset($options['data'][self::PROPERTY_PREFERED_PROFILE_IMAGE]) ? $options['data'][self::PROPERTY_PREFERED_PROFILE_IMAGE] : false,
            ]
        );
    }
}
