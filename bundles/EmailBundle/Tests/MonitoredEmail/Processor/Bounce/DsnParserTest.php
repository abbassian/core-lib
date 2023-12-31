<?php

namespace Autoborna\EmailBundle\Tests\MonitoredEmail\Processor\Bounce;

use Autoborna\EmailBundle\MonitoredEmail\Exception\BounceNotFound;
use Autoborna\EmailBundle\MonitoredEmail\Message;
use Autoborna\EmailBundle\MonitoredEmail\Processor\Bounce\BouncedEmail;
use Autoborna\EmailBundle\MonitoredEmail\Processor\Bounce\Definition\Category;
use Autoborna\EmailBundle\MonitoredEmail\Processor\Bounce\Definition\Type;
use Autoborna\EmailBundle\MonitoredEmail\Processor\Bounce\DsnParser;

class DsnParserTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @testdox Test that a BouncedEmail is returned from a dsn report
     *
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Processor\Bounce\DsnParser::getBounce()
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Processor\Bounce\DsnParser::parse()
     */
    public function testBouncedEmailIsReturnedFromParsedDsnReport()
    {
        $message            = new Message();
        $message->dsnReport = <<<'DSN'
Original-Recipient: sdfgsdfg@seznan.cz
Final-Recipient: rfc822;sdfgsdfg@seznan.cz
Action: failed
Status: 5.4.4
Diagnostic-Code: DNS; Host not found
DSN;
        $parser = new DsnParser();
        $bounce = $parser->getBounce($message);

        $this->assertInstanceOf(BouncedEmail::class, $bounce);
        $this->assertEquals('sdfgsdfg@seznan.cz', $bounce->getContactEmail());
        $this->assertEquals(Category::DNS_UNKNOWN, $bounce->getRuleCategory());
        $this->assertEquals(Type::HARD, $bounce->getType());
        $this->assertTrue($bounce->isFinal());
    }

    /**
     * @testdox Test a Postfix BouncedEmail is returned from a dsn report
     *
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Processor\Bounce\DsnParser::getBounce()
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Processor\Bounce\DsnParser::parse()
     */
    public function testPostfixBouncedEmailIsReturnedFromParsedDsnReport()
    {
        $message            = new Message();
        $message->dsnReport = <<<'DSN'
Final-Recipient: rfc822; aaaaaaaaaaaaa@yoursite.com
Original-Recipient: rfc822;aaaaaaaaaaaaa@yoursite.com
Action: failed
Status: 5.1.1
Remote-MTA: dns; mail-server.yoursite.com
Diagnostic-Code: smtp; 550 5.1.1 <aaaaaaaaaaaaa@yoursite.com> User doesn't
    exist: aaaaaaaaaaaaa@yoursite.com
DSN;

        $parser = new DsnParser();
        $bounce = $parser->getBounce($message);

        $this->assertInstanceOf(BouncedEmail::class, $bounce);
        $this->assertEquals('aaaaaaaaaaaaa@yoursite.com', $bounce->getContactEmail());
        $this->assertEquals(Category::UNKNOWN, $bounce->getRuleCategory());
        $this->assertEquals(Type::HARD, $bounce->getType());
        $this->assertTrue($bounce->isFinal());
    }

    /**
     * @testdox Test that an exception is thrown if a bounce cannot be found in a dsn report
     *
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Processor\Bounce\DsnParser::getBounce()
     */
    public function testBounceNotFoundFromBadDsnReport()
    {
        $this->expectException(BounceNotFound::class);

        $message            = new Message();
        $message->dsnReport = 'BAD';
        $parser             = new DsnParser();
        $parser->getBounce($message);
    }
}
