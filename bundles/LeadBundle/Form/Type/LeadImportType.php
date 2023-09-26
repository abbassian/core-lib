<?php

namespace Autoborna\LeadBundle\Form\Type;

use Autoborna\CoreBundle\Form\Validator\Constraints\FileEncoding as EncodingValidation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class LeadImportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'file',
            FileType::class,
            [
                'label' => 'autoborna.lead.import.file',
                'attr'  => [
                    'accept' => '.csv',
                    'class'  => 'form-control',
                ],
                'constraints' => [
                    new File(
                        [
                            'mimeTypes'        => ['text/*', 'application/octet-stream', 'application/csv'],
                            'mimeTypesMessage' => 'autoborna.core.invalid_file_type',
                        ]
                    ),
                    new EncodingValidation(
                        [
                            'encodingFormat'        => ['UTF-8'],
                            'encodingFormatMessage' => 'autoborna.core.invalid_file_encoding',
                        ]
                    ),
                    new NotBlank(
                        ['message' => 'autoborna.import.file.required']
                    ),
                ],
                'error_bubbling' => true,
            ]
        );

        $constraints = [
            new NotBlank(
                ['message' => 'autoborna.core.value.required']
            ),
        ];

        $default = (empty($options['data']['delimiter'])) ? ',' : htmlspecialchars($options['data']['delimiter']);
        $builder->add(
            'delimiter',
            TextType::class,
            [
                'label' => 'autoborna.lead.import.delimiter',
                'attr'  => [
                    'class' => 'form-control',
                ],
                'data'        => $default,
                'constraints' => $constraints,
            ]
        );

        $default = (empty($options['data']['enclosure'])) ? '&quot;' : htmlspecialchars($options['data']['enclosure']);
        $builder->add(
            'enclosure',
            TextType::class,
            [
                'label' => 'autoborna.lead.import.enclosure',
                'attr'  => [
                    'class' => 'form-control',
                ],
                'data'        => $default,
                'constraints' => $constraints,
            ]
        );

        $default = (empty($options['data']['escape'])) ? '\\' : $options['data']['escape'];
        $builder->add(
            'escape',
            TextType::class,
            [
                'label' => 'autoborna.lead.import.escape',
                'attr'  => [
                    'class' => 'form-control',
                ],
                'data'        => $default,
                'constraints' => $constraints,
            ]
        );

        $default = (empty($options['data']['batchlimit'])) ? 100 : (int) $options['data']['batchlimit'];
        $builder->add(
            'batchlimit',
            TextType::class,
            [
                'label' => 'autoborna.lead.import.batchlimit',
                'attr'  => [
                    'class'   => 'form-control',
                    'tooltip' => 'autoborna.lead.import.batchlimit_tooltip',
                ],
                'data'        => $default,
                'constraints' => $constraints,
            ]
        );

        $builder->add(
            'start',
            SubmitType::class,
            [
                'attr' => [
                    'class'   => 'btn btn-primary',
                    'icon'    => 'fa fa-upload',
                    'onclick' => "mQuery(this).prop('disabled', true); mQuery('form[name=\'lead_import\']').submit();",
                ],
                'label' => 'autoborna.lead.import.upload',
            ]
        );

        if (!empty($options['action'])) {
            $builder->setAction($options['action']);
        }
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'lead_import';
    }
}
