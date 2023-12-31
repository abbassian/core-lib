<?php

namespace Autoborna\EmailBundle\Tests\MonitoredEmail\Processor;

use Autoborna\EmailBundle\MonitoredEmail\Processor\Address;

class AddressTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @testdox Test that an email header with email addresses are parsed into array
     *
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Processor\Address::parseList()
     */
    public function testArrayOfAddressesAreReturnedFromEmailHeader()
    {
        $results = Address::parseList('<user@test.com>,<user2@test.com>');

        $this->assertEquals(
            [
                'user@test.com'  => null,
                'user2@test.com' => null,
            ],
            $results
        );
    }

    /**
     * @testdox Obtain hash ID from a special formatted email address
     *
     * @covers  \Autoborna\EmailBundle\MonitoredEmail\Processor\Address::parseList()
     */
    public function testStatHashIsParsedFromEmail()
    {
        $hash = Address::parseAddressForStatHash('hello+bounce_123abc@test.com');

        $this->assertEquals('123abc', $hash);
    }
}
