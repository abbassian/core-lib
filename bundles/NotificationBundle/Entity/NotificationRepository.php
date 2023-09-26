<?php

namespace Autoborna\NotificationBundle\Entity;

use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Autoborna\CoreBundle\Entity\CommonRepository;

/**
 * Class NotificationRepository.
 */
class NotificationRepository extends CommonRepository
{
    /**
     * Get a list of entities.
     *
     * @return Paginator
     */
    public function getEntities(array $args = [])
    {
        $q = $this->_em
            ->createQueryBuilder()
            ->select('e')
            ->from('AutobornaNotificationBundle:Notification', 'e', 'e.id');
        if (empty($args['iterator_mode'])) {
            $q->leftJoin('e.category', 'c');
        }

        $args['qb'] = $q;

        return parent::getEntities($args);
    }

    /**
     * Get amounts of sent and read notifications.
     *
     * @return array
     */
    public function getSentReadCount()
    {
        $q = $this->_em->createQueryBuilder();
        $q->select('SUM(e.sentCount) as sent_count, SUM(e.readCount) as read_count')
            ->from('AutobornaNotificationBundle:Notification', 'e');
        $results = $q->getQuery()->getSingleResult(Query::HYDRATE_ARRAY);

        if (!isset($results['sent_count'])) {
            $results['sent_count'] = 0;
        }
        if (!isset($results['read_count'])) {
            $results['read_count'] = 0;
        }

        return $results;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder|\Doctrine\DBAL\Query\QueryBuilder $q
     * @param                                                              $filter
     *
     * @return array
     */
    protected function addSearchCommandWhereClause($q, $filter)
    {
        list($expr, $parameters) = $this->addStandardSearchCommandWhereClause($q, $filter);
        if ($expr) {
            return [$expr, $parameters];
        }

        $command         = $filter->command;
        $unique          = $this->generateRandomParameterName();
        $returnParameter = false; //returning a parameter that is not used will lead to a Doctrine error

        switch ($command) {
            case $this->translator->trans('autoborna.core.searchcommand.lang'):
            case $this->translator->trans('autoborna.core.searchcommand.lang', [], null, 'en_US'):
                $langUnique      = $this->generateRandomParameterName();
                $langValue       = $filter->string.'_%';
                $forceParameters = [
                    $langUnique => $langValue,
                    $unique     => $filter->string,
                ];
                $expr = $q->expr()->orX(
                    $q->expr()->eq('e.language', ":$unique"),
                    $q->expr()->like('e.language', ":$langUnique")
                );
                $returnParameter = true;
                break;
        }

        if ($expr && $filter->not) {
            $expr = $q->expr()->not($expr);
        }

        if (!empty($forceParameters)) {
            $parameters = $forceParameters;
        } elseif ($returnParameter) {
            $string     = ($filter->strict) ? $filter->string : "%{$filter->string}%";
            $parameters = ["$unique" => $string];
        }

        return [$expr, $parameters];
    }

    /**
     * @return array
     */
    public function getSearchCommands()
    {
        $commands = [
            'autoborna.core.searchcommand.ispublished',
            'autoborna.core.searchcommand.isunpublished',
            'autoborna.core.searchcommand.isuncategorized',
            'autoborna.core.searchcommand.ismine',
            'autoborna.core.searchcommand.category',
            'autoborna.core.searchcommand.lang',
        ];

        return array_merge($commands, parent::getSearchCommands());
    }

    /**
     * @return string
     */
    protected function getDefaultOrder()
    {
        return [
            ['e.name', 'ASC'],
        ];
    }

    /**
     * @return string
     */
    public function getTableAlias()
    {
        return 'e';
    }

    /**
     * Up the click/sent counts.
     *
     * @param        $id
     * @param string $type
     * @param int    $increaseBy
     */
    public function upCount($id, $type = 'sent', $increaseBy = 1)
    {
        try {
            $q = $this->_em->getConnection()->createQueryBuilder();

            $q->update(MAUTIC_TABLE_PREFIX.'push_notifications')
                ->set($type.'_count', $type.'_count + '.(int) $increaseBy)
                ->where('id = '.(int) $id);

            $q->execute();
        } catch (\Exception $exception) {
            // not important
        }
    }

    /**
     * @param string $search
     * @param int    $limit
     * @param int    $start
     * @param bool   $viewOther
     * @param string $notificationType
     *
     * @return array
     */
    public function getNotificationList($search = '', $limit = 10, $start = 0, $viewOther = false, $notificationType = null)
    {
        $q = $this->createQueryBuilder('e');
        $q->select('partial e.{id, name, language}');

        if (!empty($search)) {
            if (is_array($search)) {
                $search = array_map('intval', $search);
                $q->andWhere($q->expr()->in('e.id', ':search'))
                    ->setParameter('search', $search);
            } else {
                $q->andWhere($q->expr()->like('e.name', ':search'))
                    ->setParameter('search', "%{$search}%");
            }
        }

        if (!$viewOther) {
            $q->andWhere($q->expr()->eq('e.createdBy', ':id'))
                ->setParameter('id', $this->currentUser->getId());
        }

        if (!empty($notificationType)) {
            $q->andWhere(
                $q->expr()->eq('e.notificationType', $q->expr()->literal($notificationType))
            );
        }

        $q->andWhere('e.mobile != 1');

        $q->orderBy('e.name');

        if (!empty($limit)) {
            $q->setFirstResult($start)
                ->setMaxResults($limit);
        }

        return $q->getQuery()->getArrayResult();
    }

    /**
     * @param string $search
     * @param int    $limit
     * @param int    $start
     * @param bool   $viewOther
     * @param string $notificationType
     *
     * @return array
     */
    public function getMobileNotificationList($search = '', $limit = 10, $start = 0, $viewOther = false, $notificationType = null)
    {
        $q = $this->createQueryBuilder('e');
        $q->select('partial e.{id, name, language}');

        if (!empty($search)) {
            if (is_array($search)) {
                $search = array_map('intval', $search);
                $q->andWhere($q->expr()->in('e.id', ':search'))
                    ->setParameter('search', $search);
            } else {
                $q->andWhere($q->expr()->like('e.name', ':search'))
                    ->setParameter('search', "%{$search}%");
            }
        }

        if (!$viewOther) {
            $q->andWhere($q->expr()->eq('e.createdBy', ':id'))
                ->setParameter('id', $this->currentUser->getId());
        }

        if (!empty($notificationType)) {
            $q->andWhere(
                $q->expr()->eq('e.notificationType', $q->expr()->literal($notificationType))
            );
        }

        $q->andWhere('e.mobile = 1');

        $q->orderBy('e.name');

        if (!empty($limit)) {
            $q->setFirstResult($start)
                ->setMaxResults($limit);
        }

        return $q->getQuery()->getArrayResult();
    }
}
