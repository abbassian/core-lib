<?php

namespace Autoborna\CampaignBundle\Controller\Api;

use Autoborna\ApiBundle\Controller\CommonApiController;
use Autoborna\ApiBundle\Serializer\Exclusion\FieldExclusionStrategy;
use Autoborna\CampaignBundle\Entity\Event;
use Autoborna\LeadBundle\Controller\LeadAccessTrait;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Class EventApiController.
 */
class EventApiController extends CommonApiController
{
    use LeadAccessTrait;

    public function initialize(FilterControllerEvent $event)
    {
        $this->model                    = $this->getModel('campaign.event');
        $this->entityClass              = 'Autoborna\CampaignBundle\Entity\Event';
        $this->entityNameOne            = 'event';
        $this->entityNameMulti          = 'events';
        $this->serializerGroups         = ['campaignEventStandaloneDetails', 'campaignList'];
        $this->parentChildrenLevelDepth = 1;

        // Don't include campaign in children/parent arrays
        $this->addExclusionStrategy(new FieldExclusionStrategy(['campaign'], 1));

        parent::initialize($event);
    }

    /**
     * @param Event  $entity
     * @param string $action
     *
     * @return bool|mixed
     */
    protected function checkEntityAccess($entity, $action = 'view')
    {
        // Use the campaign for permission checks
        return parent::checkEntityAccess($entity->getCampaign(), $action);
    }
}
