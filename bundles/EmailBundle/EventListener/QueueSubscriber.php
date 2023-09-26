<?php

namespace Autoborna\EmailBundle\EventListener;

use Autoborna\EmailBundle\Model\EmailModel;
use Autoborna\QueueBundle\Event\QueueConsumerEvent;
use Autoborna\QueueBundle\Queue\QueueConsumerResults;
use Autoborna\QueueBundle\QueueEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Proceses queue (Beanstalk, RabitMQ, ...) jobs.
 */
class QueueSubscriber implements EventSubscriberInterface
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
            QueueEvents::EMAIL_HIT => ['onEmailHit', 0],
        ];
    }

    public function onEmailHit(QueueConsumerEvent $event)
    {
        $payload = $event->getPayload();
        $this->emailModel->hitEmail($payload['idHash'], $payload['request'], false, false);
        $event->setResult(QueueConsumerResults::ACKNOWLEDGE);
    }
}
