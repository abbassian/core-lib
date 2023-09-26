<?php

namespace Autoborna\LeadBundle\Segment\Decorator\Date\Day;

use Autoborna\CoreBundle\Helper\DateTimeHelper;

class DateDayTomorrow extends DateDayAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function modifyBaseDate(DateTimeHelper $dateTimeHelper)
    {
        $dateTimeHelper->modify('+1 day');
    }
}
