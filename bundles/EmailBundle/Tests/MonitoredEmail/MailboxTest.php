<?php

namespace Autoborna\EmailBundle\Tests\MonitoredEmail;

use Autoborna\CoreBundle\Helper\CoreParametersHelper;
use Autoborna\CoreBundle\Helper\PathsHelper;

class MailboxTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructWithDefaultConfig()
    {
        $expected = [
            'host'            => '',
            'port'            => '',
            'password'        => '',
            'user'            => '',
            'encryption'      => '',
            'use_attachments' => false,
        ];

        $parametersHelper = $this->getMockBuilder(CoreParametersHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $pathsHelper = $this->getMockBuilder(PathsHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mailbox = new \Autoborna\EmailBundle\MonitoredEmail\Mailbox($parametersHelper, $pathsHelper);

        $this->assertEquals($expected, $mailbox->getMailboxSettings());
    }

    public function testSettingsForMonitoredEmailWithoutOverride()
    {
        $config = [
            'general' => [
                'address'         => 'foo@bar.com',
                'host'            => 'imap.bar.com',
                'port'            => '993',
                'encryption'      => '/ssl',
                'user'            => 'foo@bar.com',
                'password'        => 'topsecret',
                'use_attachments' => true,
            ],
            'EmailBundle_bounces' => [
                'address'           => null,
                'host'              => null,
                'port'              => '993',
                'encryption'        => '/ssl',
                'user'              => null,
                'password'          => null,
                'override_settings' => 0,
                'folder'            => 'Bounces',
            ],
        ];

        $parametersHelper = $this->getMockBuilder(CoreParametersHelper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $parametersHelper->expects($this->once())
            ->method('get')
            ->will($this->returnValue($config));

        $pathsHelper = $this->getMockBuilder(PathsHelper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $pathsHelper->expects($this->once())
            ->method('getSystemPath')
            ->will($this->returnValue(__DIR__.'/../../../../cache/'));

        $mailbox = new \Autoborna\EmailBundle\MonitoredEmail\Mailbox($parametersHelper, $pathsHelper);

        $settings = $mailbox->getMailboxSettings('EmailBundle', 'bounces');

        $this->assertArrayHasKey('folder', $settings);
        $this->assertEquals('Bounces', $settings['folder']);
        $this->assertEquals('foo@bar.com', $settings['address']);
    }

    public function testSettingsForMonitoredEmailWithOverride()
    {
        $config = [
            'general' => [
                'address'         => 'foo@bar.com',
                'host'            => 'imap.bar.com',
                'port'            => '993',
                'encryption'      => '/ssl',
                'user'            => 'foo@bar.com',
                'password'        => 'topsecret',
                'use_attachments' => true,
            ],
            'EmailBundle_bounces' => [
                'address'           => 'bar@foo.com',
                'host'              => 'imap.foo.com',
                'port'              => '993',
                'encryption'        => '/ssl',
                'user'              => 'bar@foo.com',
                'password'          => 'topsecret',
                'override_settings' => true,
                'folder'            => 'INBOX',
            ],
        ];

        $parametersHelper = $this->getMockBuilder(CoreParametersHelper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $parametersHelper->expects($this->once())
            ->method('get')
            ->will($this->returnValue($config));

        $pathsHelper = $this->getMockBuilder(PathsHelper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $pathsHelper->expects($this->once())
            ->method('getSystemPath')
            ->will($this->returnValue(__DIR__.'/../../../../cache/'));

        $mailbox = new \Autoborna\EmailBundle\MonitoredEmail\Mailbox($parametersHelper, $pathsHelper);

        $settings = $mailbox->getMailboxSettings('EmailBundle', 'bounces');

        $this->assertArrayHasKey('folder', $settings);
        $this->assertEquals('INBOX', $settings['folder']);
        $this->assertEquals('bar@foo.com', $settings['address']);
    }

    public function testUseAttachments()
    {
        // Test undefined $this->settings['use_attachments']
        // will not invoke undefined index error or mkdir error
        $config = [
            'general' => [
                'address'         => 'foo@bar.com',
                'host'            => 'imap.bar.com',
                'port'            => '993',
                'encryption'      => '/ssl',
                'user'            => 'foo@bar.com',
                'password'        => 'topsecret',
            ],
        ];

        $parametersHelper = $this->getMockBuilder(CoreParametersHelper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $parametersHelper->expects($this->once())
            ->method('get')
            ->will($this->returnValue($config));

        $pathsHelper = $this->getMockBuilder(PathsHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        new \Autoborna\EmailBundle\MonitoredEmail\Mailbox($parametersHelper, $pathsHelper);

        // Test $this->settings['use_attachments'] == true
        // dir creation is not failing
        $config = [
            'general' => [
                'address'         => 'foo@bar.com',
                'host'            => 'imap.bar.com',
                'port'            => '993',
                'encryption'      => '/ssl',
                'user'            => 'foo@bar.com',
                'password'        => 'topsecret',
                'use_attachments' => true,
            ],
        ];

        $parametersHelper = $this->getMockBuilder(CoreParametersHelper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $parametersHelper->expects($this->once())
            ->method('get')
            ->will($this->returnValue($config));

        $pathsHelper = $this->getMockBuilder(PathsHelper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $pathsHelper->expects($this->once())
            ->method('getSystemPath')
            ->with('tmp', true)
            ->will($this->returnValue(__DIR__.'/../../../../cache/tmp'));

        new \Autoborna\EmailBundle\MonitoredEmail\Mailbox($parametersHelper, $pathsHelper);
    }
}
