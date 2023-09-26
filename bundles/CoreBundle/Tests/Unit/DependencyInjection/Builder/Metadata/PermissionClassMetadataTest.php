<?php

namespace Autoborna\CoreBundle\Tests\Unit\DependencyInjection\Builder\Metadata;

use Autoborna\AssetBundle\Security\Permissions\AssetPermissions;
use Autoborna\CoreBundle\DependencyInjection\Builder\BundleMetadata;
use Autoborna\CoreBundle\DependencyInjection\Builder\Metadata\PermissionClassMetadata;
use Autoborna\CoreBundle\Security\Permissions\SystemPermissions;
use PHPUnit\Framework\TestCase;

class PermissionClassMetadataTest extends TestCase
{
    public function testPermissionsFound()
    {
        $metadataArray = [
            'isPlugin'          => false,
            'base'              => 'Core',
            'bundle'            => 'CoreBundle',
            'relative'          => 'app/bundles/AutobornaCoreBundle',
            'directory'         => __DIR__.'/../../../../../',
            'namespace'         => 'Autoborna\\CoreBundle',
            'symfonyBundleName' => 'AutobornaCoreBundle',
            'bundleClass'       => '\\Autoborna\\CoreBundle',
        ];

        $metadata                = new BundleMetadata($metadataArray);
        $permissionClassMetadata = new PermissionClassMetadata($metadata);
        $permissionClassMetadata->build();

        $this->assertTrue(isset($metadata->toArray()['permissionClasses'][SystemPermissions::class]));
        $this->assertCount(1, $metadata->toArray()['permissionClasses']);
    }

    public function testCompatibilityWithPermissionServices()
    {
        $metadataArray = [
            'isPlugin'          => false,
            'base'              => 'Asset',
            'bundle'            => 'AssetBundle',
            'relative'          => 'app/bundles/AutobornaAssetBundle',
            'directory'         => __DIR__.'/../../../../../../AssetBundle',
            'namespace'         => 'Autoborna\\AssetBundle',
            'symfonyBundleName' => 'AutobornaAssetBundle',
            'bundleClass'       => '\\Autoborna\\AssetBundle',
        ];

        $metadata                = new BundleMetadata($metadataArray);
        $permissionClassMetadata = new PermissionClassMetadata($metadata);
        $permissionClassMetadata->build();

        $this->assertTrue(isset($metadata->toArray()['permissionClasses'][AssetPermissions::class]));
    }
}
