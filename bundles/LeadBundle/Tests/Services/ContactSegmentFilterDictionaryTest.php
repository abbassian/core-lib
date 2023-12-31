<?php

namespace Autoborna\LeadBundle\Tests\Services;

use Autoborna\LeadBundle\Event\SegmentDictionaryGenerationEvent;
use Autoborna\LeadBundle\Exception\FilterNotFoundException;
use Autoborna\LeadBundle\LeadEvents;
use Autoborna\LeadBundle\Services\ContactSegmentFilterDictionary;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ContactSegmentFilterDictionaryTest extends TestCase
{
    public function testWorkflow(): void
    {
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dictionary = new ContactSegmentFilterDictionary($dispatcher);

        $dispatcher->expects($this->once())
            ->method('hasListeners')
            ->with(LeadEvents::SEGMENT_DICTIONARY_ON_GENERATE)
            ->willReturn(true);

        // Subscribe new filter like a plugin would.
        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with(LeadEvents::SEGMENT_DICTIONARY_ON_GENERATE, $this->callback(function (SegmentDictionaryGenerationEvent $event) {
                $event->addTranslation('plugin_key', ['type' => 'blah blah']);

                return true;
            }));

        $this->assertSame(['type' => 'autoborna.lead.query.builder.special.dnc'], $dictionary->getFilter('dnc_bounced'));
        $this->assertSame('campaign_leads.manually_removed = 0', $dictionary->getFilterProperty('campaign', 'where'));
        $this->assertSame('blah blah', $dictionary->getFilterProperty('plugin_key', 'type'));

        $this->expectException(FilterNotFoundException::class);
        $dictionary->getFilterProperty('unicorn', 'type');
    }
}
