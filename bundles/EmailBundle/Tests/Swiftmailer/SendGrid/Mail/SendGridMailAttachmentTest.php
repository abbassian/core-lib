<?php

namespace Autoborna\EmailBundle\Tests\Swiftmailer\SendGrid\Mail;

use Autoborna\EmailBundle\Swiftmailer\Message\AutobornaMessage;
use Autoborna\EmailBundle\Swiftmailer\SendGrid\Mail\SendGridMailAttachment;
use SendGrid\Mail;

class SendGridMailAttachmentTest extends \PHPUnit\Framework\TestCase
{
    public function testNotAutobornaMessageWithAttachment(): void
    {
        $sendGridMailAttachment = new SendGridMailAttachment();

        $message = $this->getMockBuilder(\Swift_Mime_SimpleMessage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $message->expects($this->exactly(2))->method('getChildren')->will($this->onConsecutiveCalls([
            new \Swift_Attachment('This is the plain text attachment.', 'hello.txt', 'text/plain'),
        ], [
            new \Swift_Attachment('This is the plain text attachment.', 'hello.txt', 'text/plain'),
        ]));

        $mail = $this->getMockBuilder(Mail::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mail->expects($this->once())
            ->method('addAttachment');

        $sendGridMailAttachment->addAttachmentsToMail($mail, $message);
    }

    public function testNotAutobornaMessageWithoutAttachment(): void
    {
        $sendGridMailAttachment = new SendGridMailAttachment();

        $message = $this->getMockBuilder(\Swift_Mime_SimpleMessage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mail = $this->getMockBuilder(Mail::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mail->expects($this->never())
            ->method('addAttachment');

        $sendGridMailAttachment->addAttachmentsToMail($mail, $message);
    }

    public function testNoAttachment()
    {
        $sendGridMailAttachment = new SendGridMailAttachment();

        $message = $this->getMockBuilder(AutobornaMessage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mail = $this->getMockBuilder(Mail::class)
            ->disableOriginalConstructor()
            ->getMock();

        $message->expects($this->once())
            ->method('getAttachments')
            ->willReturn([]);

        $mail->expects($this->never())
            ->method('addAttachment');

        $sendGridMailAttachment->addAttachmentsToMail($mail, $message);
    }
}
