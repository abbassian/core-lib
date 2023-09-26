<?php

declare(strict_types=1);

namespace Autoborna\MarketplaceBundle\Service;

use GuzzleHttp\ClientInterface;
use Autoborna\CacheBundle\Cache\CacheProvider;
use Autoborna\MarketplaceBundle\DTO\Allowlist as DTOAllowlist;
use Autoborna\MarketplaceBundle\Exception\ApiException;

/**
 * Provides several helper functions to interact with Autoborna's allowlist.
 */
class Allowlist
{
    private Config $config;
    private CacheProvider $cache;
    private ClientInterface $httpClient;
    private ?DTOAllowlist $allowlist = null;

    private const MARKETPLACE_ALLOWLIST_CACHE_KEY = 'marketplace_allowlist';

    public function __construct(Config $config, CacheProvider $cache, ClientInterface $httpClient)
    {
        $this->config     = $config;
        $this->cache      = $cache;
        $this->httpClient = $httpClient;
    }

    public function getAllowList(): ?DTOAllowlist
    {
        if (!empty($this->allowlist)) {
            return $this->allowlist;
        }

        $cache                 = $this->cache->getSimpleCache();
        $cachedAllowlistString = $cache->get(self::MARKETPLACE_ALLOWLIST_CACHE_KEY);

        if (!empty($cachedAllowlistString)) {
            return $this->parseAllowlistJson($cachedAllowlistString);
        }

        if (!empty($this->config->getAllowListUrl())) {
            $response  = $this->httpClient->request('GET', $this->config->getAllowlistUrl());
            $body      = (string) $response->getBody();

            if ($response->getStatusCode() >= 300) {
                throw new ApiException($body, $response->getStatusCode());
            }

            // Cache the allowlist for the given amount of seconds (3600 by default).
            $cache->set(
                self::MARKETPLACE_ALLOWLIST_CACHE_KEY,
                $body,
                $this->config->getAllowlistCacheTtlSeconds()
            );

            return $this->parseAllowlistJson($body);
        }

        return null;
    }

    public function clearCache(): void
    {
        $this->cache->getSimpleCache()->delete(self::MARKETPLACE_ALLOWLIST_CACHE_KEY);
    }

    private function parseAllowlistJson(string $payload): DTOAllowlist
    {
        return DTOAllowlist::fromArray(json_decode($payload, true));
    }
}
