<?php

declare(strict_types=1);

namespace Autoborna\PointBundle\Tests\Unit\Model;

use Doctrine\ORM\EntityManager;
use Autoborna\CoreBundle\Factory\AutobornaFactory;
use Autoborna\CoreBundle\Helper\IpLookupHelper;
use Autoborna\EmailBundle\EmailEvents;
use Autoborna\LeadBundle\Entity\Lead;
use Autoborna\LeadBundle\Model\LeadModel;
use Autoborna\LeadBundle\Tracker\ContactTracker;
use Autoborna\PointBundle\Entity\TriggerEvent;
use Autoborna\PointBundle\Entity\TriggerEventRepository;
use Autoborna\PointBundle\Event\TriggerBuilderEvent;
use Autoborna\PointBundle\Event\TriggerExecutedEvent;
use Autoborna\PointBundle\Model\TriggerEventModel;
use Autoborna\PointBundle\Model\TriggerModel;
use Autoborna\PointBundle\PointEvents;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Translation\TranslatorInterface;

class TriggerModelTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var IpLookupHelper|MockObject
     */
    private $ipLookupHelper;

    /**
     * @var LeadModel|MockObject
     */
    private $leadModel;

    /**
     * @var TriggerEventModel|MockObject
     */
    private $triggerEventModel;

    /**
     * @var AutobornaFactory|MockObject
     */
    private $autobornaFactory;

    /**
     * @var EventDispatcherInterface|MockObject
     */
    private $dispatcher;

    /**
     * @var TranslatorInterface|MockObject
     */
    private $translator;

    /**
     * @var EntityManager|MockObject
     */
    private $entityManager;

    /**
     * @var TriggerEventRepository|MockObject
     */
    private $triggerEventRepository;

    /**
     * @var TriggerModel
     */
    private $triggerModel;

    /**
     * @var ContactTracker
     */
    private $contactTracker;

    public function setUp(): void
    {
        parent::setUp();
        $this->ipLookupHelper         = $this->createMock(IpLookupHelper::class);
        $this->leadModel              = $this->createMock(LeadModel::class);
        $this->triggerEventModel      = $this->createMock(TriggerEventModel::class);
        $this->autobornaFactory          = $this->createMock(AutobornaFactory::class);
        $this->contactTracker         = $this->createMock(ContactTracker::class);
        $this->dispatcher             = $this->createMock(EventDispatcherInterface::class);
        $this->translator             = $this->createMock(TranslatorInterface::class);
        $this->entityManager          = $this->createMock(EntityManager::class);
        $this->triggerEventRepository = $this->createMock(TriggerEventRepository::class);
        $this->triggerModel           = new TriggerModel(
            $this->ipLookupHelper,
            $this->leadModel,
            $this->triggerEventModel,
            $this->autobornaFactory,
            $this->contactTracker
        );

        $this->triggerModel->setDispatcher($this->dispatcher);
        $this->triggerModel->setTranslator($this->translator);
        $this->triggerModel->setEntityManager($this->entityManager);
    }

    public function testTriggerEvent(): void
    {
        $triggerEvent = new TriggerEvent();
        $contact      = new Lead();

        $triggerEvent->setType('email.send_to_user');

        $this->entityManager->expects($this->once())
            ->method('getRepository')
            ->willReturn($this->triggerEventRepository);

        $this->triggerEventRepository->expects($this->once())
            ->method('find')
            ->willReturn($triggerEvent);

        $this->dispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [
                    PointEvents::TRIGGER_ON_BUILD,
                    $this->callback(
                        // Emulate a subscriber:
                        function (TriggerBuilderEvent $event) {
                            // PHPUNIT calls this callback twice for unknown reason. We need to set it only once.
                            if (array_key_exists('email.send_to_user', $event->getEvents())) {
                                return true;
                            }

                            $event->addEvent(
                                'email.send_to_user',
                                [
                                    'group'           => 'autoborna.email.point.trigger',
                                    'label'           => 'autoborna.email.point.trigger.send_email_to_user',
                                    'formType'        => \Autoborna\EmailBundle\Form\Type\EmailToUserType::class,
                                    'formTypeOptions' => ['update_select' => 'pointtriggerevent_properties_email'],
                                    'formTheme'       => 'AutobornaEmailBundle:FormTheme\EmailSendList',
                                    'eventName'       => EmailEvents::ON_SENT_EMAIL_TO_USER,
                                ]
                            );

                            return true;
                        }
                    ),
                ],
                // Ensure the event is triggered if the point trigger event has 'eventName' defined instead of 'callback'.
                [
                    EmailEvents::ON_SENT_EMAIL_TO_USER,
                    $this->callback(
                        function (TriggerExecutedEvent $event) use ($contact, $triggerEvent) {
                            $this->assertSame($contact, $event->getLead());
                            $this->assertSame($triggerEvent, $event->getTriggerEvent());

                            return true;
                        }
                    ),
                ]
            );

        $this->triggerModel->triggerEvent($triggerEvent->convertToArray(), $contact, true);
    }
}
