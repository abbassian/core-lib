<?php

namespace Autoborna\DynamicContentBundle\Controller\Api;

use Autoborna\ApiBundle\Controller\CommonApiController;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Class DynamicContentApiController.
 */
class DynamicContentApiController extends CommonApiController
{
    /**
     * {@inheritdoc}
     */
    public function initialize(FilterControllerEvent $event)
    {
        $this->model           = $this->getModel('dynamicContent');
        $this->entityClass     = 'Autoborna\DynamicContentBundle\Entity\DynamicContent';
        $this->entityNameOne   = 'dynamicContent';
        $this->entityNameMulti = 'dynamicContents';

        parent::initialize($event);
    }
}
