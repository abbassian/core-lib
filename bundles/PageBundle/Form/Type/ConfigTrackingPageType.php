<?php

namespace Autoborna\PageBundle\Form\Type;

use Autoborna\CoreBundle\Form\Type\YesNoButtonGroupType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ConfigType.
 */
class ConfigTrackingPageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('track_by_tracking_url', YesNoButtonGroupType::class, [
            'label' => 'autoborna.page.config.form.track.by.tracking.url',
            'data'  => isset($options['data']['track_by_tracking_url']) ? (bool) $options['data']['track_by_tracking_url'] : true,
            'attr'  => [
                'tooltip' => 'autoborna.page.config.form.track.by.tracking.url.tooltip',
            ],
        ]);

        $builder->add(
            'anonymize_ip',
            YesNoButtonGroupType::class,
            [
                'label' => 'autoborna.page.config.form.anonymize_ip',
                'data'  => isset($options['data']['anonymize_ip']) ? (bool) $options['data']['anonymize_ip'] : false,
                'attr'  => [
                    'tooltip' => 'autoborna.page.config.form.anonymize_ip.tooltip',
                ],
            ]
        );

        $builder->add(
            'track_contact_by_ip',
            YesNoButtonGroupType::class,
            [
                'label' => 'autoborna.page.config.form.track_contact_by_ip',
                'data'  => isset($options['data']['track_contact_by_ip']) ? (bool) $options['data']['track_contact_by_ip'] : false,
                'attr'  => [
                    'tooltip'      => 'autoborna.page.config.form.track_contact_by_ip.tooltip',
                    'data-show-on' => '{"config_trackingconfig_anonymize_ip_0":"checked"}',
                ],
            ]
        );

        $builder->add(
            'do_not_track_404_anonymous',
            YesNoButtonGroupType::class,
            [
                'label' => 'autoborna.page.config.form.do_not_track_404_anonymous',
                'data'  => isset($options['data']['do_not_track_404_anonymous']) ? (bool) $options['data']['do_not_track_404_anonymous'] : false,
                'attr'  => [
                    'tooltip'      => 'autoborna.page.config.form.do_not_track_404_anonymous.tooltip',
                ],
            ]
        );

        $builder->add(
            'facebook_pixel_id',
            TextType::class,
            [
                'label' => 'autoborna.page.config.form.facebook.pixel.id',
                'attr'  => [
                    'class' => 'form-control',
                ],
                'required' => false,
            ]
        );

        $builder->add(
            'facebook_pixel_trackingpage_enabled',
            YesNoButtonGroupType::class,
            [
                'label' => 'autoborna.page.config.form.tracking.trackingpage.enabled',
                'data'  => isset($options['data']['facebook_pixel_trackingpage_enabled']) ? (bool) $options['data']['facebook_pixel_trackingpage_enabled'] : false,
            ]
        );

        $builder->add(
            'facebook_pixel_landingpage_enabled',
            YesNoButtonGroupType::class,
            [
                'label' => 'autoborna.page.config.form.tracking.landingpage.enabled',
                'data'  => isset($options['data']['facebook_pixel_landingpage_enabled']) ? (bool) $options['data']['facebook_pixel_landingpage_enabled'] : false,
            ]
        );

        $builder->add(
            'google_analytics_id',
            TextType::class,
            [
                'label' => 'autoborna.page.config.form.google.analytics.id',
                'attr'  => [
                    'class' => 'form-control',
                ],
                'required' => false,
            ]
        );

        $builder->add(
            'google_analytics_trackingpage_enabled',
            YesNoButtonGroupType::class,
            [
                'label' => 'autoborna.page.config.form.tracking.trackingpage.enabled',
                'data'  => isset($options['data']['google_analytics_trackingpage_enabled']) ? (bool) $options['data']['google_analytics_trackingpage_enabled'] : false,
            ]
        );

        $builder->add(
            'google_analytics_landingpage_enabled',
            YesNoButtonGroupType::class,
            [
                'label' => 'autoborna.page.config.form.tracking.landingpage.enabled',
                'data'  => isset($options['data']['google_analytics_landingpage_enabled']) ? (bool) $options['data']['google_analytics_landingpage_enabled'] : false,
            ]
        );

        $builder->add(
            'google_analytics_anonymize_ip',
            YesNoButtonGroupType::class,
            [
                'label' => 'autoborna.page.config.form.tracking.anonymize.ip.enabled',
                'data'  => isset($options['data']['google_analytics_anonymize_ip']) ? (bool) $options['data']['google_analytics_anonymize_ip'] : false,
                'attr'  => [
                    'tooltip' => 'autoborna.page.config.form.tracking.anonymize.ip.enabled.tooltip',
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'trackingconfig';
    }
}
