<?php

namespace Autoborna\EmailBundle\Swiftmailer\Transport;

use Autoborna\CoreBundle\Factory\AutobornaFactory;
use Autoborna\EmailBundle\Helper\MailHelper;
use Autoborna\EmailBundle\Helper\PlainTextMessageHelper;
use Autoborna\EmailBundle\Swiftmailer\Message\AutobornaMessage;

abstract class AbstractTokenArrayTransport implements TokenTransportInterface
{
    /**
     * @var \Swift_Message
     */
    protected $message;

    /**
     * @var \Swift_Events_SimpleEventDispatcher|null
     */
    private $dispatcher;

    /**
     * @var bool
     */
    protected $started = false;

    /**
     * @var array
     */
    protected $standardHeaderKeys = [
        'MIME-Version',
        'received',
        'dkim-signature',
        'Content-Type',
        'Content-Transfer-Encoding',
        'To',
        'From',
        'Subject',
        'Reply-To',
        'CC',
        'BCC',
    ];

    /**
     * @var AutobornaFactory
     *
     * @deprecated 2.13.0 to be removed in 3.0; register transport as a service and pass dependencies
     */
    protected $factory;

    /**
     * Test if this Transport mechanism has started.
     *
     * @return bool
     */
    public function isStarted()
    {
        return $this->started;
    }

    /**
     * Stop this Transport mechanism.
     */
    public function stop()
    {
    }

    /**
     * Start this Transport mechanism.
     */
    public function start()
    {
    }

    /**
     * @return bool
     */
    public function ping()
    {
        return true;
    }

    /**
     * Register a plugin in the Transport.
     */
    public function registerPlugin(\Swift_Events_EventListener $plugin)
    {
        $this->getDispatcher()->bindEventListener($plugin);
    }

    /**
     * @return \Swift_Events_SimpleEventDispatcher
     */
    protected function getDispatcher()
    {
        if (null == $this->dispatcher) {
            $this->dispatcher = new \Swift_Events_SimpleEventDispatcher();
        }

        return $this->dispatcher;
    }

    /**
     * @param null $failedRecipients
     *
     * @return int
     *
     * @throws \Exception
     */
    abstract public function send(\Swift_Mime_SimpleMessage $message, &$failedRecipients = null);

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

    /**
     * Converts \Swift_Message into associative array.
     *
     * @param array      $search            If the mailer requires tokens in another format than Autoborna's, pass array of Autoborna tokens to replace
     * @param array      $replace           If the mailer requires tokens in another format than Autoborna's, pass array of replacement tokens
     * @param bool|false $binaryAttachments True to convert file attachments to binary
     *
     * @return array|\Swift_Message
     */
    protected function messageToArray($search = [], $replace = [], $binaryAttachments = false)
    {
        if (!empty($search)) {
            MailHelper::searchReplaceTokens($search, $replace, $this->message);
        }

        $from      = $this->message->getFrom();
        $fromEmail = current(array_keys($from));
        $fromName  = $from[$fromEmail];

        $message = [
            'html'    => $this->message->getBody(),
            'text'    => PlainTextMessageHelper::getPlainTextFromMessage($this->message),
            'subject' => $this->message->getSubject(),
            'from'    => [
                'name'  => $fromName,
                'email' => $fromEmail,
            ],
        ];

        // Generate the recipients
        $message['recipients'] = [
            'to'  => [],
            'cc'  => [],
            'bcc' => [],
        ];

        $to = $this->message->getTo();
        foreach ($to as $email => $name) {
            $message['recipients']['to'][$email] = [
                'email' => $email,
                'name'  => $name,
            ];
        }

        $cc = $this->message->getCc();
        if (!empty($cc)) {
            foreach ($cc as $email => $name) {
                $message['recipients']['cc'][$email] = [
                    'email' => $email,
                    'name'  => $name,
                ];
            }
        }

        $bcc = $this->message->getBcc();
        if (!empty($bcc)) {
            foreach ($bcc as $email => $name) {
                $message['recipients']['bcc'][$email] = [
                    'email' => $email,
                    'name'  => $name,
                ];
            }
        }

        $replyTo = $this->message->getReplyTo();
        if (!empty($replyTo)) {
            foreach ($replyTo as $email => $name) {
                $message['replyTo'] = [
                    'email' => $email,
                    'name'  => $name,
                ];
            }
        }

        $returnPath = $this->message->getReturnPath();
        if (!empty($returnPath)) {
            $message['returnPath'] = $returnPath;
        }

        // Attachments
        $children    = $this->message->getChildren();
        $attachments = [];
        foreach ($children as $child) {
            if ($child instanceof \Swift_Attachment) {
                $attachments[] = [
                    'type'    => $child->getContentType(),
                    'name'    => $child->getFilename(),
                    'content' => $child->getEncoder()->encodeString($child->getBody()),
                ];
            }
        }

        if ($binaryAttachments) {
            // Convert attachments to binary if applicable
            $message['attachments'] = $attachments;

            $fileAttachments = $this->getAttachments();
            if (!empty($fileAttachments)) {
                foreach ($fileAttachments as $attachment) {
                    if (file_exists($attachment['filePath']) && is_readable($attachment['filePath'])) {
                        try {
                            $swiftAttachment = \Swift_Attachment::fromPath($attachment['filePath']);

                            if (!empty($attachment['fileName'])) {
                                $swiftAttachment->setFilename($attachment['fileName']);
                            }

                            if (!empty($attachment['contentType'])) {
                                $swiftAttachment->setContentType($attachment['contentType']);
                            }

                            if (!empty($attachment['inline'])) {
                                $swiftAttachment->setDisposition('inline');
                            }

                            $message['attachments'][] = [
                                'type'    => $swiftAttachment->getContentType(),
                                'name'    => $swiftAttachment->getFilename(),
                                'content' => $swiftAttachment->getEncoder()->encodeString($swiftAttachment->getBody()),
                            ];
                        } catch (\Exception $e) {
                            error_log($e);
                        }
                    }
                }
            }
        } else {
            $message['binary_attachments'] = $attachments;
            $message['file_attachments']   = $this->getAttachments();
        }

        $message['headers'] = [];
        $headers            = $this->message->getHeaders()->getAll();
        /** @var \Swift_Mime_Header $header */
        foreach ($headers as $header) {
            if (\Swift_Mime_Header::TYPE_TEXT == $header->getFieldType() && !in_array($header->getFieldName(), $this->standardHeaderKeys)) {
                $message['headers'][$header->getFieldName()] = $header->getFieldBodyModel();
            }
        }

        return $message;
    }

    /**
     * @deprecated 2.13.0 to be removed in 3.0; register transport as a service and pass dependencies
     */
    public function setAutobornaFactory(AutobornaFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param \Exception|string $exception
     *
     * @throws \Exception
     */
    protected function throwException($exception)
    {
        if (!$exception instanceof \Exception) {
            $exception = new \Swift_TransportException($exception);
        }

        if ($evt = $this->getDispatcher()->createTransportExceptionEvent($this, $exception)) {
            $this->getDispatcher()->dispatchEvent($evt, 'exceptionThrown');
            if (!$evt->bubbleCancelled()) {
                throw $exception;
            }
        } else {
            throw $exception;
        }
    }
}
