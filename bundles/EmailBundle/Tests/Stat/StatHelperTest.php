<?php

namespace Autoborna\EmailBundle\Tests\Stat;

use Autoborna\EmailBundle\Entity\Email;
use Autoborna\EmailBundle\Entity\Stat;
use Autoborna\EmailBundle\Entity\StatRepository;
use Autoborna\EmailBundle\Stat\Exception\StatNotFoundException;
use Autoborna\EmailBundle\Stat\StatHelper;
use Autoborna\LeadBundle\Entity\Lead;

class StatHelperTest extends \PHPUnit\Framework\TestCase
{
    public function testStatsAreCreatedAndDeleted()
    {
        $mockStatRepository = $this->getMockBuilder(StatRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockStatRepository->expects($this->once())
            ->method('deleteStats')
            ->withConsecutive([[1, 2, 3, 4, 5]]);

        $statHelper = new StatHelper($mockStatRepository);

        $mockEmail = $this->getMockBuilder(Email::class)
            ->getMock();
        $mockEmail->method('getId')
            ->willReturn(15);

        $counter = 1;
        while ($counter <= 5) {
            $stat = $this->getMockBuilder(Stat::class)
                ->getMock();

            $stat->method('getId')
                ->willReturn($counter);

            $stat->method('getEmail')
                ->willReturn($mockEmail);

            $lead = $this->getMockBuilder(Lead::class)
                ->getMock();

            $lead->method('getId')
                ->willReturn($counter * 10);

            $stat->method('getLead')
                ->willReturn($lead);

            $emailAddress = "contact{$counter}@test.com";
            $statHelper->storeStat($stat, $emailAddress);

            // Delete it
            try {
                $reference = $statHelper->getStat($emailAddress);
                $this->assertEquals($reference->getLeadId(), $counter * 10);
                $statHelper->markForDeletion($reference);
            } catch (StatNotFoundException $exception) {
                $this->fail("Stat not found for $emailAddress");
            }

            ++$counter;
        }

        $statHelper->deletePending();
    }

    public function testExceptionIsThrownIfEmailAddressIsNotFound()
    {
        $this->expectException(StatNotFoundException::class);
        $mockStatRepository = $this->getMockBuilder(StatRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $statHelper = new StatHelper($mockStatRepository);

        $reference = $statHelper->getStat('nada@nada.com');
    }
}
