<?php

namespace Autoborna\EmailBundle\Tests\Model;

use Autoborna\EmailBundle\Model\TransportType;

class TransportTypeTest extends \PHPUnit\Framework\TestCase
{
    public function testGetTransportTypes()
    {
        $transportType = new TransportType();

        $expected = [
            'autoborna.transport.amazon'       => 'autoborna.email.config.mailer_transport.amazon',
            'autoborna.transport.amazon_api'   => 'autoborna.email.config.mailer_transport.amazon_api',
            'autoborna.transport.elasticemail' => 'autoborna.email.config.mailer_transport.elasticemail',
            'gmail'                         => 'autoborna.email.config.mailer_transport.gmail',
            'autoborna.transport.mandrill'     => 'autoborna.email.config.mailer_transport.mandrill',
            'autoborna.transport.mailjet'      => 'autoborna.email.config.mailer_transport.mailjet',
            'smtp'                          => 'autoborna.email.config.mailer_transport.smtp',
            'autoborna.transport.postmark'     => 'autoborna.email.config.mailer_transport.postmark',
            'autoborna.transport.sendgrid'     => 'autoborna.email.config.mailer_transport.sendgrid',
            'autoborna.transport.pepipost'     => 'autoborna.email.config.mailer_transport.pepipost',
            'autoborna.transport.sendgrid_api' => 'autoborna.email.config.mailer_transport.sendgrid_api',
            'sendmail'                      => 'autoborna.email.config.mailer_transport.sendmail',
            'autoborna.transport.sparkpost'    => 'autoborna.email.config.mailer_transport.sparkpost',
        ];

        $this->assertSame($expected, $transportType->getTransportTypes());
    }

    public function testSmtpService()
    {
        $transportType = new TransportType();

        $expected = '"smtp"';

        $this->assertSame($expected, $transportType->getSmtpService());
    }

    public function testAmazonService()
    {
        $transportType = new TransportType();

        $expected = '"autoborna.transport.amazon","autoborna.transport.amazon_api"';

        $this->assertSame($expected, $transportType->getAmazonService());
    }

    public function testDoNotNeedRegion()
    {
        $transportType = new TransportType();

        $expected = '"autoborna.transport.elasticemail","gmail","autoborna.transport.mandrill","autoborna.transport.mailjet","smtp","autoborna.transport.postmark","autoborna.transport.sendgrid","autoborna.transport.pepipost","autoborna.transport.sendgrid_api","sendmail","autoborna.transport.sparkpost"';

        $this->assertSame($expected, $transportType->getServiceDoNotNeedAmazonRegion());
    }

    public function testMailjetService()
    {
        $transportType = new TransportType();

        $expected = '"autoborna.transport.mailjet"';

        $this->assertSame($expected, $transportType->getMailjetService());
    }

    public function testRequiresLogin()
    {
        $transportType = new TransportType();

        $expected = '"autoborna.transport.mailjet","autoborna.transport.sendgrid","autoborna.transport.pepipost","autoborna.transport.elasticemail","autoborna.transport.amazon","autoborna.transport.amazon_api","autoborna.transport.postmark","gmail"';

        $this->assertSame($expected, $transportType->getServiceRequiresUser());
    }

    public function testDoNotNeedLogin()
    {
        $transportType = new TransportType();

        $expected = '"autoborna.transport.mandrill","autoborna.transport.sendgrid_api","sendmail","autoborna.transport.sparkpost"';

        $this->assertSame($expected, $transportType->getServiceDoNotNeedUser());
    }

    public function testRequiresPassword()
    {
        $transportType = new TransportType();

        $expected = '"autoborna.transport.mailjet","autoborna.transport.sendgrid","autoborna.transport.pepipost","autoborna.transport.elasticemail","autoborna.transport.amazon","autoborna.transport.amazon_api","autoborna.transport.postmark","gmail"';

        $this->assertSame($expected, $transportType->getServiceRequiresPassword());
    }

    public function testDoNotNeedPassword()
    {
        $transportType = new TransportType();

        $expected = '"autoborna.transport.mandrill","autoborna.transport.sendgrid_api","sendmail","autoborna.transport.sparkpost"';

        $this->assertSame($expected, $transportType->getServiceDoNotNeedPassword());
    }

    public function testRequiresApiKey()
    {
        $transportType = new TransportType();

        $expected = '"autoborna.transport.sparkpost","autoborna.transport.mandrill","autoborna.transport.sendgrid_api"';

        $this->assertSame($expected, $transportType->getServiceRequiresApiKey());
    }
}
