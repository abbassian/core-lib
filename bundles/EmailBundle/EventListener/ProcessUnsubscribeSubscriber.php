<?php

namespace Autoborna\EmailBundle\EventListener;

use Autoborna\EmailBundle\EmailEvents;
use Autoborna\EmailBundle\Event\EmailSendEvent;
use Autoborna\EmailBundle\Event\MonitoredEmailEvent;
use Autoborna\EmailBundle\Event\ParseEmailEvent;
use Autoborna\EmailBundle\MonitoredEmail\Processor\FeedbackLoop;
use Autoborna\EmailBundle\MonitoredEmail\Processor\Unsubscribe;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProcessUnsubscribeSubscriber implements EventSubscriberInterface
{
    const BUNDLE     = 'EmailBundle';
    const FOLDER_KEY = 'unsubscribes';

    /**
     * @var Unsubscribe
     */
    private $unsubscriber;

    /**
     * @var FeedbackLoop
     */
    private $looper;

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            EmailEvents::MONITORED_EMAIL_CONFIG => ['onEmailConfig', 0],
            EmailEvents::EMAIL_PARSE            => ['onEmailParse', 0],
            EmailEvents::EMAIL_ON_SEND          => ['onEmailSend', 0],
        ];
    }

    /**
     * ProcessUnsubscribeSubscriber constructor.
     */
    public function __construct(Unsubscribe $unsubscriber, FeedbackLoop $looper)
    {
        $this->unsubscriber = $unsubscriber;
        $this->looper       = $looper;
    }

    public function onEmailConfig(MonitoredEmailEvent $event)
    {
        $event->addFolder(self::BUNDLE, self::FOLDER_KEY, 'autoborna.email.config.monitored_email.unsubscribe_folder');
    }

    public function onEmailParse(ParseEmailEvent $event)
    {
        if ($event->isApplicable(self::BUNDLE, self::FOLDER_KEY)) {
            // Process the messages
            $messages = $event->getMessages();
            foreach ($messages as $message) {
                if (!$this->unsubscriber->process($message)) {
                    $this->looper->process($message);
                }
            }
        }
    }

    /**
     * Add an unsubscribe email to the List-Unsubscribe header if applicable.
     */
    public function onEmailSend(EmailSendEvent $event)
    {
        $helper = $event->getHelper();
        if ($helper && $unsubscribeEmail = $helper->generateUnsubscribeEmail()) {
            $headers          = $event->getTextHeaders();
            $existing         = (isset($headers['List-Unsubscribe'])) ? $headers['List-Unsubscribe'] : '';
            $unsubscribeEmail = "<mailto:$unsubscribeEmail>";
            if ($existing) {
                if (false === strpos($existing, $unsubscribeEmail)) {
                    $updatedHeader = $unsubscribeEmail.', '.$existing;
                } else {
                    $updatedHeader = $existing;
                }
            } else {
                $updatedHeader = $unsubscribeEmail;
            }

            $event->addTextHeader('List-Unsubscribe', $updatedHeader);
        }
    }
}
