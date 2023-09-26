<?php

namespace Autoborna\CoreBundle\Form\Type;

use Autoborna\IntegrationsBundle\Exception\IntegrationNotFoundException;
use Autoborna\IntegrationsBundle\Helper\BuilderIntegrationsHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DynamicContentFilterType extends AbstractType
{
    private BuilderIntegrationsHelper $builderIntegrationsHelper;

    public function __construct(BuilderIntegrationsHelper $builderIntegrationsHelper)
    {
        $this->builderIntegrationsHelper = $builderIntegrationsHelper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $extraClasses = '';

        try {
            $autobornaBuilder = $this->builderIntegrationsHelper->getBuilder('email');
            $autobornaBuilder->getName();
        } catch (IntegrationNotFoundException $exception) {
            // Assume legacy builder
            $extraClasses = ' legacy-builder';
        }

        $builder->add(
            'tokenName',
            TextType::class,
            [
                'label' => 'autoborna.core.dynamicContent.token_name',
                'attr'  => [
                    'class' => 'form-control dynamic-content-token-name',
                ],
            ]
        );

        $builder->add(
            'content',
            TextareaType::class,
            [
                'label' => 'autoborna.core.dynamicContent.default_content',
                'attr'  => [
                    'class' => 'form-control editor editor-dynamic-content'.$extraClasses,
                ],
            ]
        );

        $builder->add(
            $builder->create(
                'filters',
                DynamicListType::class,
                [
                    'entry_type'     => DynamicContentFilterEntryType::class,
                    'entry_options'  => [
                        'label' => false,
                        'attr'  => [
                            'class' => 'form-control',
                        ],
                    ],
                    'option_required' => false,
                    'allow_add'       => true,
                    'allow_delete'    => true,
                ]
            )
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'label'          => false,
                'error_bubbling' => false,
            ]
        );
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'dynamic_content_filter';
    }
}
