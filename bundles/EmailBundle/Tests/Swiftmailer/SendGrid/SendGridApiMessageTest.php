<?php

namespace Autoborna\EmailBundle\Tests\Swiftmailer\SendGrid;

use Autoborna\EmailBundle\Swiftmailer\SendGrid\Mail\SendGridMailAttachment;
use Autoborna\EmailBundle\Swiftmailer\SendGrid\Mail\SendGridMailBase;
use Autoborna\EmailBundle\Swiftmailer\SendGrid\Mail\SendGridMailMetadata;
use Autoborna\EmailBundle\Swiftmailer\SendGrid\Mail\SendGridMailPersonalization;
use Autoborna\EmailBundle\Swiftmailer\SendGrid\SendGridApiMessage;
use SendGrid\Mail;

class SendGridApiMessageTest extends \PHPUnit\Framework\TestCase
{
    public function testGetMail()
    {
        $sendGridMailBase = $this->getMockBuilder(SendGridMailBase::class)
            ->disableOriginalConstructor()
            ->getMock();

        $sendGridMailPersonalization = $this->getMockBuilder(SendGridMailPersonalization::class)
            ->disableOriginalConstructor()
            ->getMock();

        $sendGridMailMetadata = $this->getMockBuilder(SendGridMailMetadata::class)
            ->disableOriginalConstructor()
            ->getMock();

        $sendGridMailAttachment = $this->getMockBuilder(SendGridMailAttachment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mail = $this->getMockBuilder(Mail::class)
            ->disableOriginalConstructor()
            ->getMock();

        $message = $this->getMockBuilder(\Swift_Mime_SimpleMessage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $sendGridApiMessage = new SendGridApiMessage($sendGridMailBase, $sendGridMailPersonalization, $sendGridMailMetadata, $sendGridMailAttachment);

        $sendGridMailBase->expects($this->once())
            ->method('getSendGridMail')
            ->with($message)
            ->willReturn($mail);

        $sendGridMailPersonalization->expects($this->once())
            ->method('addPersonalizedDataToMail')
            ->with($mail, $message);

        $sendGridMailMetadata->expects($this->once())
            ->method('addMetadataToMail')
            ->with($mail, $message);

        $sendGridMailAttachment->expects($this->once())
            ->method('addAttachmentsToMail')
            ->with($mail, $message);

        $result = $sendGridApiMessage->getMessage($message);

        $this->assertSame($mail, $result);
    }
}
