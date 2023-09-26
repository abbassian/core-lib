<?php

namespace Autoborna\LeadBundle\Tests\Segment\Decorator\Date;

use Autoborna\LeadBundle\Segment\ContactSegmentFilterCrate;
use Autoborna\LeadBundle\Segment\Decorator\Date\DateOptionFactory;
use Autoborna\LeadBundle\Segment\Decorator\Date\Day\DateDayToday;
use Autoborna\LeadBundle\Segment\Decorator\Date\Day\DateDayTomorrow;
use Autoborna\LeadBundle\Segment\Decorator\Date\Day\DateDayYesterday;
use Autoborna\LeadBundle\Segment\Decorator\Date\Month\DateMonthLast;
use Autoborna\LeadBundle\Segment\Decorator\Date\Month\DateMonthNext;
use Autoborna\LeadBundle\Segment\Decorator\Date\Month\DateMonthThis;
use Autoborna\LeadBundle\Segment\Decorator\Date\Other\DateAnniversary;
use Autoborna\LeadBundle\Segment\Decorator\Date\Other\DateDefault;
use Autoborna\LeadBundle\Segment\Decorator\Date\Other\DateRelativeInterval;
use Autoborna\LeadBundle\Segment\Decorator\Date\TimezoneResolver;
use Autoborna\LeadBundle\Segment\Decorator\Date\Week\DateWeekLast;
use Autoborna\LeadBundle\Segment\Decorator\Date\Week\DateWeekNext;
use Autoborna\LeadBundle\Segment\Decorator\Date\Week\DateWeekThis;
use Autoborna\LeadBundle\Segment\Decorator\Date\Year\DateYearLast;
use Autoborna\LeadBundle\Segment\Decorator\Date\Year\DateYearNext;
use Autoborna\LeadBundle\Segment\Decorator\Date\Year\DateYearThis;
use Autoborna\LeadBundle\Segment\Decorator\DateDecorator;
use Autoborna\LeadBundle\Segment\RelativeDate;

class DateOptionFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \Autoborna\LeadBundle\Segment\Decorator\Date\DateOptionFactory::getDateOption
     */
    public function testBirthday()
    {
        $filterName = 'birthday';

        $filterDecorator = $this->getFilterDecorator($filterName);

        $this->assertInstanceOf(DateAnniversary::class, $filterDecorator);

        $filterName = 'anniversary';

        $filterDecorator = $this->getFilterDecorator($filterName);

        $this->assertInstanceOf(DateAnniversary::class, $filterDecorator);
    }

    /**
     * @covers \Autoborna\LeadBundle\Segment\Decorator\Date\DateOptionFactory::getDateOption
     */
    public function testDayToday()
    {
        $filterName = 'today';

        $filterDecorator = $this->getFilterDecorator($filterName);

        $this->assertInstanceOf(DateDayToday::class, $filterDecorator);
    }

    /**
     * @covers \Autoborna\LeadBundle\Segment\Decorator\Date\DateOptionFactory::getDateOption
     */
    public function testDayTomorrow()
    {
        $filterName = 'tomorrow';

        $filterDecorator = $this->getFilterDecorator($filterName);

        $this->assertInstanceOf(DateDayTomorrow::class, $filterDecorator);
    }

    /**
     * @covers \Autoborna\LeadBundle\Segment\Decorator\Date\DateOptionFactory::getDateOption
     */
    public function testDayYesterday()
    {
        $filterName = 'yesterday';

        $filterDecorator = $this->getFilterDecorator($filterName);

        $this->assertInstanceOf(DateDayYesterday::class, $filterDecorator);
    }

    /**
     * @covers \Autoborna\LeadBundle\Segment\Decorator\Date\DateOptionFactory::getDateOption
     */
    public function testWeekLast()
    {
        $filterName = 'last week';

        $filterDecorator = $this->getFilterDecorator($filterName);

        $this->assertInstanceOf(DateWeekLast::class, $filterDecorator);
    }

    /**
     * @covers \Autoborna\LeadBundle\Segment\Decorator\Date\DateOptionFactory::getDateOption
     */
    public function testWeekNext()
    {
        $filterName = 'next week';

        $filterDecorator = $this->getFilterDecorator($filterName);

        $this->assertInstanceOf(DateWeekNext::class, $filterDecorator);
    }

    /**
     * @covers \Autoborna\LeadBundle\Segment\Decorator\Date\DateOptionFactory::getDateOption
     */
    public function testWeekThis()
    {
        $filterName = 'this week';

        $filterDecorator = $this->getFilterDecorator($filterName);

        $this->assertInstanceOf(DateWeekThis::class, $filterDecorator);
    }

    /**
     * @covers \Autoborna\LeadBundle\Segment\Decorator\Date\DateOptionFactory::getDateOption
     */
    public function testMonthLast()
    {
        $filterName = 'last month';

        $filterDecorator = $this->getFilterDecorator($filterName);

        $this->assertInstanceOf(DateMonthLast::class, $filterDecorator);
    }

    /**
     * @covers \Autoborna\LeadBundle\Segment\Decorator\Date\DateOptionFactory::getDateOption
     */
    public function testMonthNext()
    {
        $filterName = 'next month';

        $filterDecorator = $this->getFilterDecorator($filterName);

        $this->assertInstanceOf(DateMonthNext::class, $filterDecorator);
    }

    /**
     * @covers \Autoborna\LeadBundle\Segment\Decorator\Date\DateOptionFactory::getDateOption
     */
    public function testMonthThis()
    {
        $filterName = 'this month';

        $filterDecorator = $this->getFilterDecorator($filterName);

        $this->assertInstanceOf(DateMonthThis::class, $filterDecorator);
    }

    /**
     * @covers \Autoborna\LeadBundle\Segment\Decorator\Date\DateOptionFactory::getDateOption
     */
    public function testYearLast()
    {
        $filterName = 'last year';

        $filterDecorator = $this->getFilterDecorator($filterName);

        $this->assertInstanceOf(DateYearLast::class, $filterDecorator);
    }

    /**
     * @covers \Autoborna\LeadBundle\Segment\Decorator\Date\DateOptionFactory::getDateOption
     */
    public function testYearNext()
    {
        $filterName = 'next year';

        $filterDecorator = $this->getFilterDecorator($filterName);

        $this->assertInstanceOf(DateYearNext::class, $filterDecorator);
    }

    /**
     * @covers \Autoborna\LeadBundle\Segment\Decorator\Date\DateOptionFactory::getDateOption
     */
    public function testYearThis()
    {
        $filterName = 'this year';

        $filterDecorator = $this->getFilterDecorator($filterName);

        $this->assertInstanceOf(DateYearThis::class, $filterDecorator);
    }

    /**
     * @covers \Autoborna\LeadBundle\Segment\Decorator\Date\DateOptionFactory::getDateOption
     */
    public function testRelativePlus()
    {
        $filterName = '+20 days';

        $filterDecorator = $this->getFilterDecorator($filterName);

        $this->assertInstanceOf(DateRelativeInterval::class, $filterDecorator);
    }

    /**
     * @covers \Autoborna\LeadBundle\Segment\Decorator\Date\DateOptionFactory::getDateOption
     */
    public function testRelativeMinus()
    {
        $filterName = '+20 days';

        $filterDecorator = $this->getFilterDecorator($filterName);

        $this->assertInstanceOf(DateRelativeInterval::class, $filterDecorator);
    }

    /**
     * @covers \Autoborna\LeadBundle\Segment\Decorator\Date\DateOptionFactory::getDateOption
     */
    public function testRelativeAgo()
    {
        $filterName = '20 days ago';

        $filterDecorator = $this->getFilterDecorator($filterName);

        $this->assertInstanceOf(DateRelativeInterval::class, $filterDecorator);
    }

    /**
     * @covers \Autoborna\LeadBundle\Segment\Decorator\Date\DateOptionFactory::getDateOption
     */
    public function testDateDefault()
    {
        $filterName = '2018-01-01';

        $filterDecorator = $this->getFilterDecorator($filterName);

        $this->assertInstanceOf(DateDefault::class, $filterDecorator);
    }

    /**
     * @covers \Autoborna\LeadBundle\Segment\Decorator\Date\DateOptionFactory::getDateOption
     */
    public function testNullValue()
    {
        $filterName = null;

        $filterDecorator = $this->getFilterDecorator($filterName);

        $this->assertInstanceOf(DateDefault::class, $filterDecorator);
    }

    /**
     * @param string $filterName
     *
     * @return \Autoborna\LeadBundle\Segment\Decorator\FilterDecoratorInterface
     */
    private function getFilterDecorator($filterName)
    {
        $dateDecorator    = $this->createMock(DateDecorator::class);
        $relativeDate     = $this->createMock(RelativeDate::class);
        $timezoneResolver = $this->createMock(TimezoneResolver::class);

        $relativeDate->method('getRelativeDateStrings')
            ->willReturn(
                [
                    'autoborna.lead.list.month_last'  => 'last month',
                    'autoborna.lead.list.month_next'  => 'next month',
                    'autoborna.lead.list.month_this'  => 'this month',
                    'autoborna.lead.list.today'       => 'today',
                    'autoborna.lead.list.tomorrow'    => 'tomorrow',
                    'autoborna.lead.list.yesterday'   => 'yesterday',
                    'autoborna.lead.list.week_last'   => 'last week',
                    'autoborna.lead.list.week_next'   => 'next week',
                    'autoborna.lead.list.week_this'   => 'this week',
                    'autoborna.lead.list.year_last'   => 'last year',
                    'autoborna.lead.list.year_next'   => 'next year',
                    'autoborna.lead.list.year_this'   => 'this year',
                    'autoborna.lead.list.birthday'    => 'birthday',
                    'autoborna.lead.list.anniversary' => 'anniversary',
                ]
            );

        $dateOptionFactory = new DateOptionFactory($dateDecorator, $relativeDate, $timezoneResolver);

        $filter                    = [
            'glue'     => 'and',
            'type'     => 'datetime',
            'object'   => 'lead',
            'field'    => 'date_identified',
            'operator' => '=',
            'filter'   => $filterName,
            'display'  => null,
        ];
        $contactSegmentFilterCrate = new ContactSegmentFilterCrate($filter);

        return $dateOptionFactory->getDateOption($contactSegmentFilterCrate);
    }
}
