<?php

namespace Autoborna\EmailBundle\Helper;

use Autoborna\EmailBundle\Stats\FetchOptions\EmailStatOptions;
use Autoborna\EmailBundle\Stats\Helper\BouncedHelper;
use Autoborna\EmailBundle\Stats\Helper\ClickedHelper;
use Autoborna\EmailBundle\Stats\Helper\FailedHelper;
use Autoborna\EmailBundle\Stats\Helper\FilterTrait;
use Autoborna\EmailBundle\Stats\Helper\OpenedHelper;
use Autoborna\EmailBundle\Stats\Helper\SentHelper;
use Autoborna\EmailBundle\Stats\Helper\UnsubscribedHelper;
use Autoborna\EmailBundle\Stats\StatHelperContainer;
use Autoborna\StatsBundle\Aggregate\Collection\StatCollection;

class StatsCollectionHelper
{
    use FilterTrait;

    const GENERAL_STAT_PREFIX = 'email';

    /**
     * @var StatHelperContainer
     */
    private $helperContainer;

    /**
     * StatsCollectionHelper constructor.
     */
    public function __construct(StatHelperContainer $helperContainer)
    {
        $this->helperContainer = $helperContainer;
    }

    /**
     * Fetch stats from listeners.
     *
     * @return mixed
     *
     * @throws \Autoborna\EmailBundle\Stats\Exception\InvalidStatHelperException
     */
    public function fetchSentStats(\DateTime $fromDateTime, \DateTime $toDateTime, EmailStatOptions $options)
    {
        return $this->helperContainer->getHelper(SentHelper::NAME)->fetchStats($fromDateTime, $toDateTime, $options);
    }

    /**
     * Fetch stats from listeners.
     *
     * @return mixed
     *
     * @throws \Autoborna\EmailBundle\Stats\Exception\InvalidStatHelperException
     */
    public function fetchOpenedStats(\DateTime $fromDateTime, \DateTime $toDateTime, EmailStatOptions $options)
    {
        return $this->helperContainer->getHelper(OpenedHelper::NAME)->fetchStats($fromDateTime, $toDateTime, $options);
    }

    /**
     * Fetch stats from listeners.
     *
     * @return mixed
     *
     * @throws \Autoborna\EmailBundle\Stats\Exception\InvalidStatHelperException
     */
    public function fetchFailedStats(\DateTime $fromDateTime, \DateTime $toDateTime, EmailStatOptions $options)
    {
        return $this->helperContainer->getHelper(FailedHelper::NAME)->fetchStats($fromDateTime, $toDateTime, $options);
    }

    /**
     * Fetch stats from listeners.
     *
     * @return mixed
     *
     * @throws \Autoborna\EmailBundle\Stats\Exception\InvalidStatHelperException
     */
    public function fetchClickedStats(\DateTime $fromDateTime, \DateTime $toDateTime, EmailStatOptions $options)
    {
        return $this->helperContainer->getHelper(ClickedHelper::NAME)->fetchStats($fromDateTime, $toDateTime, $options);
    }

    /**
     * Fetch stats from listeners.
     *
     * @return mixed
     *
     * @throws \Autoborna\EmailBundle\Stats\Exception\InvalidStatHelperException
     */
    public function fetchBouncedStats(\DateTime $fromDateTime, \DateTime $toDateTime, EmailStatOptions $options)
    {
        return $this->helperContainer->getHelper(BouncedHelper::NAME)->fetchStats($fromDateTime, $toDateTime, $options);
    }

    /**
     * Fetch stats from listeners.
     *
     * @return mixed
     *
     * @throws \Autoborna\EmailBundle\Stats\Exception\InvalidStatHelperException
     */
    public function fetchUnsubscribedStats(\DateTime $fromDateTime, \DateTime $toDateTime, EmailStatOptions $options)
    {
        return $this->helperContainer->getHelper(UnsubscribedHelper::NAME)->fetchStats($fromDateTime, $toDateTime, $options);
    }

    /**
     * Generate stats from Autoborna's raw data.
     *
     * @param $statName
     *
     * @throws \Autoborna\EmailBundle\Stats\Exception\InvalidStatHelperException
     */
    public function generateStats(
        $statName,
        \DateTime $fromDateTime,
        \DateTime $toDateTime,
        EmailStatOptions $options,
        StatCollection $statCollection
    ) {
        $this->helperContainer->getHelper($statName)->generateStats($fromDateTime, $toDateTime, $options, $statCollection);
    }
}
