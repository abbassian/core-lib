<?php

namespace Autoborna\ReportBundle\Scheduler\Builder;

use Autoborna\ReportBundle\Scheduler\BuilderInterface;
use Autoborna\ReportBundle\Scheduler\Exception\InvalidSchedulerException;
use Autoborna\ReportBundle\Scheduler\SchedulerInterface;
use Recurr\Exception\InvalidArgument;
use Recurr\Rule;

class SchedulerDailyBuilder implements BuilderInterface
{
    /**
     * @return Rule
     *
     * @throws InvalidSchedulerException
     */
    public function build(Rule $rule, SchedulerInterface $scheduler)
    {
        try {
            $rule->setFreq('DAILY');
        } catch (InvalidArgument $e) {
            throw new InvalidSchedulerException();
        }

        return $rule;
    }
}
