<?php

namespace Autoborna\CoreBundle\Helper\Update\Github;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Autoborna\CoreBundle\Helper\Update\Exception\LatestVersionSupportedException;
use Autoborna\CoreBundle\Helper\Update\Exception\MetadataNotFoundException;
use Autoborna\CoreBundle\Helper\Update\Exception\UpdatePackageNotFoundException;
use Autoborna\CoreBundle\Release\Metadata;

class ReleaseParser
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @throws LatestVersionSupportedException
     * @throws UpdatePackageNotFoundException
     */
    public function getLatestSupportedRelease(array $releases, string $autobornaVersion, string $allowedStability): Release
    {
        foreach ($releases as $release) {
            try {
                $metadata = $this->getMetadata($release['html_url']);
            } catch (MetadataNotFoundException $exception) {
                continue;
            }

            if (
                ('stable' === $allowedStability && 'stable' !== $metadata->getStability()) ||
                ('stable' !== $metadata->getStability() && version_compare($allowedStability, $metadata->getStability(), 'gt'))
            ) {
                // This Autoborna does support the given release's stability so continue
                continue;
            }

            if (version_compare($autobornaVersion, $metadata->getMinSupportedAutobornaVersion(), 'lt')) {
                // This Autoborna version cannot be upgraded to the given release so continue
                continue;
            }

            if (version_compare($autobornaVersion, $metadata->getVersion(), 'ge')) {
                // This Autoborna version is the same as the given release so continue
                continue;
            }

            return new Release($release, $metadata);
        }

        // No compatible release found
        throw new LatestVersionSupportedException();
    }

    /**
     * @throws MetadataNotFoundException
     */
    private function getMetadata(string $releaseUrl): Metadata
    {
        // Convert the release URL to a repository URL to fetch the contents of the release_metadata.json file
        // https://github.com/autoborna/autoborna/releases/tag/3.0.0-beta
        // https://raw.githubusercontent.com/autoborna/autoborna/3.0.0-beta

        $contentUrl  = str_replace(['github.com', 'releases/tag/'], ['raw.githubusercontent.com', ''], $releaseUrl);
        $metadataUrl = $contentUrl.'/app/release_metadata.json';

        try {
            $response = $this->client->request('GET', $metadataUrl);
            if (200 !== $response->getStatusCode()) {
                // A metadata file could not be found so let's assume that a release prior to the new upgrade
                // system has been encountered
                throw new MetadataNotFoundException();
            }

            $contents = $response->getBody()->getContents();
            $metadata = json_decode($contents, true);
            if (!$metadata || !isset($metadata['version'])) {
                // The contents do not match what is expected
                throw new MetadataNotFoundException();
            }

            return new Metadata($metadata);
        } catch (GuzzleException $exception) {
            throw new MetadataNotFoundException();
        }
    }
}
