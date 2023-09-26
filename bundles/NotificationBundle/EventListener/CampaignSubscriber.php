<?php

namespace Autoborna\NotificationBundle\EventListener;

use Autoborna\CampaignBundle\CampaignEvents;
use Autoborna\CampaignBundle\Event\CampaignBuilderEvent;
use Autoborna\CampaignBundle\Event\CampaignExecutionEvent;
use Autoborna\CoreBundle\Event\TokenReplacementEvent;
use Autoborna\LeadBundle\Entity\DoNotContact;
use Autoborna\LeadBundle\Model\DoNotContact as DoNotContactModel;
use Autoborna\NotificationBundle\Api\AbstractNotificationApi;
use Autoborna\NotificationBundle\Event\NotificationSendEvent;
use Autoborna\NotificationBundle\Form\Type\MobileNotificationSendType;
use Autoborna\NotificationBundle\Form\Type\NotificationSendType;
use Autoborna\NotificationBundle\Model\NotificationModel;
use Autoborna\NotificationBundle\NotificationEvents;
use Autoborna\PluginBundle\Helper\IntegrationHelper;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CampaignSubscriber implements EventSubscriberInterface
{
    private NotificationModel $notificationModel;
    private AbstractNotificationApi $notificationApi;
    private IntegrationHelper $integrationHelper;
    private EventDispatcherInterface $dispatcher;
    private DoNotContactModel $doNotContact;

    public function __construct(
        IntegrationHelper $integrationHelper,
        NotificationModel $notificationModel,
        AbstractNotificationApi $notificationApi,
        EventDispatcherInterface $dispatcher,
        DoNotContactModel $doNotContact
    ) {
        $this->integrationHelper = $integrationHelper;
        $this->notificationModel = $notificationModel;
        $this->notificationApi   = $notificationApi;
        $this->dispatcher        = $dispatcher;
        $this->doNotContact      = $doNotContact;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            CampaignEvents::CAMPAIGN_ON_BUILD              => ['onCampaignBuild', 0],
            NotificationEvents::ON_CAMPAIGN_TRIGGER_ACTION => ['onCampaignTriggerAction', 0],
        ];
    }

    public function onCampaignBuild(CampaignBuilderEvent $event)
    {
        $integration = $this->integrationHelper->getIntegrationObject('OneSignal');

        if (!$integration || false === $integration->getIntegrationSettings()->getIsPublished()) {
            return;
        }

        $features = $integration->getSupportedFeatures();

        if (in_array('mobile', $features)) {
            $event->addAction(
                'notification.send_mobile_notification',
                [
                    'label'            => 'autoborna.notification.campaign.send_mobile_notification',
                    'description'      => 'autoborna.notification.campaign.send_mobile_notification.tooltip',
                    'eventName'        => NotificationEvents::ON_CAMPAIGN_TRIGGER_ACTION,
                    'formType'         => MobileNotificationSendType::class,
                    'formTypeOptions'  => ['update_select' => 'campaignevent_properties_notification'],
                    'formTheme'        => 'AutobornaNotificationBundle:FormTheme\NotificationSendList',
                    'timelineTemplate' => 'AutobornaNotificationBundle:SubscribedEvents\Timeline:index.html.php',
                    'channel'          => 'mobile_notification',
                    'channelIdField'   => 'mobile_notification',
                ]
            );
        }

        $event->addAction(
            'notification.send_notification',
            [
                'label'            => 'autoborna.notification.campaign.send_notification',
                'description'      => 'autoborna.notification.campaign.send_notification.tooltip',
                'eventName'        => NotificationEvents::ON_CAMPAIGN_TRIGGER_ACTION,
                'formType'         => NotificationSendType::class,
                'formTypeOptions'  => ['update_select' => 'campaignevent_properties_notification'],
                'formTheme'        => 'AutobornaNotificationBundle:FormTheme\NotificationSendList',
                'timelineTemplate' => 'AutobornaNotificationBundle:SubscribedEvents\Timeline:index.html.php',
                'channel'          => 'notification',
                'channelIdField'   => 'notification',
            ]
        );
    }

    /**
     * @return CampaignExecutionEvent
     */
    public function onCampaignTriggerAction(CampaignExecutionEvent $event)
    {
        $lead = $event->getLead();

        if (DoNotContact::IS_CONTACTABLE !== $this->doNotContact->isContactable($lead, 'notification')) {
            return $event->setFailed('autoborna.notification.campaign.failed.not_contactable');
        }

        $notificationId = (int) $event->getConfig()['notification'];

        /** @var \Autoborna\NotificationBundle\Entity\Notification $notification */
        $notification = $this->notificationModel->getEntity($notificationId);

        if ($notification->getId() !== $notificationId) {
            return $event->setFailed('autoborna.notification.campaign.failed.missing_entity');
        }

        if (!$notification->getIsPublished()) {
            return $event->setFailed('autoborna.notification.campaign.failed.unpublished');
        }

        // If lead has subscribed on multiple devices, get all of them.
        /** @var \Autoborna\NotificationBundle\Entity\PushID[] $pushIDs */
        $pushIDs = $lead->getPushIDs();

        $playerID = [];

        foreach ($pushIDs as $pushID) {
            // Skip non-mobile PushIDs if this is a mobile event
            if ($event->checkContext('notification.send_mobile_notification') && false == $pushID->isMobile()) {
                continue;
            }

            // Skip mobile PushIDs if this is a non-mobile event
            if ($event->checkContext('notification.send_notification') && true == $pushID->isMobile()) {
                continue;
            }

            $playerID[] = $pushID->getPushID();
        }

        if (empty($playerID)) {
            return $event->setFailed('autoborna.notification.campaign.failed.not_subscribed');
        }

        if ($url = $notification->getUrl()) {
            $url = $this->notificationApi->convertToTrackedUrl(
                $url,
                [
                    'notification' => $notification->getId(),
                    'lead'         => $lead->getId(),
                ],
                $notification
            );
        }

        /** @var TokenReplacementEvent $tokenEvent */
        $tokenEvent = $this->dispatcher->dispatch(
            NotificationEvents::TOKEN_REPLACEMENT,
            new TokenReplacementEvent(
                $notification->getMessage(),
                $lead,
                ['channel' => ['notification', $notification->getId()]]
            )
        );

        /** @var NotificationSendEvent $sendEvent */
        $sendEvent = $this->dispatcher->dispatch(
            NotificationEvents::NOTIFICATION_ON_SEND,
            new NotificationSendEvent($tokenEvent->getContent(), $notification->getHeading(), $lead)
        );

        // prevent rewrite notification entity
        $sendNotification = clone $notification;
        $sendNotification->setUrl($url);
        $sendNotification->setMessage($sendEvent->getMessage());
        $sendNotification->setHeading($sendEvent->getHeading());

        $response = $this->notificationApi->sendNotification(
            $playerID,
            $sendNotification
        );

        $event->setChannel('notification', $notification->getId());

        // If for some reason the call failed, tell autoborna to try again by return false
        if (200 !== $response->code) {
            return $event->setResult(false);
        }

        $this->notificationModel->createStatEntry($notification, $lead, 'campaign.event', $event->getEvent()['id']);
        $this->notificationModel->getRepository()->upCount($notificationId);

        $result = [
            'status'  => 'autoborna.notification.timeline.status.delivered',
            'type'    => 'autoborna.notification.notification',
            'id'      => $notification->getId(),
            'name'    => $notification->getName(),
            'heading' => $sendEvent->getHeading(),
            'content' => $sendEvent->getMessage(),
        ];

        $event->setResult($result);
    }
}
