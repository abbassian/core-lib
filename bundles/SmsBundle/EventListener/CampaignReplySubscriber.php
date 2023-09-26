<?php

namespace Autoborna\SmsBundle\EventListener;

use Autoborna\CampaignBundle\CampaignEvents;
use Autoborna\CampaignBundle\Event\CampaignBuilderEvent;
use Autoborna\CampaignBundle\Event\DecisionEvent;
use Autoborna\CampaignBundle\Executioner\RealTimeExecutioner;
use Autoborna\SmsBundle\Event\ReplyEvent;
use Autoborna\SmsBundle\Form\Type\CampaignReplyType;
use Autoborna\SmsBundle\Helper\ReplyHelper;
use Autoborna\SmsBundle\Sms\TransportChain;
use Autoborna\SmsBundle\SmsEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class CampaignReplySubscriber.
 */
class CampaignReplySubscriber implements EventSubscriberInterface
{
    const TYPE = 'sms.reply';

    /**
     * @var TransportChain
     */
    private $transportChain;

    /**
     * @var RealTimeExecutioner
     */
    private $realTimeExecutioner;

    /**
     * CampaignReplySubscriber constructor.
     */
    public function __construct(TransportChain $transportChain, RealTimeExecutioner $realTimeExecutioner)
    {
        $this->transportChain      = $transportChain;
        $this->realTimeExecutioner = $realTimeExecutioner;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            CampaignEvents::CAMPAIGN_ON_BUILD => ['onCampaignBuild', 0],
            SmsEvents::ON_CAMPAIGN_REPLY      => ['onCampaignReply', 0],
            SmsEvents::ON_REPLY               => ['onReply', 0],
        ];
    }

    public function onCampaignBuild(CampaignBuilderEvent $event)
    {
        if (0 === count($this->transportChain->getEnabledTransports())) {
            return;
        }

        $event->addDecision(
            self::TYPE,
            [
                'label'       => 'autoborna.campaign.sms.reply',
                'description' => 'autoborna.campaign.sms.reply.tooltip',
                'eventName'   => SmsEvents::ON_CAMPAIGN_REPLY,
                'formType'    => CampaignReplyType::class,
            ]
        );
    }

    public function onCampaignReply(DecisionEvent $decisionEvent)
    {
        /** @var ReplyEvent $replyEvent */
        $replyEvent = $decisionEvent->getPassthrough();
        $pattern    = $decisionEvent->getLog()->getEvent()->getProperties()['pattern'];

        if (empty($pattern)) {
            // Assume any reply
            $decisionEvent->setAsApplicable();

            return;
        }

        if (!ReplyHelper::matches($pattern, $replyEvent->getMessage())) {
            // It does not match so ignore

            return;
        }

        $decisionEvent->setChannel('sms');
        $decisionEvent->setAsApplicable();
    }

    /**
     * @throws \Autoborna\CampaignBundle\Executioner\Dispatcher\Exception\LogNotProcessedException
     * @throws \Autoborna\CampaignBundle\Executioner\Dispatcher\Exception\LogPassedAndFailedException
     * @throws \Autoborna\CampaignBundle\Executioner\Exception\CannotProcessEventException
     * @throws \Autoborna\CampaignBundle\Executioner\Scheduler\Exception\NotSchedulableException
     */
    public function onReply(ReplyEvent $event)
    {
        $this->realTimeExecutioner->execute(self::TYPE, $event, 'sms');
    }
}
