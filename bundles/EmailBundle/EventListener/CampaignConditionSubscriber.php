<?php

namespace Autoborna\EmailBundle\EventListener;

use Autoborna\CampaignBundle\CampaignEvents;
use Autoborna\CampaignBundle\Event\CampaignBuilderEvent;
use Autoborna\CampaignBundle\Event\CampaignExecutionEvent;
use Autoborna\EmailBundle\EmailEvents;
use Autoborna\EmailBundle\Exception\InvalidEmailException;
use Autoborna\EmailBundle\Helper\EmailValidator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CampaignConditionSubscriber implements EventSubscriberInterface
{
    /**
     * @var EmailValidator
     */
    private $validator;

    public function __construct(EmailValidator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            CampaignEvents::CAMPAIGN_ON_BUILD          => ['onCampaignBuild', 0],
            EmailEvents::ON_CAMPAIGN_TRIGGER_CONDITION => ['onCampaignTriggerCondition', 0],
        ];
    }

    public function onCampaignBuild(CampaignBuilderEvent $event)
    {
        $event->addCondition(
            'email.validate.address',
            [
                'label'       => 'autoborna.email.campaign.event.validate_address',
                'description' => 'autoborna.email.campaign.event.validate_address_descr',
                'eventName'   => EmailEvents::ON_CAMPAIGN_TRIGGER_CONDITION,
            ]
        );
    }

    public function onCampaignTriggerCondition(CampaignExecutionEvent $event)
    {
        try {
            $this->validator->validate($event->getLead()->getEmail(), true);
        } catch (InvalidEmailException $exception) {
            return $event->setResult(false);
        }

        return $event->setResult(true);
    }
}
