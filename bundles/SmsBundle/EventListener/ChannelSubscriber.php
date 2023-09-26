<?php

namespace Autoborna\SmsBundle\EventListener;

use Autoborna\ChannelBundle\ChannelEvents;
use Autoborna\ChannelBundle\Event\ChannelEvent;
use Autoborna\ChannelBundle\Model\MessageModel;
use Autoborna\LeadBundle\Model\LeadModel;
use Autoborna\ReportBundle\Model\ReportModel;
use Autoborna\SmsBundle\Form\Type\SmsListType;
use Autoborna\SmsBundle\Sms\TransportChain;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ChannelSubscriber implements EventSubscriberInterface
{
    /**
     * @var TransportChain
     */
    private $transportChain;

    public function __construct(TransportChain $transportChain)
    {
        $this->transportChain = $transportChain;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            ChannelEvents::ADD_CHANNEL => ['onAddChannel', 90],
        ];
    }

    public function onAddChannel(ChannelEvent $event)
    {
        if (count($this->transportChain->getEnabledTransports()) > 0) {
            $event->addChannel(
                'sms',
                [
                    MessageModel::CHANNEL_FEATURE => [
                        'campaignAction'             => 'sms.send_text_sms',
                        'campaignDecisionsSupported' => [
                            'page.pagehit',
                            'asset.download',
                            'form.submit',
                        ],
                        'lookupFormType' => SmsListType::class,
                        'repository'     => 'AutobornaSmsBundle:Sms',
                    ],
                    LeadModel::CHANNEL_FEATURE   => [],
                    ReportModel::CHANNEL_FEATURE => [
                        'table' => 'sms_messages',
                    ],
                ]
            );
        }
    }
}
