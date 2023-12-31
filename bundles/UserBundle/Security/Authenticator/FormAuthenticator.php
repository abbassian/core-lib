<?php

namespace Autoborna\UserBundle\Security\Authenticator;

use Autoborna\PluginBundle\Helper\IntegrationHelper;
use Autoborna\UserBundle\Entity\User;
use Autoborna\UserBundle\Event\AuthenticationEvent;
use Autoborna\UserBundle\Security\Authentication\Token\PluginToken;
use Autoborna\UserBundle\UserEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\SimpleFormAuthenticatorInterface;

class FormAuthenticator implements SimpleFormAuthenticatorInterface
{
    /**
     * @var UserPasswordEncoder
     */
    protected $encoder;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var IntegrationHelper
     */
    protected $integrationHelper;

    /**
     * @var RequestStack|null
     */
    protected $requestStack;

    public function __construct(
        IntegrationHelper $integrationHelper,
        UserPasswordEncoder $encoder,
        EventDispatcherInterface $dispatcher,
        RequestStack $requestStack
    ) {
        $this->encoder           = $encoder;
        $this->dispatcher        = $dispatcher;
        $this->integrationHelper = $integrationHelper;
        $this->requestStack      = $requestStack;
    }

    /**
     * @param $providerKey
     *
     * @return PluginToken
     *
     * @throws AuthenticationException
     * @throws BadCredentialsException
     */
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        $authenticated         = false;
        $authenticationService = null;
        $response              = null;
        $failedAuthMessage     = null;
        $user                  = $token->getUser();
        $authenticatingService = ($token instanceof PluginToken) ? $token->getAuthenticatingService() : null;
        if (!$user instanceof User) {
            try {
                $user = $userProvider->loadUserByUsername($token->getUsername());
            } catch (UsernameNotFoundException $e) {
            }

            // Will try with the given password unless the plugin explicitly failed authentication
            $tryWithPassword = true;

            // Try authenticating with a plugin first
            if ($this->dispatcher->hasListeners(UserEvents::USER_FORM_AUTHENTICATION)) {
                $integrations = $this->integrationHelper->getIntegrationObjects($authenticatingService, ['sso_form'], false, null, true);
                $authEvent    = new AuthenticationEvent($user, $token, $userProvider, $this->requestStack->getCurrentRequest(), false, $authenticatingService, $integrations);
                $this->dispatcher->dispatch(UserEvents::USER_FORM_AUTHENTICATION, $authEvent);

                if ($authenticated = $authEvent->isAuthenticated()) {
                    $user                  = $authEvent->getUser();
                    $authenticatingService = $authEvent->getAuthenticatingService();
                } elseif ($authEvent->isFailed()) {
                    $tryWithPassword = false;
                }

                $response          = $authEvent->getResponse();
                $failedAuthMessage = $authEvent->getFailedAuthenticationMessage();
            }

            if (!$authenticated && $tryWithPassword && $user instanceof User) {
                // Try authenticating with local password
                $authenticated = $this->encoder->isPasswordValid($user, $token->getCredentials());
            }
        } else {
            // Assume the user is authenticated although the token will tell for sure
            $authenticated = true;
        }

        if ($authenticated) {
            return new PluginToken(
                $providerKey,
                $authenticatingService,
                $user,
                $user->getPassword(),
                $user->getRoles(),
                $response
            );
        } elseif ($response) {
            return new PluginToken(
                $providerKey,
                $authenticatingService,
                $user,
                '',
                [],
                $response
            );
        }

        if ($failedAuthMessage) {
            throw new AuthenticationException($failedAuthMessage);
        }

        throw new BadCredentialsException();
    }

    /**
     * @param $providerKey
     *
     * @return bool
     */
    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return ($token instanceof PluginToken || $token instanceof UsernamePasswordToken) && $token->getProviderKey() === $providerKey;
    }

    /**
     * @param $username
     * @param $password
     * @param $providerKey
     *
     * @return UsernamePasswordToken
     */
    public function createToken(Request $request, $username, $password, $providerKey)
    {
        return new PluginToken(
            $providerKey,
            null,
            $username,
            $password
        );
    }
}
