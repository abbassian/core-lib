<?php

namespace Autoborna\CampaignBundle\Tests\Event;

use Doctrine\Common\Collections\ArrayCollection;
use Autoborna\CampaignBundle\Entity\Event;
use Autoborna\CampaignBundle\Entity\LeadEventLog;
use Autoborna\CampaignBundle\Event\PendingEvent;
use Autoborna\CampaignBundle\EventCollector\Accessor\Event\ActionAccessor;
use Autoborna\LeadBundle\Entity\Lead;
use PHPUnit\Framework\Assert;

final class PendingEventTest extends \PHPUnit\Framework\TestCase
{
    public function testFailAndPassRemainingWithError(): void
    {
        $event    = new Event();
        $contact  = new Lead();
        $logA     = new LeadEventLog();
        $logB     = new LeadEventLog();
        $interval = new \DateInterval('PT10M');

        $logA->setLead($contact); // Will fail.
        $logB->setLead($contact); // Will pass with error.

        $pendingEvent = new PendingEvent(new ActionAccessor([]), $event, new ArrayCollection([$logA, $logB]));

        $pendingEvent->fail($logA, 'reason A', $interval);
        $pendingEvent->passRemainingWithError('Error B');

        $failedLogs  = $pendingEvent->getFailures();
        $successLogs = $pendingEvent->getSuccessful();

        Assert::assertCount(1, $failedLogs);
        Assert::assertCount(1, $successLogs);
        Assert::AssertSame($logA, $failedLogs->current());
        Assert::AssertSame($logB, $successLogs->current());
        Assert::AssertSame($interval, $logA->getRescheduleInterval());
        Assert::AssertSame(['failed' => 1, 'reason' => 'reason A'], $logA->getMetadata());
        Assert::AssertSame(['failed' => 1, 'reason' => 'Error B'], $logB->getMetadata());
        Assert::AssertNull($logB->getRescheduleInterval());
    }
}
