<?php

namespace Autoborna\EmailBundle\Swiftmailer\SendGrid\Callback;

use Autoborna\LeadBundle\Entity\DoNotContact;

class CallbackEnum
{
    const BOUNCE            = 'bounce';
    const DROPPED           = 'dropped';
    const SPAM_REPORT       = 'spamreport';
    const UNSUBSCRIBE       = 'unsubscribe';
    const GROUP_UNSUBSCRIBE = 'group_unsubscribe';

    /**
     * @see https://sendgrid.com/docs/API_Reference/Webhooks/event.html#-Event-Types
     *
     * @param string $event
     *
     * @return bool
     */
    public static function shouldBeEventProcessed($event)
    {
        return in_array($event, self::getSupportedEvents(), true);
    }

    /**
     * @param $event
     *
     * @return string|null
     */
    public static function convertEventToDncReason($event)
    {
        if (!self::shouldBeEventProcessed($event)) {
            return null;
        }

        $mapping = self::eventMappingToDncReason();

        return $mapping[$event];
    }

    /**
     * @return array
     */
    private static function getSupportedEvents()
    {
        return [
            self::BOUNCE,
            self::DROPPED,
            self::SPAM_REPORT,
            self::UNSUBSCRIBE,
            self::GROUP_UNSUBSCRIBE,
        ];
    }

    /**
     * @return array
     */
    private static function eventMappingToDncReason()
    {
        return [
            self::BOUNCE            => DoNotContact::BOUNCED,
            self::DROPPED           => DoNotContact::BOUNCED,
            self::SPAM_REPORT       => DoNotContact::BOUNCED,
            self::UNSUBSCRIBE       => DoNotContact::UNSUBSCRIBED,
            self::GROUP_UNSUBSCRIBE => DoNotContact::UNSUBSCRIBED,
        ];
    }
}
