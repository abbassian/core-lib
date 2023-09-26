<?php

namespace Autoborna\EmailBundle\Tests\MonitoredEmail\Accessor;

use Autoborna\EmailBundle\MonitoredEmail\Accessor\ConfigAccessor;

class ConfigAccessorTest extends \PHPUnit\Framework\TestCase
{
    protected $config = [
        'imap_path' => 'path',
        'user'      => 'user',
        'host'      => 'host',
        'folder'    => 'folder',
    ];

    /**
     * @testdox All getters return appropriate values
     *
     * @covers \Autoborna\EmailBundle\MonitoredEmail\Accessor\ConfigAccessor::getPath()
     * @covers \Autoborna\EmailBundle\MonitoredEmail\Accessor\ConfigAccessor::getUser()
     * @covers \Autoborna\EmailBundle\MonitoredEmail\Accessor\ConfigAccessor::getHost()
     * @covers \Autoborna\EmailBundle\MonitoredEmail\Accessor\ConfigAccessor::getFolder()
     * @covers \Autoborna\EmailBundle\MonitoredEmail\Accessor\ConfigAccessor::getProperty()
     */
    public function testGetters()
    {
        $configAccessor = new ConfigAccessor($this->config);

        $this->assertEquals($this->config['imap_path'], $configAccessor->getPath());
        $this->assertEquals($this->config['user'], $configAccessor->getUser());
        $this->assertEquals($this->config['host'], $configAccessor->getHost());
        $this->assertEquals($this->config['folder'], $configAccessor->getFolder());
    }

    /**
     * @testdox Key is formatted appropriately
     *
     * @covers \Autoborna\EmailBundle\MonitoredEmail\Accessor\ConfigAccessor::getKey()
     * @covers \Autoborna\EmailBundle\MonitoredEmail\Accessor\ConfigAccessor::getHost()
     * @covers \Autoborna\EmailBundle\MonitoredEmail\Accessor\ConfigAccessor::getFolder()
     * @covers \Autoborna\EmailBundle\MonitoredEmail\Accessor\ConfigAccessor::getProperty()
     */
    public function testKeyIsPathAndUser()
    {
        $configAccessor = new ConfigAccessor($this->config);

        $this->assertEquals('path_user', $configAccessor->getKey());
    }

    /**
     * @testdox Test its considered configured if we have a host and a folder
     *
     * @covers \Autoborna\EmailBundle\MonitoredEmail\Accessor\ConfigAccessor::isConfigured()
     * @covers \Autoborna\EmailBundle\MonitoredEmail\Accessor\ConfigAccessor::getHost()
     * @covers \Autoborna\EmailBundle\MonitoredEmail\Accessor\ConfigAccessor::getFolder()
     * @covers \Autoborna\EmailBundle\MonitoredEmail\Accessor\ConfigAccessor::getProperty()
     */
    public function testIsConfigured()
    {
        $configAccessor = new ConfigAccessor($this->config);

        $this->assertTrue($configAccessor->isConfigured());
    }

    /**
     * @testdox Test its considered not configured if folder is missing
     *
     * @covers \Autoborna\EmailBundle\MonitoredEmail\Accessor\ConfigAccessor::isConfigured()
     * @covers \Autoborna\EmailBundle\MonitoredEmail\Accessor\ConfigAccessor::getHost()
     * @covers \Autoborna\EmailBundle\MonitoredEmail\Accessor\ConfigAccessor::getFolder()
     * @covers \Autoborna\EmailBundle\MonitoredEmail\Accessor\ConfigAccessor::getProperty()
     */
    public function testIsNotConfiguredIfFolderIsMissing()
    {
        $config = $this->config;
        unset($config['folder']);
        $configAccessor = new ConfigAccessor($config);
        $this->assertFalse($configAccessor->isConfigured());
    }

    /**
     * @testdox Test its considered not configured if host is missing
     *
     * @covers \Autoborna\EmailBundle\MonitoredEmail\Accessor\ConfigAccessor::isConfigured()
     * @covers \Autoborna\EmailBundle\MonitoredEmail\Accessor\ConfigAccessor::getHost()
     * @covers \Autoborna\EmailBundle\MonitoredEmail\Accessor\ConfigAccessor::getFolder()
     * @covers \Autoborna\EmailBundle\MonitoredEmail\Accessor\ConfigAccessor::getProperty()
     */
    public function testIsNotConfiguredIfHostIsMissing()
    {
        $config = $this->config;
        unset($config['host']);
        $configAccessor = new ConfigAccessor($config);
        $this->assertFalse($configAccessor->isConfigured());
    }
}
