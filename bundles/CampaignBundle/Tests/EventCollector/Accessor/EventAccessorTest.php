<?php

namespace Autoborna\CampaignBundle\Tests\EventCollector\Accessor;

use Autoborna\CampaignBundle\Entity\Event;
use Autoborna\CampaignBundle\EventCollector\Accessor\Event\ActionAccessor;
use Autoborna\CampaignBundle\EventCollector\Accessor\Event\ConditionAccessor;
use Autoborna\CampaignBundle\EventCollector\Accessor\Event\DecisionAccessor;
use Autoborna\CampaignBundle\EventCollector\Accessor\EventAccessor;
use Autoborna\EmailBundle\Form\Type\EmailClickDecisionType;
use Autoborna\LeadBundle\Form\Type\CampaignEventLeadCampaignsType;
use Autoborna\LeadBundle\Form\Type\CompanyChangeScoreActionType;

class EventAccessorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var array
     */
    private $events = [
        Event::TYPE_ACTION    => [
            'lead.scorecontactscompanies' => [
                'label'          => 'Add to company\'s score',
                'description'    => 'This action will add the specified value to the company\'s existing score',
                'formType'       => CompanyChangeScoreActionType::class,
                'batchEventName' => 'autoborna.lead.on_campaign_trigger_action',
            ],
        ],
        Event::TYPE_CONDITION => [
            'lead.campaigns' => [
                'label'       => 'Contact campaigns',
                'description' => 'Condition based on a contact campaigns.',
                'formType'    => CampaignEventLeadCampaignsType::class,
                'formTheme'   => 'AutobornaLeadBundle:FormTheme\\ContactCampaignsCondition',
                'eventName'   => 'autoborna.lead.on_campaign_trigger_condition',
            ],
        ],
        Event::TYPE_DECISION  => [
            'email.click' => [
                'label'                  => 'Clicks email',
                'description'            => 'Trigger actions when an email is clicked. Connect a &quot;Send Email&quot; action to the top of this decision.',
                'eventName'              => 'autoborna.email.on_campaign_trigger_decision',
                'formType'               => EmailClickDecisionType::class,
                'connectionRestrictions' => [
                    'source' => [
                        'action' => [
                            'email.send',
                        ],
                    ],
                ],
            ],
        ],
    ];

    public function testEventsArrayIsBuiltWithAccessors()
    {
        $eventAccessor = new EventAccessor($this->events);

        // Actions
        $this->assertCount(1, $eventAccessor->getActions());
        $accessor = $eventAccessor->getAction('lead.scorecontactscompanies');
        $this->assertInstanceOf(ActionAccessor::class, $accessor);
        $this->assertEquals(
            $this->events[Event::TYPE_ACTION]['lead.scorecontactscompanies']['batchEventName'],
            $accessor->getBatchEventName()
        );

        // Conditions
        $this->assertCount(1, $eventAccessor->getConditions());
        $accessor = $eventAccessor->getCondition('lead.campaigns');
        $this->assertInstanceOf(ConditionAccessor::class, $accessor);
        $this->assertEquals(
            $this->events[Event::TYPE_CONDITION]['lead.campaigns']['eventName'],
            $accessor->getEventName()
        );

        // Decisions
        $this->assertCount(1, $eventAccessor->getDecisions());
        $accessor = $eventAccessor->getDecision('email.click');
        $this->assertInstanceOf(DecisionAccessor::class, $accessor);
        $this->assertEquals(
            $this->events[Event::TYPE_DECISION]['email.click']['eventName'],
            $accessor->getEventName()
        );
    }
}
