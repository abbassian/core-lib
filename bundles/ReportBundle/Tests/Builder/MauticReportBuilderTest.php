<?php

declare(strict_types=1);

namespace Autoborna\ReportBundle\Tests\Builder;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Doctrine\DBAL\Query\QueryBuilder;
use Autoborna\ChannelBundle\Helper\ChannelListHelper;
use Autoborna\ReportBundle\Builder\AutobornaReportBuilder;
use Autoborna\ReportBundle\Entity\Report;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class AutobornaReportBuilderTest extends TestCase
{
    /**
     * @var MockObject|EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var MockObject|Connection
     */
    private $connection;

    /**
     * @var MockObject|ChannelListHelper
     */
    private $channelListHelper;

    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dispatcher        = $this->createMock(EventDispatcherInterface::class);
        $this->connection        = $this->createMock(Connection::class);
        $this->channelListHelper = $this->createMock(ChannelListHelper::class);
        $this->queryBuilder      = new QueryBuilder($this->connection);

        $this->connection->method('createQueryBuilder')->willReturn($this->queryBuilder);
        $this->connection->method('getExpressionBuilder')->willReturn(new ExpressionBuilder($this->connection));
        $this->connection->method('quote')->willReturnMap([['', null, "''"]]);
    }

    public function testColumnSanitization(): void
    {
        $this->connection->method('createQueryBuilder')->willReturn($this->queryBuilder);

        $report = new Report();
        $report->setColumns(['a.b', 'b.c']);
        $builder = $this->buildBuilder($report);
        $query   = $builder->getQuery([
            'columns' => ['a.b' => [], 'b.c' => []],
        ]);
        Assert::assertSame('SELECT `a`.`b`, `b`.`c`', $query->getSql());
    }

    public function testFiltersWithEmptyAndNotEmptyDateTypes(): void
    {
        $report = new Report();
        $report->setColumns(['a.someField']);
        $report->setFilters([
            [
                'column'    => 'a.emptyDate',
                'glue'      => 'and',
                'value'     => '',
                'condition' => 'empty',
            ],
            [
                'column'    => 'a.notEmptyDate',
                'glue'      => 'and',
                'value'     => '',
                'condition' => 'notEmpty',
            ],
            [
                'column'    => 'a.emptyDateTime',
                'glue'      => 'and',
                'value'     => '',
                'condition' => 'empty',
            ],
            [
                'column'    => 'a.notEmptyDateTime',
                'glue'      => 'and',
                'value'     => '',
                'condition' => 'notEmpty',
            ],
            [
                'column'    => 'a.emptyString',
                'glue'      => 'and',
                'value'     => '',
                'condition' => 'empty',
            ],
            [
                'column'    => 'a.notEmptyString',
                'glue'      => 'and',
                'value'     => '',
                'condition' => 'notEmpty',
            ],
        ]);
        $builder = $this->buildBuilder($report);
        $query   = $builder->getQuery([
            'columns' => ['a.someField' => []],
            'filters' => [
                'a.emptyDate' => [
                    'label' => 'Empty date',
                    'type'  => 'date',
                    'alias' => 'emptyDate',
                ],
                'a.notEmptyDate' => [
                    'label' => 'Not empty date',
                    'type'  => 'date',
                    'alias' => 'notEmptyDate',
                ],
                'a.emptyDateTime' => [
                    'label' => 'Empty date time',
                    'type'  => 'datetime',
                    'alias' => 'emptyDateTime',
                ],
                'a.notEmptyDateTime' => [
                    'label' => 'Not empty date time',
                    'type'  => 'datetime',
                    'alias' => 'notEmptyDateTime',
                ],
                'a.emptyString' => [
                    'label' => 'Empty string',
                    'type'  => 'string',
                    'alias' => 'emptyString',
                ],
                'a.notEmptyString' => [
                    'label' => 'Not empty string',
                    'type'  => 'string',
                    'alias' => 'notEmptyString',
                ],
            ],
        ]);
        Assert::assertSame(trim(preg_replace('/\s{2,}/', ' ', "
            SELECT
                `a`.`someField`
            WHERE
                (a.emptyDate IS NULL)
                AND (a.notEmptyDate IS NOT NULL)
                AND (a.emptyDateTime IS NULL)
                AND (a.notEmptyDateTime IS NOT NULL)
                AND ((a.emptyString IS NULL) OR (a.emptyString = ''))
                AND (a.notEmptyString IS NOT NULL) AND (a.notEmptyString <> '')
        ")), $query->getSql());
    }

    public function testFiltersWithEmptyAndNotEmptyDateTypes2(): void
    {
        $report = new Report();
        $report->setColumns(['a.someField']);
        $report->setFilters([
            [
                'column'    => 'a.notEqualString',
                'glue'      => 'and',
                'value'     => '',
                'condition' => 'neq',
            ],
        ]);
        $builder = $this->buildBuilder($report);
        $query   = $builder->getQuery([
            'columns' => ['a.someField' => []],
            'filters' => [
                'a.notEqualString' => [
                    'label' => 'Not equal string',
                    'type'  => 'string',
                    'alias' => 'notEqualString',
                ],
            ],
        ]);
        Assert::assertSame(trim(preg_replace('/\s{2,}/', ' ', '
            SELECT `a`.`someField` WHERE (a.notEqualString IS NULL) OR (a.notEqualString <> :i0canotEqualString)
        ')), $query->getSql());
    }

    private function buildBuilder(Report $report): AutobornaReportBuilder
    {
        return new AutobornaReportBuilder(
            $this->dispatcher,
            $this->connection,
            $report,
            $this->channelListHelper
        );
    }
}
