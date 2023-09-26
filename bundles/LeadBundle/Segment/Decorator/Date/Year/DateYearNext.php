<?php

namespace Autoborna\LeadBundle\Segment\Decorator\Date\Year;

use Autoborna\CoreBundle\Helper\DateTimeHelper;

class DateYearNext extends DateYearAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function modifyBaseDate(DateTimeHelper $dateTimeHelper)
    {
        $dateTimeHelper->setDateTime('midnight first day of January next year', null);
    }
}
