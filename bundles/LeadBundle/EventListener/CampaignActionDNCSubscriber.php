<?php

namespace Autoborna\LeadBundle\EventListener;

use Autoborna\CampaignBundle\CampaignEvents;
use Autoborna\CampaignBundle\Event\CampaignBuilderEvent;
use Autoborna\CampaignBundle\Event\PendingEvent;
use Autoborna\CoreBundle\Helper\ArrayHelper;
use Autoborna\LeadBundle\Form\Type\CampaignActionAddDNCType;
use Autoborna\LeadBundle\Form\Type\CampaignActionRemoveDNCType;
use Autoborna\LeadBundle\LeadEvents;
use Autoborna\LeadBundle\Model\DoNotContact;
use Autoborna\LeadBundle\Model\LeadModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CampaignActionDNCSubscriber implements EventSubscriberInterface
{
    /**
     * @var DoNotContact
     */
    private $doNotContact;

    /**
     * @var LeadModel
     */
    private $leadModel;

    /**
     * CampaignActionDNCSubscriber constructor.
     */
    public function __construct(DoNotContact $doNotContact, LeadModel $leadModel)
    {
        $this->doNotContact = $doNotContact;
        $this->leadModel    = $leadModel;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            CampaignEvents::CAMPAIGN_ON_BUILD                  => ['configureAction', 0],
            LeadEvents::ON_CAMPAIGN_ACTION_ADD_DONOTCONTACT    => ['addDoNotContact', 0],
            LeadEvents::ON_CAMPAIGN_ACTION_REMOVE_DONOTCONTACT => ['removeDoNotContact', 0],
        ];
    }

    public function configureAction(CampaignBuilderEvent $event)
    {
        $event->addAction(
            'lead.adddnc',
            [
                'label'          => 'autoborna.lead.lead.events.add_donotcontact',
                'description'    => 'autoborna.lead.lead.events.add_donotcontact_desc',
                'batchEventName' => LeadEvents::ON_CAMPAIGN_ACTION_ADD_DONOTCONTACT,
                'formType'       => CampaignActionAddDNCType::class,
            ]
        );

        $event->addAction(
            'lead.removednc',
            [
                'label'          => 'autoborna.lead.lead.events.remove_donotcontact',
                'description'    => 'autoborna.lead.lead.events.remove_donotcontact_desc',
                'batchEventName' => LeadEvents::ON_CAMPAIGN_ACTION_REMOVE_DONOTCONTACT,
                'formType'       => CampaignActionRemoveDNCType::class,
            ]
        );
    }

    public function addDoNotContact(PendingEvent $event)
    {
        $config          = $event->getEvent()->getProperties();
        $channels        = ArrayHelper::getValue('channels', $config, []);
        $reason          = ArrayHelper::getValue('reason', $config, '');
        $persistEntities = [];
        $contacts        = $event->getContactsKeyedById();
        foreach ($contacts as $contactId=>$contact) {
            foreach ($channels as $channel) {
                $this->doNotContact->addDncForContact(
                    $contactId,
                    $channel,
                    \Autoborna\LeadBundle\Entity\DoNotContact::MANUAL,
                    $reason,
                    false
                );
            }
            $persistEntities[] = $contact;
        }

        $this->leadModel->saveEntities($persistEntities);

        $event->passAll();
    }

    public function removeDoNotContact(PendingEvent $event)
    {
        $config          = $event->getEvent()->getProperties();
        $channels        = ArrayHelper::getValue('channels', $config, []);
        $persistEntities = [];
        $contacts        = $event->getContactsKeyedById();
        foreach ($contacts as $contactId=>$contact) {
            foreach ($channels as $channel) {
                $this->doNotContact->removeDncForContact(
                    $contactId,
                    $channel,
                    true
                );
            }
            $persistEntities[] = $contact;
        }

        $this->leadModel->saveEntities($persistEntities);

        $event->passAll();
    }
}
