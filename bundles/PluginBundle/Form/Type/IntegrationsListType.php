<?php

namespace Autoborna\PluginBundle\Form\Type;

use Autoborna\PluginBundle\Helper\IntegrationHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class IntegrationsListType extends AbstractType
{
    /**
     * @var IntegrationHelper
     */
    private $integrationHelper;

    public function __construct(IntegrationHelper $integrationHelper)
    {
        $this->integrationHelper = $integrationHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $integrationObjects = $this->integrationHelper->getIntegrationObjects(null, $options['supported_features'], true);
        $integrations       = ['' => ''];

        foreach ($integrationObjects as $object) {
            $settings = $object->getIntegrationSettings();

            if ($settings->isPublished()) {
                if (!isset($integrations[$settings->getPlugin()->getName()])) {
                    $integrations[$settings->getPlugin()->getName()] = [];
                }
                $integrations[$settings->getPlugin()->getName()][$object->getDisplayName()] = $object->getName();
            }
        }

        $builder->add(
            'integration',
            ChoiceType::class,
            [
                'choices'    => $integrations,
                'expanded'   => false,
                'label_attr' => ['class' => 'control-label'],
                'multiple'   => false,
                'label'      => 'autoborna.integration.integration',
                'attr'       => [
                    'class'    => 'form-control',
                    'tooltip'  => 'autoborna.integration.integration.tooltip',
                    'onchange' => 'Autoborna.getIntegrationConfig(this);',
                ],
                'required'    => true,
                'constraints' => [
                    new NotBlank(
                        ['message' => 'autoborna.core.value.required']
                    ),
                ],
                ]
        );

        $formModifier = function (FormInterface $form, $data) use ($integrationObjects) {
            $statusChoices   = [];
            $campaignChoices = [];

            if (isset($data['integration'])) {
                $integrationObject = $this->integrationHelper->getIntegrationObject($data['integration']);
                if (method_exists($integrationObject, 'getCampaigns')) {
                    $campaigns = $integrationObject->getCampaigns();

                    if (isset($campaigns['records']) && !empty($campaigns['records'])) {
                        foreach ($campaigns['records'] as $campaign) {
                            $campaignChoices[$campaign['Id']] = $campaign['Name'];
                        }
                    }
                }
                if (method_exists($integrationObject, 'getCampaignMemberStatus') && isset($data['config']['campaigns'])) {
                    $campaignStatus = $integrationObject->getCampaignMemberStatus($data['config']['campaigns']);

                    if (isset($campaignStatus['records']) && !empty($campaignStatus['records'])) {
                        foreach ($campaignStatus['records'] as $campaignS) {
                            $statusChoices[$campaignS['Label']] = $campaignS['Label'];
                        }
                    }
                }
            }
            $form->add(
                'config',
                IntegrationConfigType::class,
                [
                    'label' => false,
                    'attr'  => [
                        'class' => 'integration-config-container',
                    ],
                    'integration' => (isset($integrationObjects[$data['integration']])) ? $integrationObjects[$data['integration']] : null,
                    'campaigns'   => $campaignChoices,
                    'data'        => (isset($data['config'])) ? $data['config'] : [],
                ]
            );

            $hideClass = (isset($data['campaign_member_status']) && !empty($data['campaign_member_status']['campaign_member_status'])) ? '' : ' hide';
            $form->add(
                'campaign_member_status',
                IntegrationCampaignsType::class,
                [
                    'label' => false,
                    'attr'  => [
                        'class' => 'integration-campaigns-status'.$hideClass,
                    ],
                    'campaignContactStatus' => $statusChoices,
                    'data'                  => (isset($data['campaign_member_status'])) ? $data['campaign_member_status'] : [],
                ]
            );
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $data = $event->getData();
                $formModifier($event->getForm(), $data);
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $data = $event->getData();
                $formModifier($event->getForm(), $data);
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined(['supported_features']);
        $resolver->setDefaults(
            [
                'supported_features' => 'push_lead',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integration_list';
    }
}
