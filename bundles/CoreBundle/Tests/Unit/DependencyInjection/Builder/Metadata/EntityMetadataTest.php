<?php

namespace Autoborna\CoreBundle\Tests\Unit\DependencyInjection\Builder\Metadata;

use Autoborna\CoreBundle\DependencyInjection\Builder\BundleMetadata;
use Autoborna\CoreBundle\DependencyInjection\Builder\Metadata\EntityMetadata;
use PHPUnit\Framework\TestCase;

class EntityMetadataTest extends TestCase
{
    /**
     * @var BundleMetadata
     */
    private $metadata;

    protected function setUp(): void
    {
        $metadataArray = [
            'isPlugin'          => true,
            'base'              => 'Core',
            'bundle'            => 'CoreBundle',
            'relative'          => 'app/bundles/AutobornaCoreBundle',
            'directory'         => __DIR__.'/../../../../../',
            'namespace'         => 'Autoborna\\CoreBundle',
            'symfonyBundleName' => 'AutobornaCoreBundle',
            'bundleClass'       => '\\Autoborna\\CoreBundle',
        ];

        $this->metadata = new BundleMetadata($metadataArray);
    }

    public function testOrmAndSerializerConfigsFound()
    {
        $entityMetadata = new EntityMetadata($this->metadata);
        $entityMetadata->build();

        $this->assertEquals(
            [
                'dir'       => 'Entity',
                'type'      => 'staticphp',
                'prefix'    => 'Autoborna\\CoreBundle\\Entity',
                'mapping'   => true,
                'is_bundle' => true,
            ],
            $entityMetadata->getOrmConfig()
        );

        $this->assertEquals(
            [
                'namespace_prefix' => 'Autoborna\\CoreBundle\\Entity',
                'path'             => '@AutobornaCoreBundle/Entity',
            ],
            $entityMetadata->getSerializerConfig()
        );
    }
}
