<?php

namespace Autoborna\EmailBundle\Tests\MonitoredEmail\Processor\Unsubscription;

use Autoborna\EmailBundle\MonitoredEmail\Exception\UnsubscriptionNotFound;
use Autoborna\EmailBundle\MonitoredEmail\Message;
use Autoborna\EmailBundle\MonitoredEmail\Processor\Unsubscription\Parser;
use Autoborna\EmailBundle\MonitoredEmail\Processor\Unsubscription\UnsubscribedEmail;

class ParserTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @testdox Test that an email is found inside a feedback report
     *
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Processor\Unsubscription\Parser::parse()
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Processor\Unsubscription\UnsubscribedEmail::getContactEmail()
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Processor\Unsubscription\UnsubscribedEmail::getUnsubscriptionAddress()
     */
    public function testThatReplyIsDetectedThroughTrackingPixel()
    {
        $message              = new Message();
        $message->fromAddress = 'hello@hello.com';
        $message->to          = [
            'test+unsubscribe@test.com' => 'Test Test',
        ];

        $parser = new Parser($message);

        $unsubscribedEmail = $parser->parse();
        $this->assertInstanceOf(UnsubscribedEmail::class, $unsubscribedEmail);

        $this->assertEquals('hello@hello.com', $unsubscribedEmail->getContactEmail());
        $this->assertEquals('test+unsubscribe@test.com', $unsubscribedEmail->getUnsubscriptionAddress());
    }

    /**
     * @testdox Test that an exeption is thrown if a unsubscription email is not found
     *
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Processor\Unsubscription\Parser::parse()
     */
    public function testExceptionIsThrownWithUnsubscribeNotFound()
    {
        $this->expectException(UnsubscriptionNotFound::class);

        $message = new Message();
        $parser  = new Parser($message);

        $parser->parse();
    }
}
