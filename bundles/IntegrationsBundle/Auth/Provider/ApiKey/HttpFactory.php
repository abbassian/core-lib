<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Auth\Provider\ApiKey;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Autoborna\IntegrationsBundle\Auth\Provider\ApiKey\Credentials\HeaderCredentialsInterface;
use Autoborna\IntegrationsBundle\Auth\Provider\ApiKey\Credentials\ParameterCredentialsInterface;
use Autoborna\IntegrationsBundle\Auth\Provider\AuthConfigInterface;
use Autoborna\IntegrationsBundle\Auth\Provider\AuthCredentialsInterface;
use Autoborna\IntegrationsBundle\Auth\Provider\AuthProviderInterface;
use Autoborna\IntegrationsBundle\Exception\InvalidCredentialsException;
use Autoborna\IntegrationsBundle\Exception\PluginNotConfiguredException;

/**
 * Factory for building HTTP clients using basic auth.
 */
class HttpFactory implements AuthProviderInterface
{
    const NAME = 'api_key';

    /**
     * Cache of initialized clients.
     *
     * @var Client[]
     */
    private $initializedClients = [];

    /**
     * @var HeaderCredentialsInterface|ParameterCredentialsInterface
     */
    private $credentials;

    public function getAuthType(): string
    {
        return self::NAME;
    }

    /**
     * @param HeaderCredentialsInterface|ParameterCredentialsInterface|AuthCredentialsInterface $credentials
     *
     * @throws PluginNotConfiguredException
     * @throws InvalidCredentialsException
     */
    public function getClient(AuthCredentialsInterface $credentials, ?AuthConfigInterface $config = null): ClientInterface
    {
        if (!$this->credentialsAreValid($credentials)) {
            throw new InvalidCredentialsException(sprintf('Credentials must implement either the %s or %s interfaces', HeaderCredentialsInterface::class, ParameterCredentialsInterface::class));
        }

        if (!$this->credentialsAreConfigured($credentials)) {
            throw new PluginNotConfiguredException('API key is missing');
        }

        // Return cached initialized client if there is one.
        if (!empty($this->initializedClients[$credentials->getKeyName()])) {
            return $this->initializedClients[$credentials->getKeyName()];
        }

        $this->credentials = $credentials;

        if ($credentials instanceof HeaderCredentialsInterface) {
            $this->initializedClients[$credentials->getKeyName()] = $this->getHeaderClient();

            return $this->initializedClients[$credentials->getKeyName()];
        }

        $this->initializedClients[$credentials->getKeyName()] = $this->getParameterClient();

        return $this->initializedClients[$credentials->getKeyName()];
    }

    private function credentialsAreValid(AuthCredentialsInterface $credentials): bool
    {
        return $credentials instanceof HeaderCredentialsInterface || $credentials instanceof ParameterCredentialsInterface;
    }

    /**
     * @param HeaderCredentialsInterface|ParameterCredentialsInterface|AuthCredentialsInterface $credentials
     */
    private function credentialsAreConfigured(AuthCredentialsInterface $credentials): bool
    {
        return !empty($credentials->getApiKey());
    }

    private function getHeaderClient(): ClientInterface
    {
        return new Client(
            [
                'headers' => [$this->credentials->getKeyName() => $this->credentials->getApiKey()],
            ]
        );
    }

    private function getParameterClient(): ClientInterface
    {
        $handler = new HandlerStack();
        $handler->setHandler(new CurlHandler());

        $handler->unshift(
            Middleware::mapRequest(
                function (Request $request) {
                    return $request->withUri(
                        Uri::withQueryValue($request->getUri(), $this->credentials->getKeyName(), $this->credentials->getApiKey())
                    );
                }
            )
        );

        return new Client(
            [
                'handler' => $handler,
            ]
        );
    }
}