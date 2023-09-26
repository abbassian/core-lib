<?php

namespace Autoborna\LeadBundle\Segment\Decorator\Date\Month;

use Autoborna\CoreBundle\Helper\DateTimeHelper;

class DateMonthThis extends DateMonthAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function modifyBaseDate(DateTimeHelper $dateTimeHelper)
    {
        $dateTimeHelper->setDateTime('midnight first day of this month', null);
    }
}
