<?php

namespace Autoborna\UserBundle\Controller\Api;

use Autoborna\ApiBundle\Controller\CommonApiController;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Class RoleApiController.
 */
class RoleApiController extends CommonApiController
{
    /**
     * {@inheritdoc}
     */
    public function initialize(FilterControllerEvent $event)
    {
        $this->model            = $this->getModel('user.role');
        $this->entityClass      = 'Autoborna\UserBundle\Entity\Role';
        $this->entityNameOne    = 'role';
        $this->entityNameMulti  = 'roles';
        $this->serializerGroups = ['roleDetails', 'publishDetails'];

        parent::initialize($event);
    }

    /**
     * {@inheritdoc}
     *
     * @param \Autoborna\LeadBundle\Entity\Lead &$entity
     * @param                                $parameters
     * @param                                $form
     * @param string                         $action
     */
    protected function preSaveEntity(&$entity, $form, $parameters, $action = 'edit')
    {
        if (isset($parameters['rawPermissions'])) {
            $this->model->setRolePermissions($entity, $parameters['rawPermissions']);
        }
    }
}
