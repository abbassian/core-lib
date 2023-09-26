<?php

namespace Autoborna\CampaignBundle\Tests\Executioner;

use Doctrine\Common\Collections\ArrayCollection;
use Autoborna\CampaignBundle\CampaignEvents;
use Autoborna\CampaignBundle\Entity\Campaign;
use Autoborna\CampaignBundle\Entity\Event;
use Autoborna\CampaignBundle\Entity\EventRepository;
use Autoborna\CampaignBundle\Entity\LeadEventLog;
use Autoborna\CampaignBundle\Entity\LeadRepository;
use Autoborna\CampaignBundle\Event\PendingEvent;
use Autoborna\CampaignBundle\EventCollector\Accessor\Event\ActionAccessor;
use Autoborna\CampaignBundle\EventCollector\EventCollector;
use Autoborna\CampaignBundle\EventListener\CampaignActionJumpToEventSubscriber;
use Autoborna\CampaignBundle\Executioner\Event\ActionExecutioner;
use Autoborna\CampaignBundle\Executioner\Event\ConditionExecutioner;
use Autoborna\CampaignBundle\Executioner\Event\DecisionExecutioner;
use Autoborna\CampaignBundle\Executioner\EventExecutioner;
use Autoborna\CampaignBundle\Executioner\Logger\EventLogger;
use Autoborna\CampaignBundle\Executioner\Result\EvaluatedContacts;
use Autoborna\CampaignBundle\Executioner\Scheduler\EventScheduler;
use Autoborna\CampaignBundle\Form\Type\CampaignEventJumpToEventType;
use Autoborna\CampaignBundle\Helper\RemovedContactTracker;
use Autoborna\CoreBundle\Translation\Translator;
use Autoborna\EmailBundle\EmailEvents;
use Autoborna\EmailBundle\Form\Type\EmailSendType;
use Autoborna\LeadBundle\Entity\Lead;
use PHPUnit\Framework\MockObject\MockBuilder;
use Psr\Log\LoggerInterface;

class EventExecutionerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var EventCollector|\PHPUnit\Framework\MockObject\MockObject
     */
    private $eventCollector;

    /**
     * @var EventLogger|\PHPUnit\Framework\MockObject\MockObject
     */
    private $eventLogger;

    /**
     * @var ActionExecutioner|\PHPUnit\Framework\MockObject\MockObject
     */
    private $actionExecutioner;

    /**
     * @var ConditionExecutioner|\PHPUnit\Framework\MockObject\MockObject
     */
    private $conditionExecutioner;

    /**
     * @var DecisionExecutioner|\PHPUnit\Framework\MockObject\MockObject
     */
    private $decisionExecutioner;

    /**
     * @var LoggerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $logger;

    /**
     * @var EventScheduler|\PHPUnit\Framework\MockObject\MockObject
     */
    private $eventScheduler;

    /**
     * @var RemovedContactTracker|\PHPUnit\Framework\MockObject\MockObject
     */
    private $removedContactTracker;

    /**
     * @var LeadRepository|\PHPUnit\Framework\MockObject\MockObject
     */
    private $leadRepository;

    /**
     * @var EventRepository|MockBuilder
     */
    private $eventRepository;

    /**
     * @var Translator|MockBuilder
     */
    private $translator;

    protected function setUp(): void
    {
        $this->eventCollector        = $this->createMock(EventCollector::class);
        $this->eventLogger           = $this->createMock(EventLogger::class);
        $this->eventLogger->method('persistCollection')
            ->willReturn($this->eventLogger);
        $this->actionExecutioner     = $this->createMock(ActionExecutioner::class);
        $this->conditionExecutioner  = $this->createMock(ConditionExecutioner::class);
        $this->decisionExecutioner   = $this->createMock(DecisionExecutioner::class);
        $this->logger                = $this->createMock(LoggerInterface::class);
        $this->eventScheduler        = $this->createMock(EventScheduler::class);
        $this->removedContactTracker = $this->createMock(RemovedContactTracker::class);
        $this->leadRepository        = $this->createMock(LeadRepository::class);
        $this->eventRepository       = $this->getMockBuilder(EventRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->translator = $this->getMockBuilder(Translator::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testJumpToEventsAreProcessedAfterOtherEvents()
    {
        $campaign = new Campaign();

        $otherEvent = new Event();
        $otherEvent->setEventType(ActionExecutioner::TYPE)
            ->setType('email.send')
            ->setCampaign($campaign);
        $otherConfig = new ActionAccessor(
           [
                'label'                => 'autoborna.email.campaign.event.send',
                'description'          => 'autoborna.email.campaign.event.send_descr',
                'batchEventName'       => EmailEvents::ON_CAMPAIGN_BATCH_ACTION,
                'formType'             => EmailSendType::class,
                'formTypeOptions'      => ['update_select' => 'campaignevent_properties_email', 'with_email_types' => true],
                'formTheme'            => 'AutobornaEmailBundle:FormTheme\EmailSendList',
                'channel'              => 'email',
                'channelIdField'       => 'email',
            ]
        );

        $jumpEvent = new Event();
        $jumpEvent->setEventType(ActionExecutioner::TYPE)
            ->setType(CampaignActionJumpToEventSubscriber::EVENT_NAME)
            ->setCampaign($campaign);
        $jumpConfig = new ActionAccessor(
            [
                'label'                  => 'autoborna.campaign.event.jump_to_event',
                'description'            => 'autoborna.campaign.event.jump_to_event_descr',
                'formType'               => CampaignEventJumpToEventType::class,
                'template'               => 'AutobornaCampaignBundle:Event:jump.html.php',
                'batchEventName'         => CampaignEvents::ON_EVENT_JUMP_TO_EVENT,
                'connectionRestrictions' => [
                    'target' => [
                        Event::TYPE_DECISION  => ['none'],
                        Event::TYPE_ACTION    => ['none'],
                        Event::TYPE_CONDITION => ['none'],
                    ],
                ],
            ]
        );

        $events   = new ArrayCollection([$otherEvent, $jumpEvent]);
        $contacts = new ArrayCollection([new Lead()]);

        $this->eventCollector->method('getEventConfig')
            ->willReturnCallback(
                function (Event $event) use ($jumpConfig, $otherConfig) {
                    if (CampaignActionJumpToEventSubscriber::EVENT_NAME === $event->getType()) {
                        return $jumpConfig;
                    }

                    return $otherConfig;
                }
            );

        $this->eventScheduler->expects($this->exactly(2))
            ->method('getExecutionDateTime')
            ->willReturn(new \DateTime());

        $this->eventLogger->expects($this->exactly(2))
            ->method('fetchRotationAndGenerateLogsFromContacts')
            ->willReturnCallback(
                function (Event $event, ActionAccessor $config, ArrayCollection $contacts, $isInactiveEntry) {
                    $logs = new ArrayCollection();
                    foreach ($contacts as $contact) {
                        $log = new LeadEventLog();
                        $log->setLead($contact);
                        $log->setEvent($event);
                        $log->setCampaign($event->getCampaign());
                        $logs->add($log);
                    }

                    return $logs;
                }
            );

        $this->actionExecutioner->expects($this->exactly(2))
            ->method('execute')
            ->withConsecutive(
                [
                    $otherConfig,
                    $this->isInstanceOf(ArrayCollection::class),
                ],
                [
                    $jumpConfig,
                    $this->isInstanceOf(ArrayCollection::class),
                ]
            )
            ->willReturn(new EvaluatedContacts());

        $this->leadRepository->expects($this->once())
            ->method('incrementCampaignRotationForContacts');

        $this->getEventExecutioner()->executeEventsForContacts($events, $contacts);
    }

    /**
     * @return EventExecutioner
     */
    private function getEventExecutioner()
    {
        return new EventExecutioner(
            $this->eventCollector,
            $this->eventLogger,
            $this->actionExecutioner,
            $this->conditionExecutioner,
            $this->decisionExecutioner,
            $this->logger,
            $this->eventScheduler,
            $this->removedContactTracker,
            $this->leadRepository
        );
    }

    public function testJumpToEventsExecutedWithoutTarget()
    {
        $campaign = new Campaign();

        $event = new Event();
        $event->setEventType(ActionExecutioner::TYPE)
            ->setType(CampaignActionJumpToEventSubscriber::EVENT_NAME)
            ->setCampaign($campaign)
            ->setProperties(['jumpToEvent' => 999]);

        $lead = $this->getMockBuilder(Lead::class)
            ->getMock();
        $lead->method('getId')
            ->willReturn(1);

        $log = $this->getMockBuilder(LeadEventLog::class)
            ->getMock();
        $log->method('getLead')
            ->willReturn($lead);
        $log->method('setIsScheduled')
            ->willReturn($log);
        $log->method('getEvent')
            ->willReturn($event);
        $log->method('getId')
            ->willReturn(1);

        $logs = new ArrayCollection(
            [
                1 => $log,
            ]
        );

        $config = new ActionAccessor(
            [
                'label'                  => 'autoborna.campaign.event.jump_to_event',
                'description'            => 'autoborna.campaign.event.jump_to_event_descr',
                'formType'               => CampaignEventJumpToEventType::class,
                'template'               => 'AutobornaCampaignBundle:Event:jump.html.php',
                'batchEventName'         => CampaignEvents::ON_EVENT_JUMP_TO_EVENT,
                'connectionRestrictions' => [
                    'target' => [
                        Event::TYPE_DECISION  => ['none'],
                        Event::TYPE_ACTION    => ['none'],
                        Event::TYPE_CONDITION => ['none'],
                    ],
                ],
            ]
        );

        $pendingEvent = new PendingEvent($config, $event, $logs);

        $this->eventRepository->method('getEntities')
            ->willReturn([]);

        $subscriber = new CampaignActionJumpToEventSubscriber($this->eventRepository, $this->getEventExecutioner(), $this->translator, $this->leadRepository);
        $subscriber->onJumpToEvent($pendingEvent);

        $this->assertEquals(count($pendingEvent->getSuccessful()), 1);
        $this->assertEquals(count($pendingEvent->getFailures()), 0);
    }
}
