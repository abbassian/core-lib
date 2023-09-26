<?php

namespace Autoborna\PointBundle\Model;

use Autoborna\CoreBundle\Model\FormModel as CommonFormModel;
use Autoborna\PointBundle\Entity\TriggerEvent;
use Autoborna\PointBundle\Entity\TriggerEventRepository;
use Autoborna\PointBundle\Form\Type\TriggerEventType;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class TriggerEventModel extends CommonFormModel
{
    /**
     * {@inheritdoc}
     *
     * @return TriggerEventRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository('AutobornaPointBundle:TriggerEvent');
    }

    /**
     * {@inheritdoc}
     */
    public function getPermissionBase()
    {
        return 'point:triggers';
    }

    /**
     * {@inheritdoc}
     *
     * @return TriggerEvent|null
     */
    public function getEntity($id = null)
    {
        if (null === $id) {
            return new TriggerEvent();
        }

        return parent::getEntity($id);
    }

    /**
     * {@inheritdoc}
     *
     * @throws MethodNotAllowedHttpException
     */
    public function createForm($entity, $formFactory, $action = null, $options = [])
    {
        if (!$entity instanceof TriggerEvent) {
            throw new MethodNotAllowedHttpException(['Trigger']);
        }

        if (!empty($action)) {
            $options['action'] = $action;
        }

        return $formFactory->create(TriggerEventType::class, $entity, $options);
    }

    /**
     * Get segments which are dependent on given segment.
     *
     * @param int $segmentId
     *
     * @return array
     */
    public function getReportIdsWithDependenciesOnSegment($segmentId)
    {
        $filter = [
            'force'  => [
                ['column' => 'e.type', 'expr' => 'eq', 'value'=>'lead.changelists'],
            ],
        ];
        $entities = $this->getEntities(
            [
                'filter'     => $filter,
            ]
        );
        $dependents = [];
        foreach ($entities as $entity) {
            $retrFilters = $entity->getProperties();
            foreach ($retrFilters as $eachFilter) {
                if (in_array($segmentId, $eachFilter)) {
                    $dependents[] = $entity->getTrigger()->getId();
                }
            }
        }

        return $dependents;
    }
}
