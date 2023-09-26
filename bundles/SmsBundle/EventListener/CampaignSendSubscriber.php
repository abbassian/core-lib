<?php

namespace Autoborna\SmsBundle\EventListener;

use Autoborna\CampaignBundle\CampaignEvents;
use Autoborna\CampaignBundle\Event\CampaignBuilderEvent;
use Autoborna\CampaignBundle\Event\CampaignExecutionEvent;
use Autoborna\SmsBundle\Form\Type\SmsSendType;
use Autoborna\SmsBundle\Model\SmsModel;
use Autoborna\SmsBundle\Sms\TransportChain;
use Autoborna\SmsBundle\SmsEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CampaignSendSubscriber implements EventSubscriberInterface
{
    /**
     * @var SmsModel
     */
    private $smsModel;

    /**
     * @var TransportChain
     */
    private $transportChain;

    public function __construct(
        SmsModel $smsModel,
        TransportChain $transportChain
    ) {
        $this->smsModel       = $smsModel;
        $this->transportChain = $transportChain;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            CampaignEvents::CAMPAIGN_ON_BUILD     => ['onCampaignBuild', 0],
            SmsEvents::ON_CAMPAIGN_TRIGGER_ACTION => ['onCampaignTriggerAction', 0],
        ];
    }

    public function onCampaignBuild(CampaignBuilderEvent $event)
    {
        if (count($this->transportChain->getEnabledTransports()) > 0) {
            $event->addAction(
                'sms.send_text_sms',
                [
                    'label'            => 'autoborna.campaign.sms.send_text_sms',
                    'description'      => 'autoborna.campaign.sms.send_text_sms.tooltip',
                    'eventName'        => SmsEvents::ON_CAMPAIGN_TRIGGER_ACTION,
                    'formType'         => SmsSendType::class,
                    'formTypeOptions'  => ['update_select' => 'campaignevent_properties_sms'],
                    'formTheme'        => 'AutobornaSmsBundle:FormTheme\SmsSendList',
                    'channel'          => 'sms',
                    'channelIdField'   => 'sms',
                ]
            );
        }
    }

    /**
     * @return void
     */
    public function onCampaignTriggerAction(CampaignExecutionEvent $event)
    {
        $lead  = $event->getLead();
        $smsId = (int) $event->getConfig()['sms'];
        $sms   = $this->smsModel->getEntity($smsId);

        if (!$sms) {
            $event->setFailed('autoborna.sms.campaign.failed.missing_entity');

            return;
        }

        if (!$sms->isPublished()) {
            $event->setFailed('autoborna.sms.campaign.failed.unpublished');

            return;
        }

        $result = $this->smsModel->sendSms($sms, $lead, ['channel' => ['campaign.event', $event->getEvent()['id']]])[$lead->getId()];

        if ('Authenticate' === $result['status']) {
            // Don't fail the event but reschedule it for later
            $event->setResult(false);

            return;
        }

        if (!empty($result['sent'])) {
            $event->setChannel('sms', $sms->getId());
            $event->setResult($result);
        } else {
            $result['failed'] = true;
            $result['reason'] = $result['status'];
            $event->setResult($result);
        }
    }
}
