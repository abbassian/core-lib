<?php

namespace Autoborna\CoreBundle\Tests\Unit\Helper\Chart;

use Autoborna\CoreBundle\Helper\Chart\LineChart;

class LineChartTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @testdox The getUnitInterval returns the correct interval objects
     *
     * @covers \Autoborna\CoreBundle\Helper\Chart\LineChart::getUnitInterval
     */
    public function testGetUnitInterval()
    {
        $units = [
            'Y' => new \DateInterval('P1Y'),
            'm' => new \DateInterval('P1M'),
            'W' => new \DateInterval('P1W'),
            'd' => new \DateInterval('P1D'),
            'H' => new \DateInterval('PT1H'),
            'i' => new \DateInterval('PT1M'),
            's' => new \DateInterval('PT1S'),
        ];

        foreach ($units as $unit => $expected) {
            $chart    = new LineChart($unit, new \DateTime(), new \DateTime());
            $interval = $chart->getUnitInterval();
            $this->assertEquals($expected, $interval);
        }
    }
}
