<?php

namespace Autoborna\PluginBundle;

/**
 * Class PluginEvents.
 *
 * Events available for PluginEvents
 */
final class PluginEvents
{
    /**
     * The autoborna.plugin_on_integration_config_save event is dispatched when an integration's configuration is saved.
     *
     * The event listener receives a Autoborna\PluginBundle\Event\PluginIntegrationEvent instance.
     *
     * @var string
     */
    const PLUGIN_ON_INTEGRATION_CONFIG_SAVE = 'autoborna.plugin_on_integration_config_save';

    /**
     * The autoborna.plugin_on_integration_keys_encrypt event is dispatched prior to encrypting keys to be stored into the database.
     *
     * The event listener receives a Autoborna\PluginBundle\Event\PluginIntegrationKeyEvent instance.
     *
     * @var string
     */
    const PLUGIN_ON_INTEGRATION_KEYS_ENCRYPT = 'autoborna.plugin_on_integration_keys_encrypt';

    /**
     * The autoborna.plugin_on_integration_keys_decrypt event is dispatched after fetching and decrypting keys from the database.
     *
     * The event listener receives a Autoborna\PluginBundle\Event\PluginIntegrationKeyEvent instance.
     *
     * @var string
     */
    const PLUGIN_ON_INTEGRATION_KEYS_DECRYPT = 'autoborna.plugin_on_integration_keys_decrypt';

    /**
     * The autoborna.plugin_on_integration_keys_merge event is dispatched after new keys are merged into existing ones.
     *
     * The event listener receives a Autoborna\PluginBundle\Event\PluginIntegrationKeyEvent instance.
     *
     * @var string
     */
    const PLUGIN_ON_INTEGRATION_KEYS_MERGE = 'autoborna.plugin_on_integration_keys_merge';

    /**
     * The autoborna.plugin_on_integration_request event is dispatched before a request is made.
     *
     * The event listener receives a Autoborna\PluginBundle\Event\PluginIntegrationRequestEvent instance.
     *
     * @var string
     */
    const PLUGIN_ON_INTEGRATION_REQUEST = 'autoborna.plugin_on_integration_request';

    /**
     * The autoborna.plugin_on_integration_response event is dispatched after a request is made.
     *
     * The event listener receives a Autoborna\PluginBundle\Event\PluginIntegrationRequestEvent instance.
     *
     * @var string
     */
    const PLUGIN_ON_INTEGRATION_RESPONSE = 'autoborna.plugin_on_integration_response';

    /**
     * The autoborna.plugin_on_integration_auth_redirect event is dispatched when an authorization URL is generated and before the user is redirected to it.
     *
     * The event listener receives a Autoborna\PluginBundle\Event\PluginIntegrationAuthRedirectEvent instance.
     *
     * @var string
     */
    const PLUGIN_ON_INTEGRATION_AUTH_REDIRECT = 'autoborna.plugin_on_integration_auth_redirect';

    /**
     * The autoborna.plugin.on_campaign_trigger_action event is fired when the campaign action triggers.
     *
     * The event listener receives a
     * Autoborna\CampaignBundle\Event\CampaignExecutionEvent
     *
     * @var string
     */
    const ON_CAMPAIGN_TRIGGER_ACTION = 'autoborna.plugin.on_campaign_trigger_action';

    /**
     * The autoborna.plugin_on_integration_get_auth_callback_url event is dispatched when generating the redirect/callback URL.
     *
     * The event listener receives a Autoborna\PluginBundle\Event\PluginIntegrationAuthCallbackUrlEvent instance.
     *
     * @var string
     */
    const PLUGIN_ON_INTEGRATION_GET_AUTH_CALLBACK_URL = 'autoborna.plugin_on_integration_get_auth_callback_url';

    /**
     * The autoborna.plugin_on_integration_form_display event is dispatched when fetching display settings for the integration's config form.
     *
     * The event listener receives a Autoborna\PluginBundle\Event\PluginIntegrationFormDisplayEvent instance.
     *
     * @var string
     */
    const PLUGIN_ON_INTEGRATION_FORM_DISPLAY = 'autoborna.plugin_on_integration_form_display';

    /**
     * The autoborna.plugin_on_integration_form_build event is dispatched when building an integration's config form.
     *
     * The event listener receives a Autoborna\PluginBundle\Event\PluginIntegrationFormBuildEvent instance.
     *
     * @var string
     */
    const PLUGIN_ON_INTEGRATION_FORM_BUILD = 'autoborna.plugin_on_integration_form_build';

    /**
     * The autoborna.plugin.on_form_submit_action_triggered event is dispatched when a plugin related submit action is executed.
     *
     * The event listener receives a Autoborna\PluginBundle\Event\PluginIntegrationFormBuildEvent instance.
     *
     * @var string
     */
    const ON_FORM_SUBMIT_ACTION_TRIGGERED = 'autoborna.plugin.on_form_submit_action_triggered';

    /**
     * The autoborna.plugin.on_plugin_update event is dispatched when a plugin is updated.
     *
     * The event listener receives a Autoborna\PluginBundle\Event\PluginUpdateEvent instance.
     *
     * @var string
     */
    const ON_PLUGIN_UPDATE = 'autoborna.plugin.on_plugin_update';

    /**
     * The autoborna.plugin.on_plugin_install event is dispatched when a plugin is installed.
     *
     * The event listener receives a Autoborna\PluginBundle\Event\PluginInstallEvent instance.
     *
     * @var string
     */
    const ON_PLUGIN_INSTALL = 'autoborna.plugin.on_plugin_install';
}
