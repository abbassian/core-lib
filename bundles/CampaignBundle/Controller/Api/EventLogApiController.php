<?php

namespace Autoborna\CampaignBundle\Controller\Api;

use Autoborna\ApiBundle\Controller\CommonApiController;
use Autoborna\ApiBundle\Serializer\Exclusion\FieldInclusionStrategy;
use Autoborna\CampaignBundle\Entity\Campaign;
use Autoborna\CampaignBundle\Entity\Event;
use Autoborna\CampaignBundle\Model\EventLogModel;
use Autoborna\CampaignBundle\Model\EventModel;
use Autoborna\LeadBundle\Controller\LeadAccessTrait;
use Autoborna\LeadBundle\Entity\Lead;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Class EventLogApiController.
 */
class EventLogApiController extends CommonApiController
{
    use LeadAccessTrait;

    /**
     * @var Campaign
     */
    protected $campaign;

    /**
     * @var Lead
     */
    protected $contact;

    /** @var EventLogModel */
    protected $model;

    public function initialize(FilterControllerEvent $event)
    {
        $this->model                    = $this->getModel('campaign.event_log');
        $this->entityClass              = 'Autoborna\CampaignBundle\Entity\LeadEventLog';
        $this->entityNameOne            = 'event';
        $this->entityNameMulti          = 'events';
        $this->parentChildrenLevelDepth = 1;
        $this->serializerGroups         = [
            'campaignList',
            'ipAddressList',
            'log' => 'campaignEventLogDetails',
        ];

        // Only include the id of the parent
        $this->addExclusionStrategy(new FieldInclusionStrategy(['id'], 1, 'parent'));

        parent::initialize($event);
    }

    /**
     * @return Response
     */
    public function getEntitiesAction()
    {
        $this->serializerGroups['log'] = 'campaignEventStandaloneLogDetails';
        $this->serializerGroups[]      = 'campaignEventStandaloneList';
        $this->serializerGroups[]      = 'leadBasicList';

        return parent::getEntitiesAction(); // TODO: Change the autogenerated stub
    }

    /**
     * Get a list of events.
     *
     * @param      $contactId
     * @param null $campaignId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getContactEventsAction($contactId, $campaignId = null)
    {
        // Ensure contact exists and user has access
        $contact = $this->checkLeadAccess($contactId, 'view');
        if ($contact instanceof Response) {
            return $contact;
        }

        // Ensure campaign exists and user has access
        if (!empty($campaignId)) {
            $campaign = $this->getModel('campaign')->getEntity($campaignId);
            if (null == $campaign || !$campaign->getId()) {
                return $this->notFound();
            }
            if (!$this->checkEntityAccess($campaign)) {
                return $this->accessDenied();
            }
            // Check that contact is part of the campaign
            $membership = $campaign->getContactMembership($contact);
            if (0 === count($membership)) {
                return $this->returnError(
                    $this->translator->trans(
                        'autoborna.campaign.error.contact_not_in_campaign',
                        ['%campaign%' => $campaignId, '%contact%' => $contactId]
                    ),
                    Response::HTTP_CONFLICT
                );
            }

            $this->campaign           = $campaign;
            $this->serializerGroups[] = 'campaignEventWithLogsList';
            $this->serializerGroups[] = 'campaignLeadList';
        } else {
            unset($this->serializerGroups['log']);
            $this->serializerGroups[] = 'campaignEventStandaloneList';
            $this->serializerGroups[] = 'campaignEventStandaloneLogDetails';
        }

        $this->contact                   = $contact;
        $this->extraGetEntitiesArguments = [
            'contact_id'  => $contactId,
            'campaign_id' => $campaignId,
        ];

        return $this->getEntitiesAction();
    }

    /**
     * @param $eventId
     * @param $contactId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editContactEventAction($eventId, $contactId)
    {
        $parameters = $this->request->request->all();

        // Ensure contact exists and user has access
        $contact = $this->checkLeadAccess($contactId, 'edit');
        if ($contact instanceof Response) {
            return $contact;
        }

        /** @var EventModel $eventModel */
        $eventModel = $this->getModel('campaign.event');
        /** @var Event $event */
        $event = $eventModel->getEntity($eventId);
        if (null === $event || !$event->getId()) {
            return $this->notFound();
        }

        // Ensure campaign edit access
        $campaign = $event->getCampaign();
        if (!$this->checkEntityAccess($campaign, 'edit')) {
            return $this->accessDenied();
        }

        $result = $this->model->updateContactEvent($event, $contact, $parameters);

        if (is_string($result)) {
            return $this->returnError($result, Response::HTTP_CONFLICT);
        } else {
            list($log, $created) = $result;
        }

        $event->addContactLog($log);
        $view = $this->view(
            [
                $this->entityNameOne => $event,
            ],
            ($created) ? Response::HTTP_CREATED : Response::HTTP_OK
        );
        $this->serializerGroups[] = 'campaignEventWithLogsDetails';
        $this->serializerGroups[] = 'campaignBasicList';
        $this->setSerializationContext($view);

        return $this->handleView($view);
    }

    /**
     * @return array|Response
     */
    public function editEventsAction()
    {
        $parameters = $this->request->request->all();

        $valid = $this->validateBatchPayload($parameters);
        if ($valid instanceof Response) {
            return $valid;
        }

        $events   = $this->getBatchEntities($parameters, $errors, false, 'eventId', $this->getModel('campaign.event'), false);
        $contacts = $this->getBatchEntities($parameters, $errors, false, 'contactId', $this->getModel('lead'), false);

        $this->inBatchMode = true;
        $errors            = [];
        foreach ($parameters as $key => $params) {
            if (!isset($params['eventId']) || !isset($params['contactId']) || !isset($events[$params['eventId']])
                || !isset($contacts[$params['contactId']])
            ) {
                $errors[$key] = $this->notFound('autoborna.campaign.error.edit_events.request_invalid');

                continue;
            }

            $event = $events[$params['eventId']];

            // Ensure contact exists and user has access
            $contact = $this->checkLeadAccess($contacts[$params['contactId']], 'edit');
            if ($contact instanceof Response) {
                $errors[$key] = $contact->getContent();

                continue;
            }

            // Ensure campaign edit access
            $campaign = $event->getCampaign();
            if (!$this->checkEntityAccess($campaign, 'edit')) {
                $errors[$key] = $this->accessDenied();

                continue;
            }

            $result = $this->model->updateContactEvent($event, $contact, $params);

            if (is_string($result)) {
                $errors[$key] = $this->returnError($result, Response::HTTP_CONFLICT);
            } else {
                list($log, $created) = $result;
                $event->addContactLog($log);
            }
        }

        $payload = [
            $this->entityNameMulti => $events,
        ];

        if (!empty($errors)) {
            $payload['errors'] = $errors;
        }

        $view                     = $this->view($payload, Response::HTTP_OK);
        $this->serializerGroups[] = 'campaignEventWithLogsList';
        $this->setSerializationContext($view);

        return $this->handleView($view);
    }

    /**
     * @param null $data
     * @param null $statusCode
     *
     * @return \FOS\RestBundle\View\View
     */
    protected function view($data = null, $statusCode = null, array $headers = [])
    {
        if ($this->campaign) {
            $data['campaign'] = $this->campaign;

            if ($this->contact) {
                list($data['membership'], $ignore) = $this->prepareEntitiesForView($this->campaign->getContactMembership($this->contact));
            }
        }

        return parent::view($data, $statusCode, $headers);
    }
}
