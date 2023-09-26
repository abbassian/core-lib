<?php

namespace Autoborna\EmailBundle\Tests\Swiftmailer\SendGrid\Callback;

use Autoborna\EmailBundle\Swiftmailer\SendGrid\Callback\ResponseItem;
use Autoborna\EmailBundle\Swiftmailer\SendGrid\Exception\ResponseItemException;
use Autoborna\LeadBundle\Entity\DoNotContact;

class ResponseItemTest extends \PHPUnit\Framework\TestCase
{
    public function testFullResponseItem()
    {
        $item = [
            'email'  => 'info@example.com',
            'reason' => 'My reason',
            'event'  => 'bounce',
        ];

        $responseItem = new ResponseItem($item);

        $this->assertSame('info@example.com', $responseItem->getEmail());
        $this->assertSame('My reason', $responseItem->getReason());
        $this->assertSame(DoNotContact::BOUNCED, $responseItem->getDncReason());
    }

    public function testResponseItemWithoutReason()
    {
        $item = [
            'email'  => 'info@example.com',
            'event'  => 'spamreport',
        ];

        $responseItem = new ResponseItem($item);

        $this->assertSame('info@example.com', $responseItem->getEmail());
        $this->assertNull($responseItem->getReason());
        $this->assertSame(DoNotContact::BOUNCED, $responseItem->getDncReason());
    }

    public function testResponseItemWithoutEmail()
    {
        $item = [
            'event'  => 'spamreport',
        ];

        $this->expectException(ResponseItemException::class);
        new ResponseItem($item);
    }
}
