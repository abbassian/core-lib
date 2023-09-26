<?php

namespace Autoborna\PageBundle\EventListener;

use Autoborna\PageBundle\Event\PageDisplayEvent;
use Autoborna\PageBundle\PageEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TokenSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            PageEvents::PAGE_ON_DISPLAY => ['decodeTokens', 254],
        ];
    }

    public function decodeTokens(PageDisplayEvent $event)
    {
        // Find and replace encoded tokens for trackable URL conversion
        $content = $event->getContent();
        $content = preg_replace('/(%7B)(.*?)(%7D)/i', '{$2}', $content);
        $event->setContent($content);
    }
}
