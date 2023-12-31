<?php

namespace Autoborna\EmailBundle\EventListener;

use Autoborna\ChannelBundle\ChannelEvents;
use Autoborna\ChannelBundle\Entity\MessageQueue;
use Autoborna\ChannelBundle\Event\MessageQueueBatchProcessEvent;
use Autoborna\EmailBundle\Model\EmailModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MessageQueueSubscriber implements EventSubscriberInterface
{
    /**
     * @var EmailModel
     */
    private $emailModel;

    public function __construct(EmailModel $emailModel)
    {
        $this->emailModel = $emailModel;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            ChannelEvents::PROCESS_MESSAGE_QUEUE_BATCH => ['onProcessMessageQueueBatch', 0],
        ];
    }

    /**
     * Sends campaign emails.
     */
    public function onProcessMessageQueueBatch(MessageQueueBatchProcessEvent $event)
    {
        if (!$event->checkContext('email')) {
            return;
        }

        $messages = $event->getMessages();
        $emailId  = $event->getChannelId();
        $email    = $this->emailModel->getEntity($emailId);

        $sendTo            = [];
        $messagesByContact = [];
        $options           = [
            'email_type' => 'marketing',
        ];

        /** @var MessageQueue $message */
        foreach ($messages as $message) {
            if (!($email && $message->getLead() && $email->isPublished())) {
                $message->setFailed();
                continue;
            }

            $contact = $message->getLead()->getProfileFields();
            if (empty($contact['email'])) {
                // No email so just let this slide
                $message->setProcessed();
                $message->setSuccess();
            }
            $sendTo[$contact['id']]            = $contact;
            $messagesByContact[$contact['id']] = $message;
        }

        if (count($sendTo)) {
            $options['resend_message_queue'] = $messagesByContact;
            $errors                          = $this->emailModel->sendEmail($email, $sendTo, $options);

            // Let's see who was successful
            foreach ($messagesByContact as $contactId => $message) {
                // If the message is processed, it was rescheduled by sendEmail
                if ($message->isProcessed()) {
                    continue;
                }

                $message->setProcessed();
                if (empty($errors[$contactId])) {
                    $message->setSuccess();
                    continue;
                }

                // Setting it to failed so it could be rescheduled
                // by MessageQueueModel::processMessageQueue.
                // We will get job loops otherwise.
                $message->setFailed();
            }
        }

        $event->stopPropagation();
    }
}
