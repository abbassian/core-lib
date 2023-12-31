<?php

namespace Autoborna\ReportBundle\Event;

use Autoborna\ReportBundle\Entity\Report;
use Symfony\Component\EventDispatcher\Event;

class AbstractReportEvent extends Event
{
    /**
     * @var string
     */
    protected $context = '';

    /**
     * Report entity.
     *
     * @var Report
     */
    protected $report;

    /**
     * @return Report
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * Get the context.
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param $context
     *
     * @return bool
     */
    public function checkContext($context)
    {
        if (empty($this->context)) {
            return true;
        }

        if (is_array($context)) {
            return in_array($this->context, $context);
        } elseif ($this->context == $context) {
            return true;
        } else {
            return false;
        }
    }
}
