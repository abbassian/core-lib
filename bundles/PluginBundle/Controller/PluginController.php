<?php

namespace Autoborna\PluginBundle\Controller;

use Autoborna\CoreBundle\Controller\FormController;
use Autoborna\CoreBundle\Helper\InputHelper;
use Autoborna\PluginBundle\Event\PluginIntegrationAuthRedirectEvent;
use Autoborna\PluginBundle\Event\PluginIntegrationEvent;
use Autoborna\PluginBundle\Form\Type\DetailsType;
use Autoborna\PluginBundle\Integration\AbstractIntegration;
use Autoborna\PluginBundle\Model\PluginModel;
use Autoborna\PluginBundle\PluginEvents;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PluginController.
 */
class PluginController extends FormController
{
    /**
     * @return JsonResponse|Response
     */
    public function indexAction()
    {
        if (!$this->get('autoborna.security')->isGranted('plugin:plugins:manage')) {
            return $this->accessDenied();
        }

        /** @var \Autoborna\PluginBundle\Model\PluginModel $pluginModel */
        $pluginModel = $this->getModel('plugin');

        // List of plugins for filter and to show as a single integration
        $plugins = $pluginModel->getEntities(
            [
                'filter' => [
                    'force' => [
                        [
                            'column' => 'p.isMissing',
                            'expr'   => 'eq',
                            'value'  => 0,
                        ],
                    ],
                ],
                'hydration_mode' => 'hydrate_array',
            ]
        );

        $session      = $this->get('session');
        $pluginFilter = $this->request->get('plugin', $session->get('autoborna.integrations.filter', ''));

        $session->set('autoborna.integrations.filter', $pluginFilter);

        /** @var \Autoborna\PluginBundle\Helper\IntegrationHelper $integrationHelper */
        $integrationHelper  = $this->factory->getHelper('integration');
        $integrationObjects = $integrationHelper->getIntegrationObjects(null, null, true);
        $integrations       = $foundPlugins       = [];

        foreach ($integrationObjects as $name => $object) {
            $settings = $object->getIntegrationSettings();
            $plugin   = $settings->getPlugin();
            $pluginId = $plugin ? $plugin->getId() : $name;
            if (isset($plugins[$pluginId]) || $pluginId === $name) {
                $integrations[$name] = [
                    'name'     => $object->getName(),
                    'display'  => $object->getDisplayName(),
                    'icon'     => $integrationHelper->getIconPath($object),
                    'enabled'  => $settings->isPublished(),
                    'plugin'   => $pluginId,
                    'isBundle' => false,
                ];
            }

            $foundPlugins[$pluginId] = true;
        }

        $nonIntegrationPlugins = array_diff_key($plugins, $foundPlugins);
        foreach ($nonIntegrationPlugins as $plugin) {
            $integrations[$plugin['name']] = [
                'name'        => $plugin['bundle'],
                'display'     => $plugin['name'],
                'icon'        => $integrationHelper->getIconPath($plugin),
                'enabled'     => true,
                'plugin'      => $plugin['id'],
                'description' => $plugin['description'],
                'isBundle'    => true,
            ];
        }

        //sort by name
        uksort(
            $integrations,
            function ($a, $b) {
                return strnatcasecmp($a, $b);
            }
        );

        $tmpl = $this->request->isXmlHttpRequest() ? $this->request->get('tmpl', 'index') : 'index';

        if (!empty($pluginFilter)) {
            foreach ($plugins as $plugin) {
                if ($plugin['id'] == $pluginFilter) {
                    $pluginName = $plugin['name'];
                    $pluginId   = $plugin['id'];
                    break;
                }
            }
        }

        return $this->delegateView(
            [
                'viewParameters' => [
                    'items'        => $integrations,
                    'tmpl'         => $tmpl,
                    'pluginFilter' => ($pluginFilter) ? ['id' => $pluginId, 'name' => $pluginName] : false,
                    'plugins'      => $plugins,
                ],
                'contentTemplate' => 'AutobornaPluginBundle:Integration:grid.html.php',
                'passthroughVars' => [
                    'activeLink'    => '#autoborna_plugin_index',
                    'autobornaContent' => 'integration',
                    'route'         => $this->generateUrl('autoborna_plugin_index'),
                ],
            ]
        );
    }

    /**
     * @param string $name
     *
     * @return JsonResponse|Response
     */
    public function configAction($name, $activeTab = 'details-container', $page = 1)
    {
        if (!$this->get('autoborna.security')->isGranted('plugin:plugins:manage')) {
            return $this->accessDenied();
        }
        if (!empty($this->request->get('activeTab'))) {
            $activeTab = $this->request->get('activeTab');
        }

        $session   = $this->get('session');

        $integrationDetailsPost = $this->request->request->get('integration_details', [], true);
        $authorize              = empty($integrationDetailsPost['in_auth']) ? false : true;

        /** @var \Autoborna\PluginBundle\Helper\IntegrationHelper $integrationHelper */
        $integrationHelper = $this->factory->getHelper('integration');
        /** @var AbstractIntegration $integrationObject */
        $integrationObject = $integrationHelper->getIntegrationObject($name);

        // Verify that the requested integration exists
        if (empty($integrationObject)) {
            throw $this->createNotFoundException($this->get('translator')->trans('autoborna.core.url.error.404'));
        }

        $object = ('leadFieldsContainer' === $activeTab) ? 'lead' : 'company';
        $limit  = $this->coreParametersHelper->get('default_pagelimit');
        $start  = (1 === $page) ? 0 : (($page - 1) * $limit);
        if ($start < 0) {
            $start = 0;
        }
        $session->set('autoborna.plugin.'.$name.'.'.$object.'.start', $start);
        $session->set('autoborna.plugin.'.$name.'.'.$object.'.page', $page);

        /** @var PluginModel $pluginModel */
        $pluginModel   = $this->getModel('plugin');
        $leadFields    = $pluginModel->getLeadFields();
        $companyFields = $pluginModel->getCompanyFields();
        /** @var \Autoborna\PluginBundle\Integration\AbstractIntegration $integrationObject */
        $entity = $integrationObject->getIntegrationSettings();

        $form = $this->createForm(
            DetailsType::class,
            $entity,
            [
                'integration'        => $entity->getName(),
                'lead_fields'        => $leadFields,
                'company_fields'     => $companyFields,
                'integration_object' => $integrationObject,
                'action'             => $this->generateUrl('autoborna_plugin_config', ['name' => $name]),
            ]
        );

        if ('POST' == $this->request->getMethod()) {
            $valid = false;
            if (!$cancelled = $this->isFormCancelled($form)) {
                $currentKeys            = $integrationObject->getDecryptedApiKeys($entity);
                $currentFeatureSettings = $entity->getFeatureSettings();
                $valid                  = $this->isFormValid($form);

                if ($authorize || $valid) {
                    $em          = $this->get('doctrine.orm.entity_manager');
                    $integration = $entity->getName();

                    if (isset($form['apiKeys'])) {
                        $keys = $form['apiKeys']->getData();

                        // Prevent merged keys
                        $secretKeys = $integrationObject->getSecretKeys();
                        foreach ($secretKeys as $secretKey) {
                            if (empty($keys[$secretKey]) && !empty($currentKeys[$secretKey])) {
                                $keys[$secretKey] = $currentKeys[$secretKey];
                            }
                        }
                        $integrationObject->encryptAndSetApiKeys($keys, $entity);
                    }

                    if (!$authorize) {
                        $features = $entity->getSupportedFeatures();
                        if (in_array('public_profile', $features) || in_array('push_lead', $features)) {
                            // Ungroup the fields
                            $autobornaLeadFields = [];
                            foreach ($leadFields as $groupFields) {
                                $autobornaLeadFields = array_merge($autobornaLeadFields, $groupFields);
                            }
                            $autobornaCompanyFields = [];
                            foreach ($companyFields as $groupFields) {
                                $autobornaCompanyFields = array_merge($autobornaCompanyFields, $groupFields);
                            }

                            if ($missing = $integrationObject->cleanUpFields($entity, $autobornaLeadFields, $autobornaCompanyFields)) {
                                if ($entity->getIsPublished()) {
                                    // Only fail validation if the integration is enabled
                                    if (!empty($missing['leadFields'])) {
                                        $valid = false;

                                        $form->get('featureSettings')->get('leadFields')->addError(
                                            new FormError(
                                                $this->get('translator')->trans('autoborna.plugin.field.required_mapping_missing', [], 'validators')
                                            )
                                        );
                                    }

                                    if (!empty($missing['companyFields'])) {
                                        $valid = false;

                                        $form->get('featureSettings')->get('companyFields')->addError(
                                            new FormError(
                                                $this->get('translator')->trans('autoborna.plugin.field.required_mapping_missing', [], 'validators')
                                            )
                                        );
                                    }
                                }
                            }
                        }
                    } else {
                        //make sure they aren't overwritten because of API connection issues
                        $entity->setFeatureSettings($currentFeatureSettings);
                    }

                    if ($valid || $authorize) {
                        $dispatcher = $this->get('event_dispatcher');
                        $this->get('monolog.logger.autoborna')->info('Dispatching integration config save event.');
                        if ($dispatcher->hasListeners(PluginEvents::PLUGIN_ON_INTEGRATION_CONFIG_SAVE)) {
                            $this->get('monolog.logger.autoborna')->info('Event dispatcher has integration config save listeners.');
                            $event = new PluginIntegrationEvent($integrationObject);

                            $dispatcher->dispatch(PluginEvents::PLUGIN_ON_INTEGRATION_CONFIG_SAVE, $event);

                            $entity = $event->getEntity();
                        }

                        $em->persist($entity);
                        $em->flush();
                    }

                    if ($authorize) {
                        //redirect to the oauth URL
                        /** @var \Autoborna\PluginBundle\Integration\AbstractIntegration $integrationObject */
                        $event = $this->dispatcher->dispatch(
                            PluginEvents::PLUGIN_ON_INTEGRATION_AUTH_REDIRECT,
                            new PluginIntegrationAuthRedirectEvent(
                                $integrationObject,
                                $integrationObject->getAuthLoginUrl()
                            )
                        );
                        $oauthUrl = $event->getAuthUrl();

                        return new JsonResponse(
                            [
                                'integration'         => $integration,
                                'authUrl'             => $oauthUrl,
                                'authorize'           => 1,
                                'popupBlockerMessage' => $this->translator->trans('autoborna.core.popupblocked'),
                            ]
                        );
                    }
                }
            }

            if (($cancelled || ($valid && !$this->isFormApplied($form))) && !$authorize) {
                // Close the modal and return back to the list view
                return new JsonResponse(
                    [
                        'closeModal'    => 1,
                        'enabled'       => $entity->getIsPublished(),
                        'name'          => $integrationObject->getName(),
                        'autobornaContent' => 'integrationConfig',
                        'sidebar'       => $this->get('templating')->render('AutobornaCoreBundle:LeftPanel:index.html.php'),
                    ]
                );
            }
        }

        $template    = $integrationObject->getFormTemplate();
        $objectTheme = $integrationObject->getFormTheme();
        $default     = 'AutobornaPluginBundle:FormTheme\Integration';
        $themes      = [$default];
        if (is_array($objectTheme)) {
            $themes = array_merge($themes, $objectTheme);
        } elseif ($objectTheme !== $default) {
            $themes[] = $objectTheme;
        }

        $formSettings = $integrationObject->getFormSettings();
        $callbackUrl  = !empty($formSettings['requires_callback']) ? $integrationObject->getAuthCallbackUrl() : '';

        $formNotes    = [];
        $noteSections = ['authorization', 'features', 'feature_settings', 'custom'];
        foreach ($noteSections as $section) {
            if ('custom' === $section) {
                $formNotes[$section] = $integrationObject->getFormNotes($section);
            } else {
                list($specialInstructions, $alertType) = $integrationObject->getFormNotes($section);

                if (!empty($specialInstructions)) {
                    $formNotes[$section] = [
                        'note' => $specialInstructions,
                        'type' => $alertType,
                    ];
                }
            }
        }

        return $this->delegateView(
            [
                'viewParameters' => [
                    'form'         => $this->setFormTheme($form, $template, $themes),
                    'description'  => $integrationObject->getDescription(),
                    'formSettings' => $formSettings,
                    'formNotes'    => $formNotes,
                    'callbackUrl'  => $callbackUrl,
                    'activeTab'    => $activeTab,
                ],
                'contentTemplate' => $template,
                'passthroughVars' => [
                    'activeLink'    => '#autoborna_plugin_index',
                    'autobornaContent' => 'integrationConfig',
                    'route'         => false,
                    'sidebar'       => $this->get('templating')->render('AutobornaCoreBundle:LeftPanel:index.html.php'),
                ],
            ]
        );
    }

    /**
     * @param $name
     *
     * @return array|JsonResponse|RedirectResponse|Response
     */
    public function infoAction($name)
    {
        if (!$this->get('autoborna.security')->isGranted('plugin:plugins:manage')) {
            return $this->accessDenied();
        }

        /** @var \Autoborna\PluginBundle\Model\PluginModel $pluginModel */
        $pluginModel = $this->getModel('plugin');

        $bundle = $pluginModel->getRepository()->findOneBy(
            [
                'bundle' => InputHelper::clean($name),
            ]
        );

        if (!$bundle) {
            return $this->accessDenied();
        }

        /** @var \Autoborna\PluginBundle\Helper\IntegrationHelper $integrationHelper */
        $integrationHelper = $this->factory->getHelper('integration');

        $bundle->splitDescriptions();

        return $this->delegateView(
            [
                'viewParameters' => [
                    'bundle' => $bundle,
                    'icon'   => $integrationHelper->getIconPath($bundle),
                ],
                'contentTemplate' => 'AutobornaPluginBundle:Integration:info.html.php',
                'passthroughVars' => [
                    'activeLink'    => '#autoborna_plugin_index',
                    'autobornaContent' => 'integration',
                    'route'         => false,
                ],
            ]
        );
    }

    /**
     * Scans the addon bundles directly and loads bundles which are not registered to the database.
     *
     * @return JsonResponse
     */
    public function reloadAction()
    {
        if (!$this->get('autoborna.security')->isGranted('plugin:plugins:manage')) {
            return $this->accessDenied();
        }

        $this->addFlash(
            $this->get('autoborna.plugin.facade.reload')->reloadPlugins()
        );

        $viewParameters = [
            'page' => $this->get('session')->get('autoborna.plugin.page'),
        ];

        // Refresh the index contents
        return $this->postActionRedirect(
            [
                'returnUrl'       => $this->generateUrl('autoborna_plugin_index', $viewParameters),
                'viewParameters'  => $viewParameters,
                'contentTemplate' => 'AutobornaPluginBundle:Plugin:index',
                'passthroughVars' => [
                    'activeLink'    => '#autoborna_plugin_index',
                    'autobornaContent' => 'plugin',
                ],
            ]
        );
    }
}
