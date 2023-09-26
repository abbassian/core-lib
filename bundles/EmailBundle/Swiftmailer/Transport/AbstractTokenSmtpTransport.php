<?php

namespace Autoborna\EmailBundle\Swiftmailer\Transport;

use Autoborna\EmailBundle\Swiftmailer\Message\AutobornaMessage;

/**
 * Class AbstractBatchTransport.
 */
abstract class AbstractTokenSmtpTransport extends \Swift_SmtpTransport implements TokenTransportInterface
{
    /**
     * @var \Swift_Mime_SimpleMessage
     */
    protected $message;

    /**
     * Do whatever is necessary to $this->message in order to deliver a batched payload. i.e. add custom headers, etc.
     */
    abstract protected function prepareMessage();

    /**
     * @param null $failedRecipients
     *
     * @return int
     *
     * @throws \Exception
     */
    public function send(\Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        $this->message = $message;

        $this->prepareMessage();

        return parent::send($message, $failedRecipients);
    }

    /**
     * Get the metadata from a AutobornaMessage.
     *
     * @return array
     */
    public function getMetadata()
    {
        return ($this->message instanceof AutobornaMessage) ? $this->message->getMetadata() : [];
    }

    /**
     * Get attachments from a AutobornaMessage.
     *
     * @return array
     */
    public function getAttachments()
    {
        return ($this->message instanceof AutobornaMessage) ? $this->message->getAttachments() : [];
    }
}
