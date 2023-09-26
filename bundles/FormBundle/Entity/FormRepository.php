<?php

namespace Autoborna\FormBundle\Entity;

use Doctrine\ORM\Query\Expr\Join;
use Autoborna\CoreBundle\Entity\CommonRepository;

/**
 * FormRepository.
 */
class FormRepository extends CommonRepository
{
    /**
     * {@inheritdoc}
     */
    public function getEntities(array $args = [])
    {
        //use a subquery to get a count of submissions otherwise doctrine will not pull all of the results
        $sq = $this->_em->createQueryBuilder()
            ->select('count(fs.id)')
            ->from('AutobornaFormBundle:Submission', 'fs')
            ->where('fs.form = f');

        $q = $this->createQueryBuilder('f');
        $q->select('f, ('.$sq->getDql().') as submission_count');
        $q->leftJoin('f.category', 'c');

        $args['qb'] = $q;

        return parent::getEntities($args);
    }

    /**
     * @param string $search
     * @param int    $limit
     * @param int    $start
     * @param bool   $viewOther
     * @param null   $formType
     *
     * @return array
     */
    public function getFormList($search = '', $limit = 10, $start = 0, $viewOther = false, $formType = null)
    {
        $q = $this->createQueryBuilder('f');
        $q->select('partial f.{id, name, alias}');

        if (!empty($search)) {
            $q->andWhere($q->expr()->like('f.name', ':search'))
                ->setParameter('search', "{$search}%");
        }

        if (!$viewOther) {
            $q->andWhere($q->expr()->eq('f.createdBy', ':id'))
                ->setParameter('id', $this->currentUser->getId());
        }

        if (!empty($formType)) {
            $q->andWhere(
                $q->expr()->eq('f.formType', ':type')
            )->setParameter('type', $formType);
        }

        $q->orderBy('f.name');

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
        return $this->addStandardCatchAllWhereClause($q, $filter, [
            'f.name',
            'f.description',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function addSearchCommandWhereClause($q, $filter)
    {
        list($expr, $standardSearchParameters) = $this->addStandardSearchCommandWhereClause($q, $filter);
        if ($expr) {
            return [$expr, $standardSearchParameters];
        }

        $command         = $filter->command;
        $unique          = $this->generateRandomParameterName();
        $parameters      = [];
        $returnParameter = false; //returning a parameter that is not used will lead to a Doctrine error

        switch ($command) {
            case $this->translator->trans('autoborna.form.form.searchcommand.isexpired'):
            case $this->translator->trans('autoborna.form.form.searchcommand.isexpired', [], null, 'en_US'):
                $expr = $q->expr()->andX(
                    $q->expr()->eq('f.isPublished', ":$unique"),
                    $q->expr()->isNotNull('f.publishDown'),
                    $q->expr()->neq('f.publishDown', $q->expr()->literal('')),
                    $q->expr()->lt('f.publishDown', 'CURRENT_TIMESTAMP()')
                );
                $forceParameters = [$unique => true];
                break;
            case $this->translator->trans('autoborna.form.form.searchcommand.ispending'):
            case $this->translator->trans('autoborna.form.form.searchcommand.ispending', [], null, 'en_US'):
                $expr = $q->expr()->andX(
                    $q->expr()->eq('f.isPublished', ":$unique"),
                    $q->expr()->isNotNull('f.publishUp'),
                    $q->expr()->neq('f.publishUp', $q->expr()->literal('')),
                    $q->expr()->gt('f.publishUp', 'CURRENT_TIMESTAMP()')
                );
                $forceParameters = [$unique => true];
                break;
            case $this->translator->trans('autoborna.form.form.searchcommand.hasresults'):
            case $this->translator->trans('autoborna.form.form.searchcommand.hasresults', [], null, 'en_US'):
                $sq       = $this->getEntityManager()->createQueryBuilder();
                $subquery = $sq->select('count(s.id)')
                    ->from('AutobornaFormBundle:Submission', 's')
                    ->leftJoin('AutobornaFormBundle:Form', 'f2',
                        Join::WITH,
                        $sq->expr()->eq('s.form', 'f2')
                    )
                    ->where(
                        $q->expr()->eq('s.form', 'f')
                    )
                    ->getDql();
                $expr = $q->expr()->gt(sprintf('(%s)', $subquery), 1);
                break;
            case $this->translator->trans('autoborna.core.searchcommand.name'):
            case $this->translator->trans('autoborna.core.searchcommand.name', [], null, 'en_US'):
                $expr            = $q->expr()->like('f.name', ':'.$unique);
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

        return [
            $expr,
            $parameters,
        ];
    }

    /**
     * Fetch the form results.
     *
     * @return array
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getFormResults(Form $form, array $options = [])
    {
        $query = $this->_em->getConnection()->createQueryBuilder();

        $query->from(MAUTIC_TABLE_PREFIX.'form_submissions', 'fs')
            ->select('fr.*')
            ->leftJoin('fs', $this->getResultsTableName($form->getId(), $form->getAlias()), 'fr', 'fr.submission_id = fs.id')
            ->where('fs.form_id = :formId')
            ->setParameter('formId', $form->getId());

        if (!empty($options['leadId'])) {
            $query->andWhere('fs.lead_id = '.(int) $options['leadId']);
        }

        if (!empty($options['formId'])) {
            $query->andWhere($query->expr()->eq('fs.form_id', ':id'))
            ->setParameter('id', $options['formId']);
        }

        if (!empty($options['limit'])) {
            $query->setMaxResults((int) $options['limit']);
        }

        return $query->execute()->fetchAll();
    }

    /**
     * Compile and return the form result table name.
     *
     * @param int    $formId
     * @param string $formAlias
     *
     * @return string
     */
    public function getResultsTableName($formId, $formAlias)
    {
        return MAUTIC_TABLE_PREFIX.'form_results_'.$formId.'_'.$formAlias;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchCommands()
    {
        $commands = [
            'autoborna.core.searchcommand.ispublished',
            'autoborna.core.searchcommand.isunpublished',
            'autoborna.core.searchcommand.isuncategorized',
            'autoborna.core.searchcommand.ismine',
            'autoborna.form.form.searchcommand.isexpired',
            'autoborna.form.form.searchcommand.ispending',
            'autoborna.form.form.searchcommand.hasresults',
            'autoborna.core.searchcommand.category',
            'autoborna.core.searchcommand.name',
        ];

        return array_merge($commands, parent::getSearchCommands());
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultOrder()
    {
        return [
            ['f.name', 'ASC'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTableAlias()
    {
        return 'f';
    }
}