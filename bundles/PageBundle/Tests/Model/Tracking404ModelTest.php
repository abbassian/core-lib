<?php

namespace Autoborna\PageBundle\Tests\Model;

use Autoborna\CoreBundle\Helper\CoreParametersHelper;
use Autoborna\LeadBundle\Entity\Lead;
use Autoborna\LeadBundle\Tracker\ContactTracker;
use Autoborna\PageBundle\Model\PageModel;
use Autoborna\PageBundle\Model\Tracking404Model;

class Tracking404ModelTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ContactTracker|\PHPUnit\Framework\MockObject\MockObject
     */
    private $mockContactTracker;

    /**
     * @var CoreParametersHelper|\PHPUnit\Framework\MockObject\MockObject
     */
    private $mockCoreParametersHelper;

    /**
     * @var PageModel|\PHPUnit\Framework\MockObject\MockObject
     */
    private $mockPageModel;

    /**
     * @var Lead|\PHPUnit\Framework\MockObject\MockObject
     */
    private $lead;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockCoreParametersHelper = $this->createMock(CoreParametersHelper::class);

        $this->mockContactTracker = $this->createMock(ContactTracker::class);

        $this->mockPageModel = $this->createMock(PageModel::class);

        $this->lead = new Lead();
    }

    public function testIsTrackableIfTracking404OptionEnabled(): void
    {
        $this->mockCoreParametersHelper->expects($this->once())
            ->method('get')
            ->with('do_not_track_404_anonymous')
            ->willReturn(true);

        $tracking404Model = new Tracking404Model($this->mockCoreParametersHelper, $this->mockContactTracker, $this->mockPageModel);
        $this->assertFalse($tracking404Model->isTrackable());
    }

    public function testIsTrackableIfTracking404OptionDisable(): void
    {
        $this->mockCoreParametersHelper->expects($this->once())
            ->method('get')
            ->with('do_not_track_404_anonymous')
            ->willReturn(false);

        $tracking404Model = new Tracking404Model($this->mockCoreParametersHelper, $this->mockContactTracker, $this->mockPageModel);
        $this->assertTrue($tracking404Model->isTrackable());
    }

    public function testIsTrackableForIdentifiedContacts(): void
    {
        $this->mockCoreParametersHelper->expects($this->once())
            ->method('get')
            ->with('do_not_track_404_anonymous')
            ->willReturn(true);

        $this->lead->setFirstname('identified');
        $this->mockContactTracker->expects($this->any())
            ->method('getContactByTrackedDevice')
            ->willReturn($this->lead);

        $tracking404Model = new Tracking404Model($this->mockCoreParametersHelper, $this->mockContactTracker, $this->mockPageModel);
        $this->assertTrue($tracking404Model->isTrackable());
    }

    public function testIsTrackableForAnonymouse(): void
    {
        $this->mockCoreParametersHelper->expects($this->once())
            ->method('get')
            ->with('do_not_track_404_anonymous')
            ->willReturn(true);

        $this->mockContactTracker->expects($this->any())
            ->method('getContactByTrackedDevice')
            ->willReturn($this->lead);

        $tracking404Model = new Tracking404Model($this->mockCoreParametersHelper, $this->mockContactTracker, $this->mockPageModel);
        $this->assertFalse($tracking404Model->isTrackable());
    }
}
