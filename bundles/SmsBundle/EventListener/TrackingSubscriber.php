<?php

namespace Autoborna\SmsBundle\EventListener;

use Autoborna\LeadBundle\Event\ContactIdentificationEvent;
use Autoborna\LeadBundle\LeadEvents;
use Autoborna\SmsBundle\Entity\Stat;
use Autoborna\SmsBundle\Entity\StatRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TrackingSubscriber implements EventSubscriberInterface
{
    /**
     * @var StatRepository
     */
    private $statRepository;

    /**
     * TrackingSubscriber constructor.
     */
    public function __construct(StatRepository $statRepository)
    {
        $this->statRepository = $statRepository;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            LeadEvents::ON_CLICKTHROUGH_IDENTIFICATION => ['onIdentifyContact', 0],
        ];
    }

    public function onIdentifyContact(ContactIdentificationEvent $event)
    {
        $clickthrough = $event->getClickthrough();

        // Nothing left to identify by so stick to the tracked lead
        if (empty($clickthrough['channel']['sms']) && empty($clickthrough['stat'])) {
            return;
        }

        /** @var Stat $stat */
        $stat = $this->statRepository->findOneBy(['trackingHash' => $clickthrough['stat']]);

        if (!$stat) {
            // Stat doesn't exist so use the tracked lead
            return;
        }

        if ($stat->getSms() && (int) $stat->getSms()->getId() !== (int) $clickthrough['channel']['sms']) {
            // ID mismatch - fishy so use tracked lead
            return;
        }

        if (!$contact = $stat->getLead()) {
            return;
        }

        $event->setIdentifiedContact($contact, 'sms');
    }
}
