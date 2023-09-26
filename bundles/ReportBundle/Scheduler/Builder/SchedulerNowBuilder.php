<?php

declare(strict_types=1);

namespace Autoborna\ReportBundle\Scheduler\Builder;

use Autoborna\ReportBundle\Scheduler\BuilderInterface;
use Autoborna\ReportBundle\Scheduler\Exception\InvalidSchedulerException;
use Autoborna\ReportBundle\Scheduler\SchedulerInterface;
use Recurr\Exception\InvalidArgument;
use Recurr\Rule;

class SchedulerNowBuilder implements BuilderInterface
{
    /**
     * @throws InvalidSchedulerException
     */
    public function build(Rule $rule, SchedulerInterface $scheduler): Rule
    {
        try {
            $rule->setFreq('SECONDLY');
        } catch (InvalidArgument $e) {
            throw new InvalidSchedulerException();
        }

        return $rule;
    }
}
