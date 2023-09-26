<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Form\Type;

use Autoborna\IntegrationsBundle\Exception\IntegrationNotFoundException;
use Autoborna\IntegrationsBundle\Integration\Interfaces\ConfigFormFeatureSettingsInterface;
use Autoborna\IntegrationsBundle\Integration\Interfaces\ConfigFormSyncInterface;
use Autoborna\IntegrationsBundle\Integration\Interfaces\IntegrationInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IntegrationFeatureSettingsType extends AbstractType
{
    /**
     * @throws IntegrationNotFoundException
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $integrationObject = $options['integrationObject'];
        if (!$integrationObject instanceof IntegrationInterface) {
            throw new IntegrationNotFoundException("{$options['integrationObject']} is not recognized");
        }

        if ($integrationObject instanceof ConfigFormFeatureSettingsInterface) {
            $builder->add(
                'integration',
                $integrationObject->getFeatureSettingsConfigFormName(),
                [
                    'label' => false,
                ]
            );
        }

        if ($integrationObject instanceof ConfigFormSyncInterface) {
            $builder->add(
                'sync',
                IntegrationSyncSettingsType::class,
                [
                    'label'             => false,
                    'integrationObject' => $integrationObject,
                ]
            );
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(
            [
                'integrationObject',
            ]
        );
    }
}
