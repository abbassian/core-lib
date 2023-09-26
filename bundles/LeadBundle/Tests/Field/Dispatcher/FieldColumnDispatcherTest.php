<?php

declare(strict_types=1);

namespace Autoborna\LeadBundle\Tests\Field\Dispatcher;

use Autoborna\LeadBundle\Entity\LeadField;
use Autoborna\LeadBundle\Field\Dispatcher\FieldColumnDispatcher;
use Autoborna\LeadBundle\Field\Event\AddColumnEvent;
use Autoborna\LeadBundle\Field\Exception\AbortColumnCreateException;
use Autoborna\LeadBundle\Field\Settings\BackgroundSettings;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FieldColumnDispatcherTest extends \PHPUnit\Framework\TestCase
{
    public function testNoBackground(): void
    {
        $dispatcher         = $this->createMock(EventDispatcherInterface::class);
        $backgroundSettings = $this->createMock(BackgroundSettings::class);
        $leadField          = new LeadField();

        $backgroundSettings->expects($this->once())
            ->method('shouldProcessColumnChangeInBackground')
            ->willReturn(false);

        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with(
                'autoborna.lead_field_pre_add_column',
                $this->isInstanceOf(AddColumnEvent::class)
            );

        $fieldColumnDispatcher = new FieldColumnDispatcher($dispatcher, $backgroundSettings);

        $fieldColumnDispatcher->dispatchPreAddColumnEvent($leadField);
    }

    public function testStopPropagation(): void
    {
        $leadField          = new LeadField();
        $dispatcher         = $this->createMock(EventDispatcherInterface::class);
        $backgroundSettings = $this->createMock(BackgroundSettings::class);

        $backgroundSettings->expects($this->once())
            ->method('shouldProcessColumnChangeInBackground')
            ->willReturn(true);

        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with(
                'autoborna.lead_field_pre_add_column',
                $this->callback(function ($event) {
                    /* @var AddColumnBackgroundEvent $event */
                    return $event instanceof AddColumnEvent;
                })
            );

        $fieldColumnDispatcher = new FieldColumnDispatcher($dispatcher, $backgroundSettings);

        $this->expectException(AbortColumnCreateException::class);
        $this->expectExceptionMessage('Column change will be processed in background job');

        $fieldColumnDispatcher->dispatchPreAddColumnEvent($leadField);
    }
}
