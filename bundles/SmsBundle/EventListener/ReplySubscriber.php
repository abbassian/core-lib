<?php

namespace Autoborna\SmsBundle\EventListener;

use Autoborna\CoreBundle\Helper\InputHelper;
use Autoborna\LeadBundle\Entity\LeadEventLog;
use Autoborna\LeadBundle\Entity\LeadEventLogRepository;
use Autoborna\LeadBundle\Event\LeadTimelineEvent;
use Autoborna\LeadBundle\EventListener\TimelineEventLogTrait;
use Autoborna\LeadBundle\LeadEvents;
use Autoborna\SmsBundle\Event\ReplyEvent;
use Autoborna\SmsBundle\SmsEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ReplySubscriber implements EventSubscriberInterface
{
    use TimelineEventLogTrait;

    /**
     * ReplySubscriber constructor.
     */
    public function __construct(TranslatorInterface $translator, LeadEventLogRepository $eventLogRepository)
    {
        $this->translator         = $translator;
        $this->eventLogRepository = $eventLogRepository;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            SmsEvents::ON_REPLY              => ['onReply', 0],
            LeadEvents::TIMELINE_ON_GENERATE => 'onTimelineGenerate',
        ];
    }

    public function onReply(ReplyEvent $event)
    {
        $message = $event->getMessage();
        $contact = $event->getContact();

        $log = new LeadEventLog();
        $log
            ->setLead($contact)
            ->setBundle('sms')
            ->setObject('sms')
            ->setAction('reply')
            ->setProperties(
                [
                    'message' => InputHelper::clean($message),
                ]
            );

        $this->eventLogRepository->saveEntity($log);
        $this->eventLogRepository->detachEntity($log);
    }

    public function onTimelineGenerate(LeadTimelineEvent $event)
    {
        $this->addEvents(
            $event,
            'sms_reply',
            'autoborna.sms.timeline.reply',
            'fa-mobile',
            'sms',
            'sms',
            'reply',
            'AutobornaSmsBundle:SubscribedEvents/Timeline:reply.html.php'
        );
    }
}
