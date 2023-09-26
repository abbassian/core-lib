<?php

namespace Autoborna\CoreBundle\Tests\Unit\DependencyInjection\Builder;

use Autoborna\CoreBundle\DependencyInjection\Builder\BundleMetadataBuilder;
use Autoborna\CoreBundle\Security\Permissions\SystemPermissions;
use AutobornaPlugin\AutobornaFocusBundle\Security\Permissions\FocusPermissions;
use PHPUnit\Framework\TestCase;

class BundleMetadataBuilderTest extends TestCase
{
    /**
     * @var array
     */
    private $paths;

    protected function setUp(): void
    {
        // Used in paths_helper
        $root = __DIR__.'/../../../../../../../app';

        /** @var array $paths */
        include __DIR__.'/../../../../../../config/paths_helper.php';

        if (!isset($paths)) {
            throw new \Exception('$paths is not set');
        }

        $this->paths = $paths;
    }

    public function testCoreBundleMetadataLoaded()
    {
        $bundles = ['AutobornaCoreBundle' => 'Autoborna\CoreBundle\AutobornaCoreBundle'];

        $builder  = new BundleMetadataBuilder($bundles, $this->paths);
        $metadata = $builder->getCoreBundleMetadata();

        $this->assertEquals([], $builder->getPluginMetadata());
        $this->assertTrue(isset($metadata['AutobornaCoreBundle']));

        $bundleMetadata = $metadata['AutobornaCoreBundle'];

        $this->assertFalse($bundleMetadata['isPlugin']);
        $this->assertEquals('Core', $bundleMetadata['base']);
        $this->assertEquals('CoreBundle', $bundleMetadata['bundle']);
        $this->assertEquals('AutobornaCoreBundle', $bundleMetadata['symfonyBundleName']);
        $this->assertEquals('app/bundles/CoreBundle', $bundleMetadata['relative']);
        $this->assertEquals(realpath($this->paths['root']).'/app/bundles/CoreBundle', $bundleMetadata['directory']);
        $this->assertEquals('Autoborna\CoreBundle', $bundleMetadata['namespace']);
        $this->assertEquals('Autoborna\CoreBundle\AutobornaCoreBundle', $bundleMetadata['bundleClass']);
        $this->assertTrue(isset($bundleMetadata['permissionClasses']));
        $this->assertTrue(isset($bundleMetadata['permissionClasses'][SystemPermissions::class]));
        $this->assertTrue(isset($bundleMetadata['config']));
        $this->assertTrue(isset($bundleMetadata['config']['routes']));
    }

    public function testPluginMetadataLoaded()
    {
        $bundles = ['AutobornaFocusBundle' => 'AutobornaPlugin\AutobornaFocusBundle\AutobornaFocusBundle'];

        $builder  = new BundleMetadataBuilder($bundles, $this->paths);
        $metadata = $builder->getPluginMetadata();

        $this->assertEquals([], $builder->getCoreBundleMetadata());
        $this->assertTrue(isset($metadata['AutobornaFocusBundle']));
        $bundleMetadata = $metadata['AutobornaFocusBundle'];

        $this->assertTrue($bundleMetadata['isPlugin']);
        $this->assertEquals('AutobornaFocus', $bundleMetadata['base']);
        $this->assertEquals('AutobornaFocusBundle', $bundleMetadata['bundle']);
        $this->assertEquals('AutobornaFocusBundle', $bundleMetadata['symfonyBundleName']);
        $this->assertEquals('plugins/AutobornaFocusBundle', $bundleMetadata['relative']);
        $this->assertEquals(realpath($this->paths['root']).'/plugins/AutobornaFocusBundle', $bundleMetadata['directory']);
        $this->assertEquals('AutobornaPlugin\AutobornaFocusBundle', $bundleMetadata['namespace']);
        $this->assertEquals('AutobornaPlugin\AutobornaFocusBundle\AutobornaFocusBundle', $bundleMetadata['bundleClass']);
        $this->assertTrue(isset($bundleMetadata['permissionClasses']));
        $this->assertTrue(isset($bundleMetadata['permissionClasses'][FocusPermissions::class]));
        $this->assertTrue(isset($bundleMetadata['config']));
        $this->assertTrue(isset($bundleMetadata['config']['routes']));
    }

    public function testSymfonyBundleIgnored()
    {
        $bundles = ['FooBarBundle' => 'Foo\Bar\BarBundle'];

        $builder = new BundleMetadataBuilder($bundles, $this->paths);
        $this->assertEquals([], $builder->getCoreBundleMetadata());
        $this->assertEquals([], $builder->getPluginMetadata());
    }
}
