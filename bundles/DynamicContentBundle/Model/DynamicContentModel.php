<?php

namespace Autoborna\DynamicContentBundle\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Autoborna\CoreBundle\Helper\Chart\ChartQuery;
use Autoborna\CoreBundle\Helper\Chart\LineChart;
use Autoborna\CoreBundle\Model\AjaxLookupModelInterface;
use Autoborna\CoreBundle\Model\FormModel;
use Autoborna\CoreBundle\Model\TranslationModelTrait;
use Autoborna\CoreBundle\Model\VariantModelTrait;
use Autoborna\DynamicContentBundle\DynamicContentEvents;
use Autoborna\DynamicContentBundle\Entity\DynamicContent;
use Autoborna\DynamicContentBundle\Entity\DynamicContentRepository;
use Autoborna\DynamicContentBundle\Entity\Stat;
use Autoborna\DynamicContentBundle\Event\DynamicContentEvent;
use Autoborna\DynamicContentBundle\Form\Type\DynamicContentType;
use Autoborna\LeadBundle\Entity\Lead;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class DynamicContentModel extends FormModel implements AjaxLookupModelInterface
{
    use VariantModelTrait;
    use TranslationModelTrait;

    /**
     * Retrieve the permissions base.
     *
     * @return string
     */
    public function getPermissionBase()
    {
        return 'dynamiccontent:dynamiccontents';
    }

    /**
     * {@inheritdoc}
     *
     * @return DynamicContentRepository
     */
    public function getRepository()
    {
        /** @var DynamicContentRepository $repo */
        $repo = $this->em->getRepository('AutobornaDynamicContentBundle:DynamicContent');

        $repo->setTranslator($this->translator);

        return $repo;
    }

    /**
     * @return \Autoborna\DynamicContentBundle\Entity\StatRepository
     */
    public function getStatRepository()
    {
        return $this->em->getRepository('AutobornaDynamicContentBundle:Stat');
    }

    /**
     * {@inheritdoc}
     *
     * @param object $entity
     * @param bool   $unlock
     */
    public function saveEntity($entity, $unlock = true)
    {
        parent::saveEntity($entity, $unlock);

        $this->postTranslationEntitySave($entity);
    }

    /**
     * Here just so PHPStorm calms down about type hinting.
     *
     * @param null $id
     *
     * @return DynamicContent|null
     */
    public function getEntity($id = null)
    {
        if (null === $id) {
            return new DynamicContent();
        }

        return parent::getEntity($id);
    }

    /**
     * {@inheritdoc}
     *
     * @param       $entity
     * @param       $formFactory
     * @param null  $action
     * @param array $options
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public function createForm($entity, $formFactory, $action = null, $options = [])
    {
        if (!$entity instanceof DynamicContent) {
            throw new \InvalidArgumentException('Entity must be of class DynamicContent');
        }

        if (!empty($action)) {
            $options['action'] = $action;
        }

        return $formFactory->create(DynamicContentType::class, $entity, $options);
    }

    /**
     * @param $slot
     */
    public function setSlotContentForLead(DynamicContent $dwc, Lead $lead, $slot)
    {
        $qb = $this->em->getConnection()->createQueryBuilder();

        $qb->insert(MAUTIC_TABLE_PREFIX.'dynamic_content_lead_data')
            ->values([
                'lead_id'            => $lead->getId(),
                'dynamic_content_id' => $dwc->getId(),
                'slot'               => ':slot',
                'date_added'         => $qb->expr()->literal((new \DateTime())->format('Y-m-d H:i:s')),
            ])->setParameter('slot', $slot);

        $qb->execute();
    }

    /**
     * @param string     $slot
     * @param Lead|array $lead
     *
     * @return DynamicContent
     */
    public function getSlotContentForLead($slot, $lead)
    {
        if (!$lead) {
            return [];
        }

        $qb = $this->em->getConnection()->createQueryBuilder();

        $id = $lead instanceof Lead ? $lead->getId() : $lead['id'];

        $qb->select('dc.id, dc.content')
            ->from(MAUTIC_TABLE_PREFIX.'dynamic_content', 'dc')
            ->leftJoin('dc', MAUTIC_TABLE_PREFIX.'dynamic_content_lead_data', 'dcld', 'dcld.dynamic_content_id = dc.id')
            ->andWhere($qb->expr()->eq('dcld.slot', ':slot'))
            ->andWhere($qb->expr()->eq('dcld.lead_id', ':lead_id'))
            ->andWhere($qb->expr()->eq('dc.is_published', 1))
            ->setParameter('slot', $slot)
            ->setParameter('lead_id', $id)
            ->orderBy('dcld.date_added', 'DESC')
            ->addOrderBy('dcld.id', 'DESC');

        return $qb->execute()->fetch();
    }

    /**
     * @param Lead|array $lead
     * @param string     $source
     */
    public function createStatEntry(DynamicContent $dynamicContent, $lead, $source = null)
    {
        if (empty($lead)) {
            return;
        }

        if ($lead instanceof Lead && !$lead->getId()) {
            return;
        }

        if (is_array($lead)) {
            if (empty($lead['id'])) {
                return;
            }

            $lead = $this->em->getReference('AutobornaLeadBundle:Lead', $lead['id']);
        }

        $stat = new Stat();
        $stat->setDateSent(new \DateTime());
        $stat->setLead($lead);
        $stat->setDynamicContent($dynamicContent);
        $stat->setSource($source);

        $this->getStatRepository()->saveEntity($stat);
    }

    /**
     * {@inheritdoc}
     *
     * @param $action
     * @param $entity
     * @param $isNew
     * @param $event
     *
     * @throws \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException
     */
    protected function dispatchEvent($action, &$entity, $isNew = false, Event $event = null)
    {
        if (!$entity instanceof DynamicContent) {
            throw new MethodNotAllowedHttpException(['Dynamic Content']);
        }

        switch ($action) {
            case 'pre_save':
                $name = DynamicContentEvents::PRE_SAVE;
                break;
            case 'post_save':
                $name = DynamicContentEvents::POST_SAVE;
                break;
            case 'pre_delete':
                $name = DynamicContentEvents::PRE_DELETE;
                break;
            case 'post_delete':
                $name = DynamicContentEvents::POST_DELETE;
                break;
            default:
                return null;
        }

        if ($this->dispatcher->hasListeners($name)) {
            if (empty($event)) {
                $event = new DynamicContentEvent($entity, $isNew);
                $event->setEntityManager($this->em);
            }

            $this->dispatcher->dispatch($name, $event);

            return $event;
        } else {
            return null;
        }
    }

    /**
     * Joins the page table and limits created_by to currently logged in user.
     */
    public function limitQueryToCreator(QueryBuilder &$q)
    {
        $q->join('t', MAUTIC_TABLE_PREFIX.'dynamic_content', 'd', 'd.id = t.dynamic_content_id')
            ->andWhere('d.created_by = :userId')
            ->setParameter('userId', $this->userHelper->getUser()->getId());
    }

    /**
     * Get line chart data of hits.
     *
     * @param char   $unit          {@link php.net/manual/en/function.date.php#refsect1-function.date-parameters}
     * @param string $dateFormat
     * @param array  $filter
     * @param bool   $canViewOthers
     *
     * @return array
     */
    public function getHitsLineChartData($unit, \DateTime $dateFrom, \DateTime $dateTo, $dateFormat = null, $filter = [], $canViewOthers = true)
    {
        $flag = null;

        if (isset($filter['flag'])) {
            $flag = $filter['flag'];
            unset($filter['flag']);
        }

        $chart = new LineChart($unit, $dateFrom, $dateTo, $dateFormat);
        $query = new ChartQuery($this->em->getConnection(), $dateFrom, $dateTo);

        if (!$flag || 'total_and_unique' === $flag) {
            $q = $query->prepareTimeDataQuery('dynamic_content_stats', 'date_sent', $filter);

            if (!$canViewOthers) {
                $this->limitQueryToCreator($q);
            }

            $data = $query->loadAndBuildTimeData($q);
            $chart->setDataset($this->translator->trans('autoborna.dynamicContent.show.total.views'), $data);
        }

        if ('unique' === $flag || 'total_and_unique' === $flag) {
            $q = $query->prepareTimeDataQuery('dynamic_content_stats', 'date_sent', $filter);
            $q->groupBy('t.lead_id, t.date_sent');

            if (!$canViewOthers) {
                $this->limitQueryToCreator($q);
            }

            $data = $query->loadAndBuildTimeData($q);
            $chart->setDataset($this->translator->trans('autoborna.dynamicContent.show.unique.views'), $data);
        }

        return $chart->render();
    }

    /**
     * @param        $type
     * @param string $filter
     * @param int    $limit
     * @param int    $start
     * @param array  $options
     */
    public function getLookupResults($type, $filter = '', $limit = 10, $start = 0, $options = [])
    {
        $results = [];
        switch ($type) {
            case 'dynamicContent':
                $entities = $this->getRepository()->getDynamicContentList(
                    $filter,
                    $limit,
                    $start,
                    $this->security->isGranted($this->getPermissionBase().':viewother'),
                    isset($options['top_level']) ? $options['top_level'] : false,
                    isset($options['ignore_ids']) ? $options['ignore_ids'] : [],
                    isset($options['where']) ? $options['where'] : ''
                );

                foreach ($entities as $entity) {
                    $results[$entity['language']][$entity['id']] = $entity['name'];
                }

                //sort by language
                ksort($results);

                break;
        }

        return $results;
    }
}
