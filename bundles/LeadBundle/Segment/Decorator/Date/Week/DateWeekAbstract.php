<?php

namespace Autoborna\LeadBundle\Segment\Decorator\Date\Week;

use Autoborna\CoreBundle\Helper\DateTimeHelper;
use Autoborna\LeadBundle\Segment\ContactSegmentFilterCrate;
use Autoborna\LeadBundle\Segment\Decorator\Date\DateOptionAbstract;

abstract class DateWeekAbstract extends DateOptionAbstract
{
    /**
     * @return string
     */
    protected function getModifierForBetweenRange()
    {
        return '+1 week';
    }

    /**
     * {@inheritdoc}
     */
    protected function getValueForBetweenRange(DateTimeHelper $dateTimeHelper)
    {
        $dateFormat = $this->dateOptionParameters->hasTimePart() ? 'Y-m-d H:i:s' : 'Y-m-d';
        $startWith  = $dateTimeHelper->toLocalString($dateFormat);

        $modifier = $this->getModifierForBetweenRange().' -1 second';
        $dateTimeHelper->modify($modifier);
        $endWith = $dateTimeHelper->toLocalString($dateFormat);

        return [$startWith, $endWith];
    }

    /**
     * {@inheritdoc}
     */
    protected function getOperatorForBetweenRange(ContactSegmentFilterCrate $leadSegmentFilterCrate)
    {
        return '!=' === $leadSegmentFilterCrate->getOperator() ? 'notBetween' : 'between';
    }
}
