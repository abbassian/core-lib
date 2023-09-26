<?php

namespace Autoborna\ApiBundle;

/**
 * Class ApiEvents.
 */
final class ApiEvents
{
    /**
     * The autoborna.client_pre_save event is thrown right before an API client is persisted.
     *
     * The event listener receives a Autoborna\ApiBundle\Event\ClientEvent instance.
     *
     * @var string
     */
    const CLIENT_PRE_SAVE = 'autoborna.client_pre_save';

    /**
     * The autoborna.client_post_save event is thrown right after an API client is persisted.
     *
     * The event listener receives a Autoborna\ApiBundle\Event\ClientEvent instance.
     *
     * @var string
     */
    const CLIENT_POST_SAVE = 'autoborna.client_post_save';

    /**
     * The autoborna.client_post_delete event is thrown after an API client is deleted.
     *
     * The event listener receives a Autoborna\ApiBundle\Event\ClientEvent instance.
     *
     * @var string
     */
    const CLIENT_POST_DELETE = 'autoborna.client_post_delete';

    /**
     * The autoborna.build_api_route event is thrown to build Autoborna API routes.
     *
     * The event listener receives a Autoborna\CoreBundle\Event\RouteEvent instance.
     *
     * @var string
     */
    const BUILD_ROUTE = 'autoborna.build_api_route';

    /**
     * The autoborna.api_on_entity_pre_save event is thrown after an entity about to be saved via API.
     *
     * The event listener receives a Autoborna\ApiBundle\Event\ApiEntityEvent instance.
     *
     * @var string
     */
    const API_ON_ENTITY_PRE_SAVE = 'autoborna.api_on_entity_pre_save';

    /**
     * The autoborna.api_on_entity_post_save event is thrown after an entity is saved via API.
     *
     * The event listener receives a Autoborna\ApiBundle\Event\ApiEntityEvent instance.
     *
     * @var string
     */
    const API_ON_ENTITY_POST_SAVE = 'autoborna.api_on_entity_post_save';
}
