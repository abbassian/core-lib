<?php

declare(strict_types=1);

namespace Autoborna\LeadBundle\Field\Dispatcher;

use Autoborna\LeadBundle\Entity\LeadField;
use Autoborna\LeadBundle\Field\Event\AddColumnEvent;
use Autoborna\LeadBundle\Field\Exception\AbortColumnCreateException;
use Autoborna\LeadBundle\Field\Settings\BackgroundSettings;
use Autoborna\LeadBundle\LeadEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FieldColumnDispatcher
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var BackgroundSettings
     */
    private $backgroundSettings;

    public function __construct(EventDispatcherInterface $dispatcher, BackgroundSettings $backgroundSettings)
    {
        $this->dispatcher         = $dispatcher;
        $this->backgroundSettings = $backgroundSettings;
    }

    /**
     * @throws AbortColumnCreateException
     */
    public function dispatchPreAddColumnEvent(LeadField $leadField): void
    {
        $shouldProcessInBackground = $this->backgroundSettings->shouldProcessColumnChangeInBackground();
        $event                     = new AddColumnEvent($leadField, $shouldProcessInBackground);

        $this->dispatcher->dispatch(LeadEvents::LEAD_FIELD_PRE_ADD_COLUMN, $event);

        if ($shouldProcessInBackground) {
            throw new AbortColumnCreateException('Column change will be processed in background job');
        }
    }
}
