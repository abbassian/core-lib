<?php

namespace Autoborna\EmailBundle\EventListener;

use Autoborna\EmailBundle\EmailEvents;
use Autoborna\EmailBundle\Event\MonitoredEmailEvent;
use Autoborna\EmailBundle\Event\ParseEmailEvent;
use Autoborna\EmailBundle\MonitoredEmail\Processor\Bounce;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProcessBounceSubscriber implements EventSubscriberInterface
{
    const BUNDLE     = 'EmailBundle';
    const FOLDER_KEY = 'bounces';

    /**
     * @var Bounce
     */
    private $bouncer;

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            EmailEvents::MONITORED_EMAIL_CONFIG => ['onEmailConfig', 0],
            EmailEvents::EMAIL_PARSE            => ['onEmailParse', 0],
        ];
    }

    /**
     * EmailBounceSubscriber constructor.
     */
    public function __construct(Bounce $bouncer)
    {
        $this->bouncer = $bouncer;
    }

    public function onEmailConfig(MonitoredEmailEvent $event)
    {
        $event->addFolder(self::BUNDLE, self::FOLDER_KEY, 'autoborna.email.config.monitored_email.bounce_folder');
    }

    public function onEmailParse(ParseEmailEvent $event)
    {
        if ($event->isApplicable(self::BUNDLE, self::FOLDER_KEY)) {
            // Process the messages
            $messages = $event->getMessages();
            foreach ($messages as $message) {
                $this->bouncer->process($message);
            }
        }
    }
}
