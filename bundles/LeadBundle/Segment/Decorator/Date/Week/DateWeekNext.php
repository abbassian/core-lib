<?php

namespace Autoborna\LeadBundle\Segment\Decorator\Date\Week;

use Autoborna\CoreBundle\Helper\DateTimeHelper;

class DateWeekNext extends DateWeekAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function modifyBaseDate(DateTimeHelper $dateTimeHelper)
    {
        $dateTimeHelper->setDateTime('midnight monday next week', null);
    }
}
