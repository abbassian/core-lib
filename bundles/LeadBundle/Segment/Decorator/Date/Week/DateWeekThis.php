<?php

namespace Autoborna\LeadBundle\Segment\Decorator\Date\Week;

use Autoborna\CoreBundle\Helper\DateTimeHelper;

class DateWeekThis extends DateWeekAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function modifyBaseDate(DateTimeHelper $dateTimeHelper)
    {
        $dateTimeHelper->setDateTime('midnight monday this week', null);
    }
}
