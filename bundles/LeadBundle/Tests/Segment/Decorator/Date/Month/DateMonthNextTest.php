<?php

namespace Autoborna\LeadBundle\Tests\Segment\Decorator\Date\Month;

use Autoborna\CoreBundle\Helper\DateTimeHelper;
use Autoborna\LeadBundle\Segment\ContactSegmentFilterCrate;
use Autoborna\LeadBundle\Segment\Decorator\Date\DateOptionParameters;
use Autoborna\LeadBundle\Segment\Decorator\Date\Month\DateMonthNext;
use Autoborna\LeadBundle\Segment\Decorator\Date\TimezoneResolver;
use Autoborna\LeadBundle\Segment\Decorator\DateDecorator;

class DateMonthNextTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \Autoborna\LeadBundle\Segment\Decorator\Date\Month\DateMonthNext::getOperator
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

        $filterDecorator = new DateMonthNext($dateDecorator, $dateOptionParameters);

        $this->assertEquals('like', $filterDecorator->getOperator($contactSegmentFilterCrate));
    }

    /**
     * @covers \Autoborna\LeadBundle\Segment\Decorator\Date\Month\DateMonthNext::getOperator
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

        $filterDecorator = new DateMonthNext($dateDecorator, $dateOptionParameters);

        $this->assertEquals('=<', $filterDecorator->getOperator($contactSegmentFilterCrate));
    }

    /**
     * @covers \Autoborna\LeadBundle\Segment\Decorator\Date\Month\DateMonthNext::getParameterValue
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

        $filterDecorator = new DateMonthNext($dateDecorator, $dateOptionParameters);

        $expectedDate = new \DateTime('first day of next month');

        $this->assertEquals($expectedDate->format('Y-m-%'), $filterDecorator->getParameterValue($contactSegmentFilterCrate));
    }

    /**
     * @covers \Autoborna\LeadBundle\Segment\Decorator\Date\Month\DateMonthNext::getParameterValue
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

        $filterDecorator = new DateMonthNext($dateDecorator, $dateOptionParameters);

        $expectedDate = new \DateTime('first day of next month');

        $this->assertEquals($expectedDate->format('Y-m-d'), $filterDecorator->getParameterValue($contactSegmentFilterCrate));
    }
}