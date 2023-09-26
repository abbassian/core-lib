<?php

namespace Autoborna\NotificationBundle\Integration;

use Autoborna\PluginBundle\Integration\AbstractIntegration;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilder;

/**
 * Class OneSignalIntegration.
 */
class OneSignalIntegration extends AbstractIntegration
{
    protected bool $coreIntegration = true;

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName()
    {
        return 'OneSignal';
    }

    public function getIcon()
    {
        return 'app/bundles/NotificationBundle/Assets/img/OneSignal.png';
    }

    public function getSupportedFeatures()
    {
        return [
            'mobile',
            'landing_page_enabled',
            'welcome_notification_enabled',
            'tracking_page_enabled',
        ];
    }

    public function getSupportedFeatureTooltips()
    {
        return [
            'landing_page_enabled'  => 'autoborna.integration.form.features.landing_page_enabled.tooltip',
            'tracking_page_enabled' => 'autoborna.integration.form.features.tracking_page_enabled.tooltip',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getRequiredKeyFields()
    {
        return [
            'app_id'        => 'autoborna.notification.config.form.notification.app_id',
            'safari_web_id' => 'autoborna.notification.config.form.notification.safari_web_id',
            'rest_api_key'  => 'autoborna.notification.config.form.notification.rest_api_key',
            'gcm_sender_id' => 'autoborna.notification.config.form.notification.gcm_sender_id',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getAuthenticationType()
    {
        return 'none';
    }

    /**
     * @param \Autoborna\PluginBundle\Integration\Form|FormBuilder $builder
     * @param array                                             $data
     * @param string                                            $formArea
     */
    public function appendToForm(&$builder, $data, $formArea)
    {
        if ('features' == $formArea) {
            /* @var FormBuilder $builder */
            $builder->add(
                'subdomain_name',
                TextType::class,
                [
                    'label'    => 'autoborna.notification.form.subdomain_name.label',
                    'required' => false,
                    'attr'     => [
                        'class' => 'form-control',
                    ],
                ]
            );

            $builder->add(
                'platforms',
                ChoiceType::class,
                [
                    'choices' => [
                        'autoborna.integration.form.platforms.ios'     => 'ios',
                        'autoborna.integration.form.platforms.android' => 'android',
                    ],
                    'attr'              => [
                        'tooltip'      => 'autoborna.integration.form.platforms.tooltip',
                        'data-show-on' => '{"integration_details_supportedFeatures_0":"checked"}',
                    ],
                    'expanded'    => true,
                    'multiple'    => true,
                    'label'       => 'autoborna.integration.form.platforms',
                    'placeholder' => false,
                    'required'    => false,
                ]
            );
        }
    }
}
