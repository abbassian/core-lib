<?php

namespace Autoborna\UserBundle;

/**
 * Class UserEvents.
 *
 * Events available for UserBundle
 */
final class UserEvents
{
    /**
     * The autoborna.user_pre_save event is dispatched right before a user is persisted.
     *
     * The event listener receives a Autoborna\UserBundle\Event\UserEvent instance.
     *
     * @var string
     */
    const USER_PRE_SAVE = 'autoborna.user_pre_save';

    /**
     * The autoborna.user_post_save event is dispatched right after a user is persisted.
     *
     * The event listener receives a Autoborna\UserBundle\Event\UserEvent instance.
     *
     * @var string
     */
    const USER_POST_SAVE = 'autoborna.user_post_save';

    /**
     * The autoborna.user_pre_delete event is dispatched prior to when a user is deleted.
     *
     * The event listener receives a Autoborna\UserBundle\Event\UserEvent instance.
     *
     * @var string
     */
    const USER_PRE_DELETE = 'autoborna.user_pre_delete';

    /**
     * The autoborna.user_post_delete event is dispatched after a user is deleted.
     *
     * The event listener receives a Autoborna\UserBundle\Event\UserEvent instance.
     *
     * @var string
     */
    const USER_POST_DELETE = 'autoborna.user_post_delete';

    /**
     * The autoborna.role_pre_save event is dispatched right before a role is persisted.
     *
     * The event listener receives a Autoborna\UserBundle\Event\RoleEvent instance.
     *
     * @var string
     */
    const ROLE_PRE_SAVE = 'autoborna.role_pre_save';

    /**
     * The autoborna.role_post_save event is dispatched right after a role is persisted.
     *
     * The event listener receives a Autoborna\UserBundle\Event\RoleEvent instance.
     *
     * @var string
     */
    const ROLE_POST_SAVE = 'autoborna.role_post_save';

    /**
     * The autoborna.role_pre_delete event is dispatched prior a role being deleted.
     *
     * The event listener receives a Autoborna\UserBundle\Event\RoleEvent instance.
     *
     * @var string
     */
    const ROLE_PRE_DELETE = 'autoborna.role_pre_delete';

    /**
     * The autoborna.role_post_delete event is dispatched after a role is deleted.
     *
     * The event listener receives a Autoborna\UserBundle\Event\RoleEvent instance.
     *
     * @var string
     */
    const ROLE_POST_DELETE = 'autoborna.role_post_delete';

    /**
     * The autoborna.user_logout event is dispatched during the logout routine giving a chance to carry out tasks before
     * the session is lost.
     *
     * The event listener receives a Autoborna\UserBundle\Event\LogoutEvent instance.
     *
     * @var string
     */
    const USER_LOGOUT = 'autoborna.user_logout';

    /**
     * The autoborna.user_login event is dispatched right after a user logs in.
     *
     * The event listener receives a Autoborna\UserBundle\Event\LoginEvent instance.
     *
     * @var string
     */
    const USER_LOGIN = 'autoborna.user_login';

    /**
     * The autoborna.user_form_authentication event is dispatched when a user logs in so that listeners can authenticate a user, i.e. via a 3rd party service.
     *
     * The event listener receives a Autoborna\UserBundle\Event\AuthenticationEvent instance.
     *
     * @var string
     */
    const USER_FORM_AUTHENTICATION = 'autoborna.user_form_authentication';

    /**
     * The autoborna.user_pre_authentication event is dispatched when a user browses a page under /s/ except for /login. This allows support for
     * 3rd party authentication providers outside the login form.
     *
     * The event listener receives a Autoborna\UserBundle\Event\AuthenticationEvent instance.
     *
     * @var string
     */
    const USER_PRE_AUTHENTICATION = 'autoborna.user_pre_authentication';

    /**
     * The autoborna.user_authentication_content event is dispatched to collect HTML from plugins to be injected into the UI to assist with
     * authentication.
     *
     * The event listener receives a Autoborna\UserBundle\Event\AuthenticationContentEvent instance.
     *
     * @var string
     */
    const USER_AUTHENTICATION_CONTENT = 'autoborna.user_authentication_content';
}
