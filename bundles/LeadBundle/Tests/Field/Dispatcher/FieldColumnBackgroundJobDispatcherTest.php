<?php

declare(strict_types=1);

namespace Autoborna\LeadBundle\Tests\Field\Dispatcher;

use Autoborna\LeadBundle\Entity\LeadField;
use Autoborna\LeadBundle\Exception\NoListenerException;
use Autoborna\LeadBundle\Field\Dispatcher\FieldColumnBackgroundJobDispatcher;
use Autoborna\LeadBundle\Field\Event\AddColumnBackgroundEvent;
use Autoborna\LeadBundle\Field\Exception\AbortColumnCreateException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FieldColumnBackgroundJobDispatcherTest extends \PHPUnit\Framework\TestCase
{
    public function testNoListener(): void
    {
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher
            ->expects($this->once())
            ->method('hasListeners')
            ->willReturn(false);
        $dispatcher
            ->expects($this->never())
            ->method('dispatch');

        $fieldColumnBackgroundJobDispatcher = new FieldColumnBackgroundJobDispatcher($dispatcher);

        $leadField = new LeadField();

        $this->expectException(NoListenerException::class);
        $this->expectExceptionMessage('There is no Listener for this event');

        $fieldColumnBackgroundJobDispatcher->dispatchPreAddColumnEvent($leadField);
    }

    public function testNormalProcess(): void
    {
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher
            ->expects($this->once())
            ->method('hasListeners')
            ->willReturn(true);

        $dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                'autoborna.lead_field_pre_add_column_background_job',
                $this->isInstanceOf(AddColumnBackgroundEvent::class)
            );

        $fieldColumnBackgroundJobDispatcher = new FieldColumnBackgroundJobDispatcher($dispatcher);

        $leadField = new LeadField();

        $fieldColumnBackgroundJobDispatcher->dispatchPreAddColumnEvent($leadField);
    }

    public function testStopPropagation(): void
    {
        $leadField = new LeadField();

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher
            ->expects($this->once())
            ->method('hasListeners')
            ->willReturn(true);

        $dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                'autoborna.lead_field_pre_add_column_background_job',
                $this->callback(function ($event) {
                    /* @var AddColumnBackgroundEvent $event */
                    $event->stopPropagation();

                    return $event instanceof AddColumnBackgroundEvent;
                })
            );

        $fieldColumnBackgroundJobDispatcher = new FieldColumnBackgroundJobDispatcher($dispatcher);

        $this->expectException(AbortColumnCreateException::class);
        $this->expectExceptionMessage('Column cannot be created now');

        $fieldColumnBackgroundJobDispatcher->dispatchPreAddColumnEvent($leadField);
    }
}
