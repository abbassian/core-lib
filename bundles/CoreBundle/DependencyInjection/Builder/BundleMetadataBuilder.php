<?php

namespace Autoborna\CoreBundle\DependencyInjection\Builder;

use Autoborna\CoreBundle\DependencyInjection\Builder\Metadata\ConfigMetadata;
use Autoborna\CoreBundle\DependencyInjection\Builder\Metadata\EntityMetadata;
use Autoborna\CoreBundle\DependencyInjection\Builder\Metadata\PermissionClassMetadata;

class BundleMetadataBuilder
{
    /**
     * @var array
     */
    private $paths;

    /**
     * @var array
     */
    private $symfonyBundles;

    /**
     * @var array
     */
    private $ipLookupServices = [];

    /**
     * @var array
     */
    private $ormConfig = [];

    /**
     * @var array
     */
    private $serializerConfig = [];

    /**
     * @var array
     */
    private $pluginMetadata = [];

    /**
     * @var array
     */
    private $coreMetadata = [];

    public function __construct(array $symfonyBundles, array $paths)
    {
        $this->paths          = $paths;
        $this->symfonyBundles = $symfonyBundles;

        $this->buildMetadata();
    }

    public function getCoreBundleMetadata(): array
    {
        return $this->coreMetadata;
    }

    public function getPluginMetadata(): array
    {
        return $this->pluginMetadata;
    }

    public function getIpLookupServices(): array
    {
        return $this->ipLookupServices;
    }

    public function getOrmConfig(): array
    {
        return $this->ormConfig;
    }

    public function getSerializerConfig(): array
    {
        return $this->serializerConfig;
    }

    private function buildMetadata(): void
    {
        foreach ($this->symfonyBundles as $symfonyBundle => $namespace) {
            // Plugin
            if (false !== strpos($namespace, 'AutobornaPlugin\\')) {
                $this->pluginMetadata[$symfonyBundle] = $this->buildPluginMetadata($namespace, $symfonyBundle);

                continue;
            }

            // Core bundle
            if (false !== strpos($namespace, 'Autoborna\\')) {
                $this->coreMetadata[$symfonyBundle] = $this->buildCoreMetadata($namespace, $symfonyBundle);

                continue;
            }

            // Otherwise not a Autoborna bundle so ignore
        }

        // Make CoreBundle the first in the core bundle list
        if (!isset($this->coreMetadata['AutobornaCoreBundle'])) {
            // Not always set for tests
            return;
        }

        $coreBundle = $this->coreMetadata['AutobornaCoreBundle'];
        unset($this->coreMetadata['AutobornaCoreBundle']);
        $this->coreMetadata = array_merge(['AutobornaCoreBundle' => $coreBundle], $this->coreMetadata);
    }

    private function buildPluginMetadata(string $namespace, string $symfonyBundle): array
    {
        $relativePath  = $this->paths['plugins'].'/'.$symfonyBundle;
        $metadataArray = $this->getMetadata(true, $namespace, $symfonyBundle, $symfonyBundle, $relativePath);

        $metadata = new BundleMetadata($metadataArray);
        $this->completMetadata($metadata);

        return $metadata->toArray();
    }

    private function buildCoreMetadata(string $namespace, string $symfonyBundle): array
    {
        $bundleName    = str_replace('Autoborna', '', $symfonyBundle);
        $relativePath  = $this->paths['bundles'].'/'.$bundleName;
        $metadataArray = $this->getMetadata(false, $namespace, $symfonyBundle, $bundleName, $relativePath);

        $metadata = new BundleMetadata($metadataArray);
        $this->completMetadata($metadata);

        return $metadata->toArray();
    }

    private function getMetadata(bool $isPlugin, string $namespace, string $symfonyBundle, string $bundleName, string $relativePath): array
    {
        return [
            'isPlugin'          => $isPlugin,
            'base'              => str_replace('Bundle', '', $bundleName),
            'bundle'            => $bundleName,
            'relative'          => $relativePath,
            'directory'         => realpath($this->paths['root'].'/'.$relativePath),
            'namespace'         => preg_replace('#\\\[^\\\]*$#', '', $namespace),
            'symfonyBundleName' => $symfonyBundle,
            'bundleClass'       => $namespace,
        ];
    }

    private function completMetadata(BundleMetadata $metadata): void
    {
        $configParser = new ConfigMetadata($metadata);
        $configParser->build();

        if ($foundIpLookupServices = $configParser->getIpLookupServices()) {
            $this->ipLookupServices = array_merge($foundIpLookupServices, $this->ipLookupServices);
        }

        (new PermissionClassMetadata($metadata))->build();

        $this->buildMappings($metadata);
    }

    private function buildMappings(BundleMetadata $metadata): void
    {
        $mappingParser = new EntityMetadata($metadata);
        $mappingParser->build();

        $bundleName = $metadata->getBundleName();

        if ($ormMappings = $mappingParser->getOrmConfig()) {
            $this->ormConfig[$bundleName] = $ormMappings;
        }

        if ($serializerConfig = $mappingParser->getSerializerConfig()) {
            $this->serializerConfig[$bundleName] = $serializerConfig;
        }
    }
}
