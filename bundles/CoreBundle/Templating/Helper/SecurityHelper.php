<?php

namespace Autoborna\CoreBundle\Templating\Helper;

use Autoborna\CoreBundle\Security\Permissions\CorePermissions;
use Autoborna\UserBundle\Event\AuthenticationContentEvent;
use Autoborna\UserBundle\UserEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * Class SecurityHelper.
 */
class SecurityHelper extends Helper
{
    /**
     * @var CorePermissions
     */
    private $security;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * @var CsrfTokenManagerInterface
     */
    private $tokenManager;

    /**
     * SecurityHelper constructor.
     */
    public function __construct(
        CorePermissions $security,
        RequestStack $requestStack,
        EventDispatcherInterface $dispatcher,
        CsrfTokenManagerInterface $tokenManager
    ) {
        $this->security     = $security;
        $this->requestStack = $requestStack;
        $this->dispatcher   = $dispatcher;
        $this->tokenManager = $tokenManager;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'security';
    }

    /**
     * Helper function to check if the logged in user has access to an entity.
     *
     * @param $ownPermission
     * @param $otherPermission
     * @param $ownerId
     *
     * @return bool
     */
    public function hasEntityAccess($ownPermission, $otherPermission, $ownerId)
    {
        return $this->security->hasEntityAccess($ownPermission, $otherPermission, $ownerId);
    }

    /**
     * @param $permission
     *
     * @return mixed
     */
    public function isGranted($permission)
    {
        return $this->security->isGranted($permission);
    }

    /**
     * Get content from listeners.
     */
    public function getAuthenticationContent()
    {
        $request = $this->requestStack->getCurrentRequest();
        $content = '';
        if ($this->dispatcher->hasListeners(UserEvents::USER_AUTHENTICATION_CONTENT)) {
            $event = new AuthenticationContentEvent($request);
            $this->dispatcher->dispatch(UserEvents::USER_AUTHENTICATION_CONTENT, $event);
            $content = $event->getContent();

            // Remove post_logout session after content has been generated
            $request->getSession()->remove('post_logout');
        }

        return $content;
    }

    /**
     * Returns CSRF token string for an intention.
     *
     * @param string $intention
     *
     * @return string
     */
    public function getCsrfToken($intention)
    {
        return $this->tokenManager->getToken($intention)->getValue();
    }
}
