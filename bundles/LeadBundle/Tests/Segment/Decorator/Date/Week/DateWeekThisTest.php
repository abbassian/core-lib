<?php

namespace Autoborna\LeadBundle\Tests\Segment\Decorator\Date\Week;

use Autoborna\CoreBundle\Helper\DateTimeHelper;
use Autoborna\LeadBundle\Segment\ContactSegmentFilterCrate;
use Autoborna\LeadBundle\Segment\Decorator\Date\DateOptionParameters;
use Autoborna\LeadBundle\Segment\Decorator\Date\TimezoneResolver;
use Autoborna\LeadBundle\Segment\Decorator\Date\Week\DateWeekThis;
use Autoborna\LeadBundle\Segment\Decorator\DateDecorator;

class DateWeekThisTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \Autoborna\LeadBundle\Segment\Decorator\Date\Week\DateWeekLast::getOperator
     */
    public function testGetOperatorBetween()
    {
        $dateDecorator    = $this->createMock(DateDecorator::class);
        $timezoneResolver = $this->createMock(TimezoneResolver::class);

        $filter        = [
            'operator' => '=',
        ];
        $contactSegmentFilterCrate = new ContactSegmentFilterCrate($filter);
        $dateOptionParameters      = new DateOptionParameters($contactSegmentFilterCrate, [], $timezoneResolver);

        $filterDecorator = new DateWeekThis($dateDecorator, $dateOptionParameters);

        $this->assertEquals('between', $filterDecorator->getOperator($contactSegmentFilterCrate));
    }

    /**
     * @covers \Autoborna\LeadBundle\Segment\Decorator\Date\Week\DateWeekLast::getOperator
     */
    public function testGetOperatorLessOrEqual()
    {
        $dateDecorator    = $this->createMock(DateDecorator::class);
        $timezoneResolver = $this->createMock(TimezoneResolver::class);
        $dateDecorator->method('getOperator')
            ->with()
            ->willReturn('=<');

        $filter        = [
            'operator' => 'lte',
        ];
        $contactSegmentFilterCrate = new ContactSegmentFilterCrate($filter);
        $dateOptionParameters      = new DateOptionParameters($contactSegmentFilterCrate, [], $timezoneResolver);

        $filterDecorator = new DateWeekThis($dateDecorator, $dateOptionParameters);

        $this->assertEquals('=<', $filterDecorator->getOperator($contactSegmentFilterCrate));
    }

    /**
     * @covers \Autoborna\LeadBundle\Segment\Decorator\Date\Week\DateWeekLast::getParameterValue
     */
    public function testGetParameterValueBetween()
    {
        $dateDecorator    = $this->createMock(DateDecorator::class);
        $timezoneResolver = $this->createMock(TimezoneResolver::class);

        $date = new DateTimeHelper('', null, 'local');

        $timezoneResolver->method('getDefaultDate')
            ->with()
            ->willReturn($date);

        $filter        = [
            'operator' => '!=',
        ];
        $contactSegmentFilterCrate = new ContactSegmentFilterCrate($filter);
        $dateOptionParameters      = new DateOptionParameters($contactSegmentFilterCrate, [], $timezoneResolver);

        $filterDecorator = new DateWeekThis($dateDecorator, $dateOptionParameters);

        $expectedDateStart = new \DateTime('monday this week');
        $expectedDateEnd   = new \DateTime('sunday this week');

        $this->assertEquals(
            [
                $expectedDateStart->format('Y-m-d'),
                $expectedDateEnd->format('Y-m-d'),
            ],
            $filterDecorator->getParameterValue($contactSegmentFilterCrate)
        );
    }

    /**
     * @covers \Autoborna\LeadBundle\Segment\Decorator\Date\Week\DateWeekLast::getParameterValue
     */
    public function testGetParameterValueSingle()
    {
        $dateDecorator    = $this->createMock(DateDecorator::class);
        $timezoneResolver = $this->createMock(TimezoneResolver::class);

        $date = new DateTimeHelper('', null, 'local');

        $timezoneResolver->method('getDefaultDate')
            ->with()
            ->willReturn($date);

        $filter        = [
            'operator' => 'lt',
        ];
        $contactSegmentFilterCrate = new ContactSegmentFilterCrate($filter);
        $dateOptionParameters      = new DateOptionParameters($contactSegmentFilterCrate, [], $timezoneResolver);

        $filterDecorator = new DateWeekThis($dateDecorator, $dateOptionParameters);

        $expectedDate = new \DateTime('monday this week');

        $this->assertEquals($expectedDate->format('Y-m-d'), $filterDecorator->getParameterValue($contactSegmentFilterCrate));
    }

    /**
     * @covers \Autoborna\LeadBundle\Segment\Decorator\Date\Week\DateWeekLast::getParameterValue
     */
    public function testGetParameterValueforGreaterOperatorIncludesSunday()
    {
        $dateDecorator    = $this->createMock(DateDecorator::class);
        $timezoneResolver = $this->createMock(TimezoneResolver::class);

        $date = new DateTimeHelper('', null, 'local');
        $timezoneResolver->method('getDefaultDate')
            ->with()
            ->willReturn($date);

        $filter        = [
            'operator' => 'gt',
        ];
        $contactSegmentFilterCrate = new ContactSegmentFilterCrate($filter);
        $dateOptionParameters      = new DateOptionParameters($contactSegmentFilterCrate, [], $timezoneResolver);

        $filterDecorator = new DateWeekThis($dateDecorator, $dateOptionParameters);

        $expectedDate = new \DateTime('sunday this week');

        $this->assertEquals($expectedDate->format('Y-m-d'), $filterDecorator->getParameterValue($contactSegmentFilterCrate));
    }

    /**
     * @covers \Autoborna\LeadBundle\Segment\Decorator\Date\Week\DateWeekLast::getParameterValue
     */
    public function testGetParameterValueForLessThanOperatorIncludesSunday()
    {
        $dateDecorator    = $this->createMock(DateDecorator::class);
        $timezoneResolver = $this->createMock(TimezoneResolver::class);

        $date = new DateTimeHelper('', null, 'local');
        $timezoneResolver->method('getDefaultDate')
            ->with()
            ->willReturn($date);

        $filter        = [
            'operator' => 'lte',
        ];
        $contactSegmentFilterCrate = new ContactSegmentFilterCrate($filter);
        $dateOptionParameters      = new DateOptionParameters($contactSegmentFilterCrate, [], $timezoneResolver);

        $filterDecorator = new DateWeekThis($dateDecorator, $dateOptionParameters);

        $expectedDate = new \DateTime('sunday this week');

        $this->assertEquals($expectedDate->format('Y-m-d'), $filterDecorator->getParameterValue($contactSegmentFilterCrate));
    }
}
