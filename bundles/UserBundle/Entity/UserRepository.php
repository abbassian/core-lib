<?php

namespace Autoborna\UserBundle\Entity;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Autoborna\CoreBundle\Entity\CommonRepository;
use Autoborna\CoreBundle\Helper\DateTimeHelper;

/**
 * UserRepository.
 */
class UserRepository extends CommonRepository
{
    /**
     * Find user by username or email.
     *
     * @param $identifier
     *
     * @return array|null
     */
    public function findByIdentifier($identifier)
    {
        $q = $this->createQueryBuilder('u')
            ->where('u.username = :identifier OR u.email = :identifier')
            ->setParameter('identifier', $identifier);

        $result = $q->getQuery()->getResult();

        return (null != $result) ? $result[0] : null;
    }

    /**
     * @param $user
     */
    public function setLastLogin($user)
    {
        $now      = new DateTimeHelper();
        $datetime = $now->toUtcString();
        $conn     = $this->_em->getConnection();
        $conn->update(MAUTIC_TABLE_PREFIX.'users', [
            'last_login'  => $datetime,
            'last_active' => $datetime,
        ], ['id' => (int) $user->getId()]);
    }

    /**
     * @param $user
     */
    public function setLastActive($user)
    {
        $now  = new DateTimeHelper();
        $conn = $this->_em->getConnection();
        $conn->update(MAUTIC_TABLE_PREFIX.'users', ['last_active' => $now->toUtcString()], ['id' => (int) $user->getId()]);
    }

    /**
     * Checks to ensure that a username and/or email is unique.
     *
     * @param $params
     *
     * @return array
     */
    public function checkUniqueUsernameEmail($params)
    {
        $q = $this->createQueryBuilder('u');

        if (isset($params['email'])) {
            $q->where('u.username = :email OR u.email = :email')
                ->setParameter('email', $params['email']);
        }

        if (isset($params['username'])) {
            $q->orWhere('u.username = :username OR u.email = :username')
                ->setParameter('username', $params['username']);
        }

        return $q->getQuery()->getResult();
    }

    /**
     * Get a list of users.
     *
     * @return Paginator
     */
    public function getEntities(array $args = [])
    {
        $q = $this
            ->createQueryBuilder('u')
            ->select('u, r')
            ->leftJoin('u.role', 'r');

        $args['qb'] = $q;

        return parent::getEntities($args);
    }

    /**
     * Get a list of users for an autocomplete input.
     *
     * @param string $search
     * @param int    $limit
     * @param int    $start
     * @param array  $permissionLimiter
     *
     * @return array
     */
    public function getUserList($search = '', $limit = 10, $start = 0, $permissionLimiter = [])
    {
        $q = $this->_em->createQueryBuilder();

        $q->select('partial u.{id, firstName, lastName}')
            ->from('AutobornaUserBundle:User', 'u')
            ->leftJoin('u.role', 'r')
            ->leftJoin('r.permissions', 'p');

        if (!empty($search)) {
            $q->where(
                $q->expr()->orX(
                    $q->expr()->like('u.firstName', ':search'),
                    $q->expr()->like('u.lastName', ':search'),
                    $q->expr()->like(
                        $q->expr()->concat('u.firstName',
                            $q->expr()->concat(
                                $q->expr()->literal(' '),
                                'u.lastName'
                            )
                        ),
                        ':search'
                    )
                )
            )
            ->setParameter('search', "{$search}%");
        }

        if (!empty($permissionLimiter)) {
            //only get users with a role that has some sort of access to set permissions
            $expr = $q->expr()->andX();
            foreach ($permissionLimiter as $bundle => $level) {
                $expr->add(
                    $q->expr()->andX(
                        $q->expr()->eq('p.bundle', $q->expr()->literal($bundle)),
                        $q->expr()->eq('p.name', $q->expr()->literal($level))
                    )
                );
            }
            $expr = $q->expr()->orX(
                $q->expr()->eq('r.isAdmin', ':true'),
                $expr
            );
            $q->andWhere($expr);
        }

        $q->andWhere('u.isPublished = :true')
            ->setParameter('true', true, 'boolean')
            ->orderBy('u.firstName, u.lastName');

        if (!empty($limit)) {
            $q->setFirstResult($start)
                ->setMaxResults($limit);
        }

        return $q->getQuery()->getArrayResult();
    }

    /**
     * Return list of Users for formType Choice.
     *
     * @return array
     */
    public function getOwnerListChoices()
    {
        $q = $this->createQueryBuilder('u');

        $q->select('partial u.{id, firstName, lastName}');

        $q->andWhere('u.isPublished = true')
            ->orderBy('u.firstName, u.lastName');

        $users = $q->getQuery()->getResult();

        $result = [];
        /** @var User $user */
        foreach ($users as $user) {
            $result[$user->getName(true)] = $user->getId();
        }

        return $result;
    }

    /**
     * @param string $search
     * @param int    $limit
     * @param int    $start
     *
     * @return array
     */
    public function getPositionList($search = '', $limit = 10, $start = 0)
    {
        $q = $this->_em->createQueryBuilder()
            ->select('u.position')
            ->distinct()
            ->from('AutobornaUserBundle:User', 'u')
            ->where("u.position != ''")
            ->andWhere('u.position IS NOT NULL');
        if (!empty($search)) {
            $q->andWhere('u.position LIKE :search')
                ->setParameter('search', "{$search}%");
        }

        $q->orderBy('u.position');

        if (!empty($limit)) {
            $q->setFirstResult($start)
                ->setMaxResults($limit);
        }

        return $q->getQuery()->getArrayResult();
    }

    /**
     * {@inheritdoc}
     */
    protected function addCatchAllWhereClause($q, $filter)
    {
        return $this->addStandardCatchAllWhereClause(
            $q,
            $filter,
            [
                'u.username',
                'u.email',
                'u.firstName',
                'u.lastName',
                'u.position',
                'r.name',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function addSearchCommandWhereClause($q, $filter)
    {
        $command                 = $filter->command;
        $unique                  = $this->generateRandomParameterName();
        $returnParameter         = false; //returning a parameter that is not used will lead to a Doctrine error
        list($expr, $parameters) = parent::addSearchCommandWhereClause($q, $filter);

        switch ($command) {
            case $this->translator->trans('autoborna.core.searchcommand.ispublished'):
            case $this->translator->trans('autoborna.core.searchcommand.ispublished', [], null, 'en_US'):
                $expr            = $q->expr()->eq('u.isPublished', ":$unique");
                $forceParameters = [$unique => true];

                break;
            case $this->translator->trans('autoborna.core.searchcommand.isunpublished'):
                case $this->translator->trans('autoborna.core.searchcommand.isunpublished', [], null, 'en_US'):
                $expr            = $q->expr()->eq('u.isPublished', ":$unique");
                $forceParameters = [$unique => false];

                break;
            case $this->translator->trans('autoborna.user.user.searchcommand.isadmin'):
                case $this->translator->trans('autoborna.user.user.searchcommand.isadmin', [], null, 'en_US'):
                $expr            = $q->expr()->eq('r.isAdmin', ":$unique");
                $forceParameters = [$unique => true];
                break;
            case $this->translator->trans('autoborna.core.searchcommand.email'):
                case $this->translator->trans('autoborna.core.searchcommand.email', [], null, 'en_US'):
                $expr            = $q->expr()->like('u.email', ':'.$unique);
                $returnParameter = true;
                break;
            case $this->translator->trans('autoborna.user.user.searchcommand.position'):
                case $this->translator->trans('autoborna.user.user.searchcommand.position', [], null, 'en_US'):
                $expr            = $q->expr()->like('u.position', ':'.$unique);
                $returnParameter = true;
                break;
            case $this->translator->trans('autoborna.user.user.searchcommand.username'):
                case $this->translator->trans('autoborna.user.user.searchcommand.username', [], null, 'en_US'):
                $expr            = $q->expr()->like('u.username', ':'.$unique);
                $returnParameter = true;
                break;
            case $this->translator->trans('autoborna.user.user.searchcommand.role'):
                case $this->translator->trans('autoborna.user.user.searchcommand.role', [], null, 'en_US'):
                $expr            = $q->expr()->like('r.name', ':'.$unique);
                $returnParameter = true;
                break;
            case $this->translator->trans('autoborna.core.searchcommand.name'):
                case $this->translator->trans('autoborna.core.searchcommand.name', [], null, 'en_US'):
                $expr = $q->expr()->orX(
                    $q->expr()->like('u.firstName', ':'.$unique),
                    $q->expr()->like('u.lastName', ':'.$unique)
                );
                $returnParameter = true;
                break;
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
     * {@inheritdoc}
     */
    public function getSearchCommands()
    {
        $commands = [
            'autoborna.core.searchcommand.email',
            'autoborna.core.searchcommand.ispublished',
            'autoborna.core.searchcommand.isunpublished',
            'autoborna.user.user.searchcommand.isadmin',
            'autoborna.core.searchcommand.name',
            'autoborna.user.user.searchcommand.position',
            'autoborna.user.user.searchcommand.role',
            'autoborna.user.user.searchcommand.username',
        ];

        return array_merge($commands, parent::getSearchCommands());
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultOrder()
    {
        return [
            ['u.lastName', 'ASC'],
            ['u.firstName', 'ASC'],
            ['u.username', 'ASC'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTableAlias()
    {
        return 'u';
    }
}
