<?php

namespace Autoborna\PluginBundle\Controller;

use Autoborna\CoreBundle\Controller\FormController;
use Autoborna\PluginBundle\Event\PluginIntegrationAuthRedirectEvent;
use Autoborna\PluginBundle\PluginEvents;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class AuthController.
 */
class AuthController extends FormController
{
    /**
     * @param string $integration
     *
     * @return JsonResponse
     */
    public function authCallbackAction($integration)
    {
        $isAjax  = $this->request->isXmlHttpRequest();
        $session = $this->get('session');

        /** @var \Autoborna\PluginBundle\Helper\IntegrationHelper $integrationHelper */
        $integrationHelper = $this->factory->getHelper('integration');
        $integrationObject = $integrationHelper->getIntegrationObject($integration);

        //check to see if the service exists
        if (!$integrationObject) {
            $session->set('autoborna.integration.postauth.message', ['autoborna.integration.notfound', ['%name%' => $integration], 'error']);
            if ($isAjax) {
                return new JsonResponse(['url' => $this->generateUrl('autoborna_integration_auth_postauth', ['integration' => $integration])]);
            } else {
                return new RedirectResponse($this->generateUrl('autoborna_integration_auth_postauth', ['integration' => $integration]));
            }
        }

        try {
            $error = $integrationObject->authCallback();
        } catch (\InvalidArgumentException $e) {
            $session->set('autoborna.integration.postauth.message', [$e->getMessage(), [], 'error']);
            $redirectUrl = $this->generateUrl('autoborna_integration_auth_postauth', ['integration' => $integration]);
            if ($isAjax) {
                return new JsonResponse(['url' => $redirectUrl]);
            } else {
                return new RedirectResponse($redirectUrl);
            }
        }

        //check for error
        if ($error) {
            $type    = 'error';
            $message = 'autoborna.integration.error.oauthfail';
            $params  = ['%error%' => $error];
        } else {
            $type    = 'notice';
            $message = 'autoborna.integration.notice.oauthsuccess';
            $params  = [];
        }

        $session->set('autoborna.integration.postauth.message', [$message, $params, $type]);

        $identifier[$integration] = null;
        $socialCache              = [];
        $userData                 = $integrationObject->getUserData($identifier, $socialCache);

        $session->set('autoborna.integration.'.$integration.'.userdata', $userData);

        return new RedirectResponse($this->generateUrl('autoborna_integration_auth_postauth', ['integration' => $integration]));
    }

    /**
     * @param $integration
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function authStatusAction($integration)
    {
        $postAuthTemplate = 'AutobornaPluginBundle:Auth:postauth.html.php';

        $session     = $this->get('session');
        $postMessage = $session->get('autoborna.integration.postauth.message');
        $userData    = [];

        if (isset($integration)) {
            $userData = $session->get('autoborna.integration.'.$integration.'.userdata');
        }

        $message = $type = '';
        $alert   = 'success';
        if (!empty($postMessage)) {
            $message = $this->translator->trans($postMessage[0], $postMessage[1], 'flashes');
            $session->remove('autoborna.integration.postauth.message');
            $type = $postMessage[2];
            if ('error' == $type) {
                $alert = 'danger';
            }
        }

        return $this->render($postAuthTemplate, ['message' => $message, 'alert' => $alert, 'data' => $userData]);
    }

    /**
     * @param $integration
     *
     * @return RedirectResponse
     */
    public function authUserAction($integration)
    {
        /** @var \Autoborna\PluginBundle\Helper\IntegrationHelper $integrationHelper */
        $integrationHelper = $this->factory->getHelper('integration');
        $integrationObject = $integrationHelper->getIntegrationObject($integration);

        $settings['method']      = 'GET';
        $settings['integration'] = $integrationObject->getName();

        /** @var \Autoborna\PluginBundle\Integration\AbstractIntegration $integrationObject */
        $event = $this->dispatcher->dispatch(
            PluginEvents::PLUGIN_ON_INTEGRATION_AUTH_REDIRECT,
            new PluginIntegrationAuthRedirectEvent(
                $integrationObject,
                $integrationObject->getAuthLoginUrl()
            )
        );
        $oauthUrl = $event->getAuthUrl();

        return new RedirectResponse($oauthUrl);
    }
}
