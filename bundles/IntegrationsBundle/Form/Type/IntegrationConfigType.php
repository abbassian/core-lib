<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Form\Type;

use Autoborna\CoreBundle\Form\Type\FormButtonsType;
use Autoborna\CoreBundle\Form\Type\YesNoButtonGroupType;
use Autoborna\IntegrationsBundle\Exception\IntegrationNotFoundException;
use Autoborna\IntegrationsBundle\Helper\ConfigIntegrationsHelper;
use Autoborna\IntegrationsBundle\Integration\Interfaces\ConfigFormAuthInterface;
use Autoborna\IntegrationsBundle\Integration\Interfaces\ConfigFormFeaturesInterface;
use Autoborna\PluginBundle\Entity\Integration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IntegrationConfigType extends AbstractType
{
    /**
     * @var ConfigIntegrationsHelper
     */
    private $integrationsHelper;

    /**
     * IntegrationConfigType constructor.
     */
    public function __construct(ConfigIntegrationsHelper $integrationsHelper)
    {
        $this->integrationsHelper = $integrationsHelper;
    }

    /**
     * @throws IntegrationNotFoundException
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $integrationObject = $this->integrationsHelper->getIntegration($options['integration']);

        // isPublished
        $builder->add(
            'isPublished',
            YesNoButtonGroupType::class,
            [
                'label'      => 'autoborna.integration.enabled',
                'label_attr' => ['class' => 'control-label'],
            ]
        );

        // apiKeys
        if ($integrationObject instanceof ConfigFormAuthInterface) {
            $builder->add(
                'apiKeys',
                $integrationObject->getAuthConfigFormName(),
                [
                    'label'       => false,
                    'integration' => $integrationObject,
                ]
            );
        }

        // supportedFeatures
        if ($integrationObject instanceof ConfigFormFeaturesInterface) {
            // @todo add tooltip support
            $builder->add(
                'supportedFeatures',
                ChoiceType::class,
                [
                    'label'      => 'autoborna.integration.features',
                    'label_attr' => ['class' => 'control-label'],
                    'choices'    => array_flip($integrationObject->getSupportedFeatures()),
                    'expanded'   => true,
                    'multiple'   => true,
                    'required'   => false,
                ]
            );
        }

        // featureSettings
        $builder->add(
            'featureSettings',
            IntegrationFeatureSettingsType::class,
            [
                'label'             => false,
                'integrationObject' => $integrationObject,
            ]
        );

        $builder->add('buttons', FormButtonsType::class);

        $builder->setAction($options['action']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(
            [
                'integration',
            ]
        );

        $resolver->setDefined(
            [
                'data_class'  => Integration::class,
            ]
        );
    }
}
