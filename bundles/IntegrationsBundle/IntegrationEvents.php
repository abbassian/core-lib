<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle;

final class IntegrationEvents
{
    /**
     * The autoborna.integration.sync_post_execute_integration event is dispatched after a sync is executed.
     *
     * The event listener receives a Autoborna\IntegrationsBundle\Event\SyncEvent object.
     *
     * @var string
     */
    public const INTEGRATION_POST_EXECUTE = 'autoborna.integration.sync_post_execute_integration';

    /**
     * The autoborna.integration.config_form_loaded event is dispatched when config page for integration is loaded.
     *
     * The event listener receives a Autoborna\IntegrationsBundle\Event\FormLoadEvent object.
     *
     * @var string
     */
    public const INTEGRATION_CONFIG_FORM_LOAD = 'autoborna.integration.config_form_loaded';

    /**
     * The autoborna.integration.config_before_save event is dispatched prior to an integration's configuration is saved.
     *
     * The event listener receives a Autoborna\IntegrationsBundle\Event\ConfigSaveEvent instance.
     *
     * @var string
     */
    public const INTEGRATION_CONFIG_BEFORE_SAVE = 'autoborna.integration.config_before_save';

    /**
     * The autoborna.integration.config_after_save event is dispatched after an integration's configuration is saved.
     *
     * The event listener receives a Autoborna\IntegrationsBundle\Event\ConfigSaveEvent instance.
     *
     * @var string
     */
    public const INTEGRATION_CONFIG_AFTER_SAVE = 'autoborna.integration.config_after_save';

    /**
     * The autoborna.integration.keys_before_encryption event is dispatched prior to encrypting keys to be stored into the database.
     *
     * The event listener receives a Autoborna\IntegrationsBundle\Event\KeysEncryptionEvent instance.
     *
     * @var string
     */
    public const INTEGRATION_KEYS_BEFORE_ENCRYPTION = 'autoborna.integration.keys_before_encryption';

    /**
     * The autoborna.integration.keys_after_decryption event is dispatched after fetching and decrypting keys from the database.
     *
     * The event listener receives a Autoborna\IntegrationsBundle\Event\KeysDecryptionEvent instance.
     *
     * @var string
     */
    public const INTEGRATION_KEYS_AFTER_DECRYPTION = 'autoborna.integration.keys_after_decryption';

    /**
     * The autoborna.integration.autoborna_sync_field_load event is dispatched when Autoborna sync fields are build.
     *
     * The event listener receives a Autoborna\IntegrationsBundle\Event\AutobornaSyncFieldsLoadEvent instance.
     *
     * @var string
     */
    public const INTEGRATION_MAUTIC_SYNC_FIELDS_LOAD = 'autoborna.integration.autoborna_sync_field_load';

    /**
     * The autoborna.integration.INTEGRATION_COLLECT_INTERNAL_OBJECTS event is dispatched when a list of Autoborna internal objects is build.
     *
     * The event listener receives a Autoborna\IntegrationsBundle\Event\InternalObjectEvent instance.
     *
     * @var string
     */
    public const INTEGRATION_COLLECT_INTERNAL_OBJECTS = 'autoborna.integration.INTEGRATION_COLLECT_INTERNAL_OBJECTS';

    /**
     * The autoborna.integration.INTEGRATION_CREATE_INTERNAL_OBJECTS event is dispatched when a list of Autoborna internal objects should be created.
     *
     * The event listener receives a Autoborna\IntegrationsBundle\Event\InternalObjectCreateEvent instance.
     *
     * @var string
     */
    public const INTEGRATION_CREATE_INTERNAL_OBJECTS = 'autoborna.integration.INTEGRATION_CREATE_INTERNAL_OBJECTS';

    /**
     * The autoborna.integration.INTEGRATION_UPDATE_INTERNAL_OBJECTS event is dispatched when a list of Autoborna internal objects should be updated.
     *
     * The event listener receives a Autoborna\IntegrationsBundle\Event\InternalObjectUpdateEvent instance.
     *
     * @var string
     */
    public const INTEGRATION_UPDATE_INTERNAL_OBJECTS = 'autoborna.integration.INTEGRATION_UPDATE_INTERNAL_OBJECTS';

    /**
     * The autoborna.integration.INTEGRATION_FIND_INTERNAL_RECORDS event is dispatched when a list of Autoborna internal object records by ID is requested.
     *
     * The event listener receives a Autoborna\IntegrationsBundle\Event\InternalObjectFindEvent instance.
     *
     * @var string
     */
    public const INTEGRATION_FIND_INTERNAL_RECORDS = 'autoborna.integration.INTEGRATION_FIND_INTERNAL_RECORDS';

    /**
     * The autoborna.integration.INTEGRATION_FIND_OWNER_IDS event is dispatched when a list of Autoborna internal owner IDs by internal object ID is requested.
     *
     * The event listener receives a Autoborna\IntegrationsBundle\Event\InternalObjectFindEvent instance.
     *
     * @var string
     */
    public const INTEGRATION_FIND_OWNER_IDS = 'autoborna.integration.INTEGRATION_FIND_OWNER_IDS';

    /**
     * The autoborna.integration.INTEGRATION_BUILD_INTERNAL_OBJECT_ROUTE event is dispatched when a Autoborna internal object route is requested.
     *
     * The event listener receives a Autoborna\IntegrationsBundle\Event\InternalObjectOwnerEvent instance.
     *
     * @var string
     */
    public const INTEGRATION_BUILD_INTERNAL_OBJECT_ROUTE = 'autoborna.integration.INTEGRATION_BUILD_INTERNAL_OBJECT_ROUTE';

    /**
     * This event is dispatched when a tokens are being built to represent links to mapped integration objects.
     *
     * The event listener receives a Autoborna\IntegrationsBundle\Event\MappedIntegrationObjectTokenEvent instance.
     *
     * @var string
     */
    public const INTEGRATION_OBJECT_TOKEN_EVENT = 'autoborna.integration.INTEGRATION_OBJECT_TOKEN_EVENT';
}
