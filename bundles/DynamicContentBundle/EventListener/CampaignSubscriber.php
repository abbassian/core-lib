<?php

namespace Autoborna\DynamicContentBundle\EventListener;

use Autoborna\CampaignBundle\CampaignEvents;
use Autoborna\CampaignBundle\Event\CampaignBuilderEvent;
use Autoborna\CampaignBundle\Event\CampaignExecutionEvent;
use Autoborna\CoreBundle\Event\TokenReplacementEvent;
use Autoborna\DynamicContentBundle\DynamicContentEvents;
use Autoborna\DynamicContentBundle\Entity\DynamicContent;
use Autoborna\DynamicContentBundle\Form\Type\DynamicContentDecisionType;
use Autoborna\DynamicContentBundle\Form\Type\DynamicContentSendType;
use Autoborna\DynamicContentBundle\Model\DynamicContentModel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class CampaignSubscriber implements EventSubscriberInterface
{
    /**
     * @var DynamicContentModel
     */
    private $dynamicContentModel;
    /**
     * @var Session
     */
    private $session;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(DynamicContentModel $dynamicContentModel, Session $session, EventDispatcherInterface $dispatcher)
    {
        $this->dynamicContentModel = $dynamicContentModel;
        $this->session             = $session;
        $this->dispatcher          = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            CampaignEvents::CAMPAIGN_ON_BUILD                  => ['onCampaignBuild', 0],
            DynamicContentEvents::ON_CAMPAIGN_TRIGGER_DECISION => ['onCampaignTriggerDecision', 0],
            DynamicContentEvents::ON_CAMPAIGN_TRIGGER_ACTION   => ['onCampaignTriggerAction', 0],
        ];
    }

    public function onCampaignBuild(CampaignBuilderEvent $event)
    {
        $event->addAction(
            'dwc.push_content',
            [
                'label'                  => 'autoborna.dynamicContent.campaign.send_dwc',
                'description'            => 'autoborna.dynamicContent.campaign.send_dwc.tooltip',
                'eventName'              => DynamicContentEvents::ON_CAMPAIGN_TRIGGER_ACTION,
                'formType'               => DynamicContentSendType::class,
                'formTypeOptions'        => ['update_select' => 'campaignevent_properties_dynamicContent'],
                'formTheme'              => 'AutobornaDynamicContentBundle:FormTheme\DynamicContentPushList',
                'timelineTemplate'       => 'AutobornaDynamicContentBundle:SubscribedEvents\Timeline:index.html.php',
                'hideTriggerMode'        => true,
                'connectionRestrictions' => [
                    'anchor' => [
                        'decision.inaction',
                    ],
                    'source' => [
                        'decision' => [
                            'dwc.decision',
                        ],
                    ],
                ],
                'channel'        => 'dynamicContent',
                'channelIdField' => 'dwc_slot_name',
            ]
        );

        $event->addDecision(
            'dwc.decision',
            [
                'label'           => 'autoborna.dynamicContent.campaign.decision_dwc',
                'description'     => 'autoborna.dynamicContent.campaign.decision_dwc.tooltip',
                'eventName'       => DynamicContentEvents::ON_CAMPAIGN_TRIGGER_DECISION,
                'formType'        => DynamicContentDecisionType::class,
                'formTypeOptions' => ['update_select' => 'campaignevent_properties_dynamicContent'],
                'formTheme'       => 'AutobornaDynamicContentBundle:FormTheme\DynamicContentDecisionList',
                'channel'         => 'dynamicContent',
                'channelIdField'  => 'dynamicContent',
            ]
        );
    }

    /**
     * @return bool|CampaignExecutionEvent
     */
    public function onCampaignTriggerDecision(CampaignExecutionEvent $event)
    {
        $eventConfig  = $event->getConfig();
        $eventDetails = $event->getEventDetails();
        $lead         = $event->getLead();

        // stop
        if ($eventConfig['dwc_slot_name'] !== $eventDetails) {
            $event->setResult(false);

            return false;
        }

        $defaultDwc = $this->dynamicContentModel->getRepository()->getEntity($eventConfig['dynamicContent']);

        if ($defaultDwc instanceof DynamicContent) {
            // Set the default content in case none of the actions return data
            $this->dynamicContentModel->setSlotContentForLead($defaultDwc, $lead, $eventDetails);
        }

        $this->session->set('dwc.slot_name.lead.'.$lead->getId(), $eventDetails);

        $event->stopPropagation();

        return $event->setResult(true);
    }

    public function onCampaignTriggerAction(CampaignExecutionEvent $event)
    {
        $eventConfig = $event->getConfig();
        $lead        = $event->getLead();
        $slot        = $this->session->get('dwc.slot_name.lead.'.$lead->getId());

        $dwc = $this->dynamicContentModel->getRepository()->getEntity($eventConfig['dynamicContent']);

        if ($dwc instanceof DynamicContent) {
            // Use translation if available
            list($ignore, $dwc) = $this->dynamicContentModel->getTranslatedEntity($dwc, $lead);

            if ($slot) {
                $this->dynamicContentModel->setSlotContentForLead($dwc, $lead, $slot);
            }

            $this->dynamicContentModel->createStatEntry($dwc, $lead, $slot);

            $tokenEvent = new TokenReplacementEvent($dwc->getContent(), $lead, ['slot' => $slot, 'dynamic_content_id' => $dwc->getId()]);
            $this->dispatcher->dispatch(DynamicContentEvents::TOKEN_REPLACEMENT, $tokenEvent);

            $content = $tokenEvent->getContent();
            $content = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content);

            $event->stopPropagation();

            $result = $event->setResult($content);
            $event->setChannel('dynamicContent', $dwc->getId());

            return $result;
        }
    }
}
