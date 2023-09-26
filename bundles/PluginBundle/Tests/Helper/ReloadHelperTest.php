<?php

namespace Autoborna\PluginBundle\Tests\Helper;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\Mapping\ClassMetadata;
use Autoborna\CoreBundle\Factory\AutobornaFactory;
use Autoborna\PluginBundle\Entity\Plugin;
use Autoborna\PluginBundle\Event\PluginInstallEvent;
use Autoborna\PluginBundle\Event\PluginUpdateEvent;
use Autoborna\PluginBundle\Helper\ReloadHelper;
use Autoborna\PluginBundle\PluginEvents;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ReloadHelperTest extends \PHPUnit\Framework\TestCase
{
    private $factoryMock;

    /**
     * @var ReloadHelper
     */
    private $helper;

    /**
     * @var array
     */
    private $sampleAllPlugins = [];

    /**
     * @var array
     */
    private $sampleMetaData = [];

    /**
     * @var array
     */
    private $sampleSchemas = [];

    /**
     * @var MockObject&EventDispatcherInterface
     */
    private $eventDispatcher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->factoryMock     = $this->createMock(AutobornaFactory::class);
        $this->helper          = new ReloadHelper($this->eventDispatcher, $this->factoryMock);

        $this->sampleMetaData = [
            'AutobornaPlugin\AutobornaZapierBundle' => [$this->createMock(ClassMetadata::class)],
            'AutobornaPlugin\AutobornaCitrixBundle' => [$this->createMock(ClassMetadata::class)],
        ];

        $sampleSchema = $this->createMock(Schema::class);
        $sampleSchema->method('getTables')
                ->willReturn([]);

        $this->sampleSchemas = [
            'AutobornaPlugin\AutobornaZapierBundle' => $sampleSchema,
            'AutobornaPlugin\AutobornaCitrixBundle' => $sampleSchema,
        ];

        $this->sampleAllPlugins = [
            'AutobornaZapierBundle' => [
                'isPlugin'          => true,
                'base'              => 'AutobornaZapier',
                'bundle'            => 'AutobornaZapierBundle',
                'namespace'         => 'AutobornaPlugin\AutobornaZapierBundle',
                'symfonyBundleName' => 'AutobornaZapierBundle',
                'bundleClass'       => 'Autoborna\PluginBundle\Tests\Helper\PluginBundleBaseStub',
                'permissionClasses' => [],
                'relative'          => 'plugins/AutobornaZapierBundle',
                'directory'         => '/Users/jan/dev/autoborna/plugins/AutobornaZapierBundle',
                'config'            => [
                    'name'        => 'Zapier Integration',
                    'description' => 'Zapier lets you connect Autoborna with 1100+ other apps',
                    'version'     => '1.0',
                    'author'      => 'Autoborna',
                ],
            ],
            'AutobornaCitrixBundle' => [
                'isPlugin'          => true,
                'base'              => 'AutobornaCitrix',
                'bundle'            => 'AutobornaCitrixBundle',
                'namespace'         => 'AutobornaPlugin\AutobornaCitrixBundle',
                'symfonyBundleName' => 'AutobornaCitrixBundle',
                'bundleClass'       => 'Autoborna\PluginBundle\Tests\Helper\PluginBundleBaseStub',
                'permissionClasses' => [],
                'relative'          => 'plugins/AutobornaCitrixBundle',
                'directory'         => '/Users/jan/dev/autoborna/plugins/AutobornaCitrixBundle',
                'config'            => [
                    'name'        => 'Citrix',
                    'description' => 'Enables integration with Autoborna supported Citrix collaboration products.',
                    'version'     => '1.0',
                    'author'      => 'Autoborna',
                    'routes'      => [
                        'public' => [
                            'autoborna_citrix_proxy' => [
                                'path'       => '/citrix/proxy',
                                'controller' => 'AutobornaCitrixBundle:Public:proxy',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    public function testDisableMissingPlugins()
    {
        $sampleInstalledPlugins = [
            'AutobornaZapierBundle'  => $this->createSampleZapierPlugin(),
            'AutobornaHappierBundle' => $this->createSampleHappierPlugin(),
        ];

        $disabledPlugins = $this->helper->disableMissingPlugins($this->sampleAllPlugins, $sampleInstalledPlugins);

        $this->assertEquals(1, count($disabledPlugins));
        $this->assertEquals('Happier Integration', $disabledPlugins['AutobornaHappierBundle']->getName());
        $this->assertTrue($disabledPlugins['AutobornaHappierBundle']->isMissing());
    }

    public function testEnableFoundPlugins()
    {
        $zapierPlugin = $this->createSampleZapierPlugin();
        $zapierPlugin->setIsMissing(true);
        $sampleInstalledPlugins = [
            'AutobornaZapierBundle' => $zapierPlugin,
            'AutobornaCitrixBundle' => $this->createSampleCitrixPlugin(),
        ];

        $enabledPlugins = $this->helper->enableFoundPlugins($this->sampleAllPlugins, $sampleInstalledPlugins);

        $this->assertEquals(1, count($enabledPlugins));
        $this->assertEquals('Zapier Integration', $enabledPlugins['AutobornaZapierBundle']->getName());
        $this->assertFalse($enabledPlugins['AutobornaZapierBundle']->isMissing());
    }

    public function testUpdatePlugins()
    {
        $this->sampleAllPlugins['AutobornaZapierBundle']['config']['version']     = '1.0.1';
        $this->sampleAllPlugins['AutobornaZapierBundle']['config']['description'] = 'Updated description';
        $sampleInstalledPlugins                                                = [
            'AutobornaZapierBundle'  => $this->createSampleZapierPlugin(),
            'AutobornaCitrixBundle'  => $this->createSampleCitrixPlugin(),
            'AutobornaHappierBundle' => $this->createSampleHappierPlugin(),
        ];
        $plugin = $this->createSampleZapierPlugin();
        $plugin->setVersion('1.0.1');
        $plugin->setDescription('Updated description');
        $event = new PluginUpdateEvent($plugin, '1.0');
        $this->eventDispatcher->expects($this->once())->method('dispatch')->with($event, PluginEvents::ON_PLUGIN_UPDATE);
        $updatedPlugins = $this->helper->updatePlugins($this->sampleAllPlugins, $sampleInstalledPlugins, $this->sampleMetaData, $this->sampleSchemas);

        $this->assertEquals(1, count($updatedPlugins));
        $this->assertEquals('Zapier Integration', $updatedPlugins['AutobornaZapierBundle']->getName());
        $this->assertEquals('1.0.1', $updatedPlugins['AutobornaZapierBundle']->getVersion());
        $this->assertEquals('Updated description', $updatedPlugins['AutobornaZapierBundle']->getDescription());
    }

    public function testInstallPlugins()
    {
        $sampleInstalledPlugins = [
            'AutobornaCitrixBundle'  => $this->createSampleCitrixPlugin(),
            'AutobornaHappierBundle' => $this->createSampleHappierPlugin(),
        ];
        $event = new PluginInstallEvent($this->createSampleZapierPlugin());
        $this->eventDispatcher->expects($this->once())->method('dispatch')->with($event, PluginEvents::ON_PLUGIN_INSTALL);

        $installedPlugins = $this->helper->installPlugins($this->sampleAllPlugins, $sampleInstalledPlugins, $this->sampleMetaData, $this->sampleSchemas);

        $this->assertEquals(1, count($installedPlugins));
        $this->assertEquals('Zapier Integration', $installedPlugins['AutobornaZapierBundle']->getName());
        $this->assertEquals('1.0', $installedPlugins['AutobornaZapierBundle']->getVersion());
        $this->assertEquals('AutobornaZapierBundle', $installedPlugins['AutobornaZapierBundle']->getBundle());
        $this->assertEquals('Autoborna', $installedPlugins['AutobornaZapierBundle']->getAuthor());
        $this->assertEquals('Zapier lets you connect Autoborna with 1100+ other apps', $installedPlugins['AutobornaZapierBundle']->getDescription());
        $this->assertFalse($installedPlugins['AutobornaZapierBundle']->isMissing());
    }

    private function createSampleZapierPlugin()
    {
        $plugin = new Plugin();
        $plugin->setName('Zapier Integration');
        $plugin->setDescription('Zapier lets you connect Autoborna with 1100+ other apps');
        $plugin->isMissing(false);
        $plugin->setBundle('AutobornaZapierBundle');
        $plugin->setVersion('1.0');
        $plugin->setAuthor('Autoborna');

        return $plugin;
    }

    private function createSampleCitrixPlugin()
    {
        $plugin = new Plugin();
        $plugin->setName('Citrix');
        $plugin->setDescription('Enables integration with Autoborna supported Citrix collaboration products.');
        $plugin->isMissing(false);
        $plugin->setBundle('AutobornaCitrixBundle');
        $plugin->setVersion('1.0');
        $plugin->setAuthor('Autoborna');

        return $plugin;
    }

    private function createSampleHappierPlugin()
    {
        $plugin = new Plugin();
        $plugin->setName('Happier Integration');
        $plugin->setDescription('Happier lets you connect Autoborna with 1100+ other apps');
        $plugin->isMissing(false);
        $plugin->setBundle('AutobornaHappierBundle');
        $plugin->setVersion('1.0');
        $plugin->setAuthor('Autoborna');

        return $plugin;
    }
}
