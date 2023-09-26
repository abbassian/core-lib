<?php

namespace Autoborna\LeadBundle\Segment\Decorator\Date\Week;

use Autoborna\CoreBundle\Helper\DateTimeHelper;

class DateWeekLast extends DateWeekAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function modifyBaseDate(DateTimeHelper $dateTimeHelper)
    {
        $dateTimeHelper->setDateTime('midnight monday last week', null);
    }
}
