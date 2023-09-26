<?php

namespace Autoborna\LeadBundle\EventListener;

use Autoborna\LeadBundle\Entity\LeadEventLogRepository;
use Autoborna\LeadBundle\Event\LeadTimelineEvent;
use Autoborna\LeadBundle\LeadEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;

class TimelineEventLogSubscriber implements EventSubscriberInterface
{
    use TimelineEventLogTrait;

    /**
     * TimelineEventLogSubscriber constructor.
     */
    public function __construct(
        TranslatorInterface $translator,
        LeadEventLogRepository $leadEventLogRepository
    ) {
        $this->translator         = $translator;
        $this->eventLogRepository = $leadEventLogRepository;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            LeadEvents::TIMELINE_ON_GENERATE => ['onTimelineGenerate', 0],
        ];
    }

    public function onTimelineGenerate(LeadTimelineEvent $event)
    {
        $this->addEvents(
            $event,
            'lead.source.created',
            'autoborna.lead.timeline.created_source',
            'fa-user-secret',
            null,
            null,
            'created_contact'
        );

        $this->addEvents(
            $event,
            'lead.source.identified',
            'autoborna.lead.timeline.identified_source',
            'fa-user',
            null,
            null,
            'identified_contact'
        );
    }
}
