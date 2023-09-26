<?php

namespace Autoborna\LeadBundle\Controller\Api;

use Autoborna\ApiBundle\Controller\CommonApiController;
use Autoborna\LeadBundle\Entity\Tag;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class TagApiController extends CommonApiController
{
    public function initialize(FilterControllerEvent $event)
    {
        $this->model           = $this->getModel('lead.tag');
        $this->entityClass     = Tag::class;
        $this->entityNameOne   = 'tag';
        $this->entityNameMulti = 'tags';

        parent::initialize($event);
    }

    /**
     * Creates new entity from provided params.
     *
     * @return object
     *
     * @throws \InvalidArgumentException
     */
    public function getNewEntity(array $params)
    {
        if (empty($params[$this->entityNameOne])) {
            throw new \InvalidArgumentException($this->get('translator')->trans('autoborna.lead.api.tag.required', [], 'validators'));
        }

        return $this->model->getRepository()->getTagByNameOrCreateNewOne($params[$this->entityNameOne]);
    }
}
