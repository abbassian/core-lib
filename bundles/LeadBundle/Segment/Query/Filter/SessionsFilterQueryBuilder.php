<?php

namespace Autoborna\LeadBundle\Segment\Query\Filter;

use Autoborna\LeadBundle\Segment\ContactSegmentFilter;
use Autoborna\LeadBundle\Segment\Query\QueryBuilder;

class SessionsFilterQueryBuilder extends BaseFilterQueryBuilder
{
    public static function getServiceId()
    {
        return 'autoborna.lead.query.builder.special.sessions';
    }

    public function applyQuery(QueryBuilder $queryBuilder, ContactSegmentFilter $filter)
    {
        $leadsTableAlias      = $queryBuilder->getTableAlias(MAUTIC_TABLE_PREFIX.'leads');
        $pageHitsAlias        = $this->generateRandomParameterName();
        $exclusionAlias       = $this->generateRandomParameterName();
        $expressionValueAlias = $this->generateRandomParameterName();

        $expressionOperator = $filter->getOperator();
        $expression         = $queryBuilder->expr()->$expressionOperator('count(id)',
            $filter->getParameterHolder($expressionValueAlias));

        $queryBuilder->setParameter($expressionValueAlias, (int) $filter->getParameterValue());

        $exclusionQueryBuilder = $queryBuilder->getConnection()->createQueryBuilder();
        $exclusionQueryBuilder
            ->select($exclusionAlias.'.id')
            ->from(MAUTIC_TABLE_PREFIX.'page_hits', $exclusionAlias)
            ->where(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq($leadsTableAlias.'.id', $exclusionAlias.'.lead_id'),
                    $queryBuilder->expr()->gt(
                        $exclusionAlias.'.date_hit',
                        $pageHitsAlias.'.date_hit - INTERVAL 30 MINUTE'
                    ),
                    $queryBuilder->expr()->lt($exclusionAlias.'.date_hit', $pageHitsAlias.'.date_hit')
                )
            );

        $sessionQueryBuilder = $queryBuilder->getConnection()->createQueryBuilder();
        $sessionQueryBuilder
            ->select('count(id)')
            ->from(MAUTIC_TABLE_PREFIX.'page_hits', $pageHitsAlias)
            ->where(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq($leadsTableAlias.'.id', $pageHitsAlias.'.lead_id'),
                    $queryBuilder->expr()->isNull($pageHitsAlias.'.email_id'),
                    $queryBuilder->expr()->isNull($pageHitsAlias.'.redirect_id'),
                    $queryBuilder->expr()->notExists(
                        $exclusionQueryBuilder->getSQL()
                    )
                )
            )
            ->having($expression);

        $glue = $filter->getGlue().'Where';
        $queryBuilder->$glue($queryBuilder->expr()->exists($sessionQueryBuilder->getSQL()));

        return $queryBuilder;
    }
}
