<?php

namespace Autoborna\CoreBundle\Tests\Unit\DependencyInjection\Builder;

use Autoborna\CoreBundle\DependencyInjection\Builder\BundleMetadata;
use PHPUnit\Framework\TestCase;

class BundleMetadataTest extends TestCase
{
    public function testGetters()
    {
        $metadataArray = [
            'isPlugin'          => true,
            'base'              => 'Core',
            'bundle'            => 'CoreBundle',
            'relative'          => 'app/bundles/AutobornaCoreBundle',
            'directory'         => '/var/www/app/bundles/AutobornaCoreBundle',
            'namespace'         => 'Autoborna\\CoreBundle',
            'symfonyBundleName' => 'AutobornaCoreBundle',
            'bundleClass'       => '\\Autoborna\\CoreBundle',
        ];

        $metadata = new BundleMetadata($metadataArray);
        $this->assertSame($metadataArray['directory'], $metadata->getDirectory());
        $this->assertSame($metadataArray['namespace'], $metadata->getNamespace());
        $this->assertSame($metadataArray['bundle'], $metadata->getBaseName());
        $this->assertSame($metadataArray['symfonyBundleName'], $metadata->getBundleName());

        $metadata->setConfig(['foo' => 'bar']);
        $metadata->addPermissionClass('\Foo\Bar');

        $metadataArray['config']                        = ['foo' => 'bar'];
        $metadataArray['permissionClasses']['\Foo\Bar'] = '\Foo\Bar';
        $this->assertEquals($metadataArray, $metadata->toArray());
    }
}
