<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Controller;

use Autoborna\CoreBundle\Controller\CommonController;
use Autoborna\IntegrationsBundle\Exception\IntegrationNotFoundException;
use Autoborna\IntegrationsBundle\Exception\UnauthorizedException;
use Autoborna\IntegrationsBundle\Helper\AuthIntegrationsHelper;
use Symfony\Component\HttpFoundation\Request;

class AuthController extends CommonController
{
    public function callbackAction(string $integration, Request $request)
    {
        /** @var AuthIntegrationsHelper $authIntegrationsHelper */
        $authIntegrationsHelper = $this->get('autoborna.integrations.helper.auth_integrations');
        $authenticationError    = false;

        try {
            $authIntegration = $authIntegrationsHelper->getIntegration($integration);
            $message         = $authIntegration->authenticateIntegration($request);
        } catch (UnauthorizedException $exception) {
            $message             = $exception->getMessage();
            $authenticationError = true;
        } catch (IntegrationNotFoundException $exception) {
            return $this->notFound();
        }

        return $this->render(
            'IntegrationsBundle:Auth:authenticated.html.php',
            [
                'message'             => $message,
                'authenticationError' => $authenticationError,
            ]
        );
    }
}
