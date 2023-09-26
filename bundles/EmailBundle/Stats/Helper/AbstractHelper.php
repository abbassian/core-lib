<?php

namespace Autoborna\EmailBundle\Stats\Helper;

use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Exception;
use Autoborna\CoreBundle\Doctrine\Provider\GeneratedColumnsProviderInterface;
use Autoborna\CoreBundle\Helper\Chart\ChartQuery;
use Autoborna\CoreBundle\Helper\Chart\DateRangeUnitTrait;
use Autoborna\CoreBundle\Helper\UserHelper;
use Autoborna\EmailBundle\Stats\FetchOptions\EmailStatOptions;
use Autoborna\StatsBundle\Aggregate\Collection\StatCollection;
use Autoborna\StatsBundle\Aggregate\Collector;

abstract class AbstractHelper implements StatHelperInterface
{
    use FilterTrait;
    use DateRangeUnitTrait;

    /**
     * @var Collector
     */
    private $collector;

    /**
     * @var UserHelper
     */
    private $userHelper;

    /**
     * @var GeneratedColumnsProviderInterface
     */
    protected $generatedColumnsProvider;

    public function __construct(
        Collector $collector,
        Connection $connection,
        GeneratedColumnsProviderInterface $generatedColumnsProvider,
        UserHelper $userHelper
    ) {
        $this->collector                = $collector;
        $this->connection               = $connection;
        $this->generatedColumnsProvider = $generatedColumnsProvider;
        $this->userHelper               = $userHelper;
    }

    /**
     * @return array
     *
     * @throws Exception
     */
    public function fetchStats(DateTime $fromDateTime, DateTime $toDateTime, EmailStatOptions $options)
    {
        $statCollection = $this->collector->fetchStats($this->getName(), $fromDateTime, $toDateTime, $options);
        $calculator     = $statCollection->getCalculator($fromDateTime, $toDateTime);

        // Format into what is required for the graphs
        switch ($this->getTimeUnitFromDateRange($fromDateTime, $toDateTime)) {
            case 'Y': // year
                $stats = $calculator->getSumsByYear();
                break;
            case 'm': // month
                $stats = $calculator->getSumsByMonth();
                break;
            case 'W':
                $stats = $calculator->getSumsByWeek();
                break;
            case 'd': // day
                $stats = $calculator->getSumsByDay();
                break;
            case 'H': // hour
            default:
                $stats = $calculator->getCountsByHour();
                break;
        }

        // Chart.js only care about the values
        return array_values($stats->getStats());
    }

    /**
     * @return ChartQuery
     */
    protected function getQuery(DateTime $fromDateTime, DateTime $toDateTime)
    {
        $unit = $this->getTimeUnitFromDateRange($fromDateTime, $toDateTime);

        if ('W' == $unit) {   // We won't support week storage, we will store it by date
            $unit = 'd';
        }
        $query = new ChartQuery($this->connection, $fromDateTime, $toDateTime, $unit);

        $query->setGeneratedColumnProvider($this->generatedColumnsProvider);

        return $query;
    }

    /**
     * Joins the email table and limits created_by to currently logged in user.
     *
     * @param string $emailIdColumn
     */
    protected function limitQueryToCreator(QueryBuilder $q, $emailIdColumn = 't.email_id')
    {
        $q->join('t', MAUTIC_TABLE_PREFIX.'emails', 'e', 'e.id = '.$emailIdColumn)
            ->andWhere('e.created_by = :userId')
            ->setParameter('userId', $this->userHelper->getUser()->getId());
    }

    /**
     * @param string $column
     * @param string $prefix
     */
    protected function limitQueryToEmailIds(QueryBuilder $q, array $ids, $column, $prefix)
    {
        if (0 === count($ids)) {
            return;
        }

        if (1 === count($ids)) {
            $q->andWhere("$prefix.$column = :email_id");
            $q->setParameter('email_id', $ids[0]);

            return;
        }

        $q->andWhere("$prefix.$column IN (:email_ids)");
        $q->setParameter('email_ids', $ids, Connection::PARAM_INT_ARRAY);
    }

    /**
     * @throws Exception
     */
    protected function fetchAndBindToCollection(QueryBuilder $q, StatCollection $statCollection)
    {
        $results = $q->execute()->fetchAll();
        foreach ($results as $result) {
            $statCollection->addStatByDateTimeStringInUTC($result['date'], $result['count']);
        }
    }
}
