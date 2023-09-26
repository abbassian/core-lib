<?php

namespace Autoborna\CampaignBundle\Tests\Helper;

use Autoborna\CampaignBundle\Entity\Event;
use Autoborna\CampaignBundle\Entity\LeadEventLog;
use Autoborna\CampaignBundle\EventCollector\Accessor\Event\AbstractEventAccessor;
use Autoborna\CampaignBundle\Helper\ChannelExtractor;

class ChannelExtractorTest extends \PHPUnit\Framework\TestCase
{
    public function testChannelIsSet()
    {
        $event  = new Event();
        $config = $this->createMock(AbstractEventAccessor::class);
        $config->expects($this->once())
            ->method('getChannel')
            ->willReturn('email');

        $log = new LeadEventLog();
        ChannelExtractor::setChannel($log, $event, $config);

        $this->assertEquals('email', $log->getChannel());
    }

    public function testChannelIsIgnoredIfSet()
    {
        $event  = new Event();
        $config = $this->createMock(AbstractEventAccessor::class);
        $config->expects($this->never())
            ->method('getChannel');

        $log = new LeadEventLog();
        $log->setChannel('page');
        ChannelExtractor::setChannel($log, $event, $config);

        $this->assertEquals('page', $log->getChannel());
    }

    public function testChannelIdIsSet()
    {
        $event = new Event();
        $event->setProperties(['email' => 1]);
        $config = $this->createMock(AbstractEventAccessor::class);
        $config->expects($this->once())
            ->method('getChannel')
            ->willReturn('email');

        $config->expects($this->once())
            ->method('getChannelIdField')
            ->willReturn('email');

        $log = new LeadEventLog();
        ChannelExtractor::setChannel($log, $event, $config);

        $this->assertEquals('email', $log->getChannel());
        $this->assertEquals(1, $log->getChannelId());
    }

    public function testChannelIdIsIgnoredIfPropertiesAreEmpty()
    {
        $event = new Event();
        $event->setProperties(null);
        $config = $this->createMock(AbstractEventAccessor::class);
        $config->expects($this->once())
            ->method('getChannel')
            ->willReturn('email');

        $config->expects($this->once())
            ->method('getChannelIdField')
            ->willReturn('email');

        $log = new LeadEventLog();
        ChannelExtractor::setChannel($log, $event, $config);

        $this->assertEquals('email', $log->getChannel());
        $this->assertEquals(null, $log->getChannelId());
    }

    public function testChannelIdIsIgnoredIfChannelIdFieldIsNotSet()
    {
        $event = new Event();
        $event->setProperties(['email' => 1]);
        $config = $this->createMock(AbstractEventAccessor::class);
        $config->expects($this->once())
            ->method('getChannel')
            ->willReturn('email');

        $config->expects($this->once())
            ->method('getChannelIdField')
            ->willReturn(null);

        $log = new LeadEventLog();
        ChannelExtractor::setChannel($log, $event, $config);

        $this->assertEquals('email', $log->getChannel());
        $this->assertEquals(null, $log->getChannelId());
    }
}
