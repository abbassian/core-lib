<?php

namespace Autoborna\LeadBundle\EventListener;

use Autoborna\CampaignBundle\CampaignEvents;
use Autoborna\CampaignBundle\Event\CampaignBuilderEvent;
use Autoborna\CampaignBundle\Event\CampaignExecutionEvent;
use Autoborna\CampaignBundle\Model\CampaignModel;
use Autoborna\CoreBundle\Helper\CoreParametersHelper;
use Autoborna\CoreBundle\Helper\IpLookupHelper;
use Autoborna\LeadBundle\Entity\Company;
use Autoborna\LeadBundle\Entity\Lead;
use Autoborna\LeadBundle\Entity\PointsChangeLog;
use Autoborna\LeadBundle\Form\Type\AddToCompanyActionType;
use Autoborna\LeadBundle\Form\Type\CampaignEventLeadCampaignsType;
use Autoborna\LeadBundle\Form\Type\CampaignEventLeadDeviceType;
use Autoborna\LeadBundle\Form\Type\CampaignEventLeadFieldValueType;
use Autoborna\LeadBundle\Form\Type\CampaignEventLeadOwnerType;
use Autoborna\LeadBundle\Form\Type\CampaignEventLeadSegmentsType;
use Autoborna\LeadBundle\Form\Type\CampaignEventLeadTagsType;
use Autoborna\LeadBundle\Form\Type\ChangeOwnerType;
use Autoborna\LeadBundle\Form\Type\CompanyChangeScoreActionType;
use Autoborna\LeadBundle\Form\Type\ListActionType;
use Autoborna\LeadBundle\Form\Type\ModifyLeadTagsType;
use Autoborna\LeadBundle\Form\Type\PointActionType;
use Autoborna\LeadBundle\Form\Type\UpdateCompanyActionType;
use Autoborna\LeadBundle\Form\Type\UpdateLeadActionType;
use Autoborna\LeadBundle\Helper\CustomFieldHelper;
use Autoborna\LeadBundle\Helper\IdentifyCompanyHelper;
use Autoborna\LeadBundle\LeadEvents;
use Autoborna\LeadBundle\Model\CompanyModel;
use Autoborna\LeadBundle\Model\FieldModel;
use Autoborna\LeadBundle\Model\LeadModel;
use Autoborna\LeadBundle\Model\ListModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CampaignSubscriber implements EventSubscriberInterface
{
    const ACTION_LEAD_CHANGE_OWNER = 'lead.changeowner';

    /**
     * @var IpLookupHelper
     */
    private $ipLookupHelper;

    /**
     * @var LeadModel
     */
    private $leadModel;

    /**
     * @var FieldModel
     */
    private $leadFieldModel;

    /**
     * @var ListModel
     */
    private $listModel;

    /**
     * @var CompanyModel
     */
    private $companyModel;

    /**
     * @var CampaignModel
     */
    private $campaignModel;

    /**
     * @var CoreParametersHelper
     */
    private $coreParametersHelper;

    /**
     * @var array
     */
    private $fields;

    public function __construct(
        IpLookupHelper $ipLookupHelper,
        LeadModel $leadModel,
        FieldModel $leadFieldModel,
        ListModel $listModel,
        CompanyModel $companyModel,
        CampaignModel $campaignModel,
        CoreParametersHelper $coreParametersHelper
    ) {
        $this->ipLookupHelper       = $ipLookupHelper;
        $this->leadModel            = $leadModel;
        $this->leadFieldModel       = $leadFieldModel;
        $this->listModel            = $listModel;
        $this->companyModel         = $companyModel;
        $this->campaignModel        = $campaignModel;
        $this->coreParametersHelper = $coreParametersHelper;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            CampaignEvents::CAMPAIGN_ON_BUILD      => ['onCampaignBuild', 0],
            LeadEvents::ON_CAMPAIGN_TRIGGER_ACTION => [
                ['onCampaignTriggerActionChangePoints', 0],
                ['onCampaignTriggerActionChangeLists', 1],
                ['onCampaignTriggerActionUpdateLead', 2],
                ['onCampaignTriggerActionUpdateTags', 3],
                ['onCampaignTriggerActionAddToCompany', 4],
                ['onCampaignTriggerActionChangeCompanyScore', 4],
                ['onCampaignTriggerActionChangeOwner', 7],
                ['onCampaignTriggerActionUpdateCompany', 8],
            ],
            LeadEvents::ON_CAMPAIGN_TRIGGER_CONDITION => ['onCampaignTriggerCondition', 0],
        ];
    }

    /**
     * Add event triggers and actions.
     */
    public function onCampaignBuild(CampaignBuilderEvent $event)
    {
        //Add actions
        $action = [
            'label'       => 'autoborna.lead.lead.events.changepoints',
            'description' => 'autoborna.lead.lead.events.changepoints_descr',
            'formType'    => PointActionType::class,
            'eventName'   => LeadEvents::ON_CAMPAIGN_TRIGGER_ACTION,
        ];
        $event->addAction('lead.changepoints', $action);

        $action = [
            'label'       => 'autoborna.lead.lead.events.changelist',
            'description' => 'autoborna.lead.lead.events.changelist_descr',
            'formType'    => ListActionType::class,
            'eventName'   => LeadEvents::ON_CAMPAIGN_TRIGGER_ACTION,
        ];
        $event->addAction('lead.changelist', $action);

        $action = [
            'label'       => 'autoborna.lead.lead.events.updatelead',
            'description' => 'autoborna.lead.lead.events.updatelead_descr',
            'formType'    => UpdateLeadActionType::class,
            'formTheme'   => 'AutobornaLeadBundle:FormTheme\ActionUpdateLead',
            'eventName'   => LeadEvents::ON_CAMPAIGN_TRIGGER_ACTION,
        ];
        $event->addAction('lead.updatelead', $action);

        $action = [
            'label'       => 'autoborna.lead.lead.events.updatecompany',
            'description' => 'autoborna.lead.lead.events.updatecompany_descr',
            'formType'    => UpdateCompanyActionType::class,
            'formTheme'   => 'AutobornaLeadBundle:FormTheme\ActionUpdateCompany',
            'eventName'   => LeadEvents::ON_CAMPAIGN_TRIGGER_ACTION,
        ];
        $event->addAction('lead.updatecompany', $action);

        $action = [
            'label'       => 'autoborna.lead.lead.events.changetags',
            'description' => 'autoborna.lead.lead.events.changetags_descr',
            'formType'    => ModifyLeadTagsType::class,
            'eventName'   => LeadEvents::ON_CAMPAIGN_TRIGGER_ACTION,
        ];
        $event->addAction('lead.changetags', $action);

        $action = [
            'label'       => 'autoborna.lead.lead.events.addtocompany',
            'description' => 'autoborna.lead.lead.events.addtocompany_descr',
            'formType'    => AddToCompanyActionType::class,
            'eventName'   => LeadEvents::ON_CAMPAIGN_TRIGGER_ACTION,
        ];
        $event->addAction('lead.addtocompany', $action);

        $action = [
            'label'       => 'autoborna.lead.lead.events.changeowner',
            'description' => 'autoborna.lead.lead.events.changeowner_descr',
            'formType'    => ChangeOwnerType::class,
            'eventName'   => LeadEvents::ON_CAMPAIGN_TRIGGER_ACTION,
        ];
        $event->addAction(self::ACTION_LEAD_CHANGE_OWNER, $action);

        $action = [
            'label'       => 'autoborna.lead.lead.events.changecompanyscore',
            'description' => 'autoborna.lead.lead.events.changecompanyscore_descr',
            'formType'    => CompanyChangeScoreActionType::class,
            'eventName'   => LeadEvents::ON_CAMPAIGN_TRIGGER_ACTION,
        ];
        $event->addAction('lead.scorecontactscompanies', $action);

        $trigger = [
            'label'       => 'autoborna.lead.lead.events.field_value',
            'description' => 'autoborna.lead.lead.events.field_value_descr',
            'formType'    => CampaignEventLeadFieldValueType::class,
            'formTheme'   => 'AutobornaLeadBundle:FormTheme\FieldValueCondition',
            'eventName'   => LeadEvents::ON_CAMPAIGN_TRIGGER_CONDITION,
        ];
        $event->addCondition('lead.field_value', $trigger);

        $trigger = [
            'label'       => 'autoborna.lead.lead.events.device',
            'description' => 'autoborna.lead.lead.events.device_descr',
            'formType'    => CampaignEventLeadDeviceType::class,
            'eventName'   => LeadEvents::ON_CAMPAIGN_TRIGGER_CONDITION,
        ];

        $event->addCondition('lead.device', $trigger);

        $trigger = [
            'label'       => 'autoborna.lead.lead.events.tags',
            'description' => 'autoborna.lead.lead.events.tags_descr',
            'formType'    => CampaignEventLeadTagsType::class,
            'eventName'   => LeadEvents::ON_CAMPAIGN_TRIGGER_CONDITION,
        ];
        $event->addCondition('lead.tags', $trigger);

        $trigger = [
            'label'       => 'autoborna.lead.lead.events.segments',
            'description' => 'autoborna.lead.lead.events.segments_descr',
            'formType'    => CampaignEventLeadSegmentsType::class,
            'eventName'   => LeadEvents::ON_CAMPAIGN_TRIGGER_CONDITION,
        ];

        $event->addCondition('lead.segments', $trigger);

        $trigger = [
            'label'       => 'autoborna.lead.lead.events.owner',
            'description' => 'autoborna.lead.lead.events.owner_descr',
            'formType'    => CampaignEventLeadOwnerType::class,
            'eventName'   => LeadEvents::ON_CAMPAIGN_TRIGGER_CONDITION,
        ];

        $event->addCondition('lead.owner', $trigger);

        $trigger = [
            'label'       => 'autoborna.lead.lead.events.campaigns',
            'description' => 'autoborna.lead.lead.events.campaigns_descr',
            'formType'    => CampaignEventLeadCampaignsType::class,
            'formTheme'   => 'AutobornaLeadBundle:FormTheme\ContactCampaignsCondition',
            'eventName'   => LeadEvents::ON_CAMPAIGN_TRIGGER_CONDITION,
        ];

        $event->addCondition('lead.campaigns', $trigger);
    }

    public function onCampaignTriggerActionChangePoints(CampaignExecutionEvent $event)
    {
        if (!$event->checkContext('lead.changepoints')) {
            return;
        }

        $lead   = $event->getLead();
        $points = $event->getConfig()['points'];

        $somethingHappened = false;

        if (null !== $lead && !empty($points)) {
            $lead->adjustPoints($points);

            //add a lead point change log
            $log = new PointsChangeLog();
            $log->setDelta($points);
            $log->setLead($lead);
            $log->setType('campaign');
            $log->setEventName("{$event->getEvent()['campaign']['id']}: {$event->getEvent()['campaign']['name']}");
            $log->setActionName("{$event->getEvent()['id']}: {$event->getEvent()['name']}");
            $log->setIpAddress($this->ipLookupHelper->getIpAddress());
            $log->setDateAdded(new \DateTime());
            $lead->addPointsChangeLog($log);

            $this->leadModel->saveEntity($lead);
            $somethingHappened = true;
        }

        return $event->setResult($somethingHappened);
    }

    public function onCampaignTriggerActionChangeLists(CampaignExecutionEvent $event)
    {
        if (!$event->checkContext('lead.changelist')) {
            return;
        }

        $addTo      = $event->getConfig()['addToLists'];
        $removeFrom = $event->getConfig()['removeFromLists'];

        $lead              = $event->getLead();
        $somethingHappened = false;

        if (!empty($addTo)) {
            $this->leadModel->addToLists($lead, $addTo);
            $somethingHappened = true;
        }

        if (!empty($removeFrom)) {
            $this->leadModel->removeFromLists($lead, $removeFrom);
            $somethingHappened = true;
        }

        return $event->setResult($somethingHappened);
    }

    public function onCampaignTriggerActionUpdateLead(CampaignExecutionEvent $event)
    {
        if (!$event->checkContext('lead.updatelead')) {
            return;
        }

        $lead   = $event->getLead();
        $values = $event->getConfig();
        $fields = $lead->getFields(true);

        $this->leadModel->setFieldValues($lead, CustomFieldHelper::fieldsValuesTransformer($fields, $values), false);
        $this->leadModel->saveEntity($lead);

        return $event->setResult(true);
    }

    public function onCampaignTriggerActionChangeOwner(CampaignExecutionEvent $event)
    {
        if (!$event->checkContext(self::ACTION_LEAD_CHANGE_OWNER)) {
            return;
        }

        $lead = $event->getLead();
        $data = $event->getConfig();
        if (empty($data['owner'])) {
            return;
        }

        $this->leadModel->updateLeadOwner($lead, $data['owner']);

        return $event->setResult(true);
    }

    public function onCampaignTriggerActionUpdateTags(CampaignExecutionEvent $event)
    {
        if (!$event->checkContext('lead.changetags')) {
            return;
        }

        $config = $event->getConfig();
        $lead   = $event->getLead();

        $addTags    = (!empty($config['add_tags'])) ? $config['add_tags'] : [];
        $removeTags = (!empty($config['remove_tags'])) ? $config['remove_tags'] : [];

        $this->leadModel->modifyTags($lead, $addTags, $removeTags);

        return $event->setResult(true);
    }

    public function onCampaignTriggerActionAddToCompany(CampaignExecutionEvent $event)
    {
        if (!$event->checkContext('lead.addtocompany')) {
            return;
        }

        $company = $event->getConfig()['company'];
        $lead    = $event->getLead();

        if (!empty($company)) {
            $this->leadModel->addToCompany($lead, $company);
        }
    }

    public function onCampaignTriggerActionChangeCompanyScore(CampaignExecutionEvent $event)
    {
        if (!$event->checkContext('lead.scorecontactscompanies')) {
            return;
        }

        $score = $event->getConfig()['score'];
        $lead  = $event->getLead();

        if (!$this->leadModel->scoreContactsCompany($lead, $score)) {
            return $event->setFailed('autoborna.lead.no_company');
        } else {
            return $event->setResult(true);
        }
    }

    public function onCampaignTriggerActionUpdateCompany(CampaignExecutionEvent $event)
    {
        if (!$event->checkContext('lead.updatecompany')) {
            return;
        }

        $lead    = $event->getLead();
        $company = $lead->getPrimaryCompany();
        $config  = $event->getConfig();

        if (empty($company['id'])) {
            return;
        }

        $primaryCompany =  $this->companyModel->getEntity($company['id']);

        if (isset($config['companyname']) && $primaryCompany->getName() != $config['companyname']) {
            [$company, $leadAdded, $companyEntity] = IdentifyCompanyHelper::identifyLeadsCompany($config, $lead, $this->companyModel);
            if ($leadAdded) {
                $lead->addCompanyChangeLogEntry('form', 'Identify Company', 'Lead added to the company, '.$company['companyname'], $company['id']);
            } elseif ($companyEntity instanceof Company) {
                $this->companyModel->setFieldValues($companyEntity, $config);
                $this->companyModel->saveEntity($companyEntity);
            }

            if (!empty($company)) {
                // Save after the lead in for new leads created
                $this->companyModel->addLeadToCompany($companyEntity, $lead);
                $this->leadModel->setPrimaryCompany($companyEntity->getId(), $lead->getId());
            }
        } else {
            $this->companyModel->setFieldValues($primaryCompany, $config, false);
            $this->companyModel->saveEntity($primaryCompany);
        }

        return $event->setResult(true);
    }

    public function onCampaignTriggerCondition(CampaignExecutionEvent $event)
    {
        $lead   = $event->getLead();
        $result = false;

        if (!$lead || !$lead->getId()) {
            return $event->setResult(false);
        }

        if ($event->checkContext('lead.device')) {
            $deviceRepo = $this->leadModel->getDeviceRepository();
            $result     = false;

            $deviceType   = $event->getConfig()['device_type'];
            $deviceBrands = $event->getConfig()['device_brand'];
            $deviceOs     = $event->getConfig()['device_os'];

            if (!empty($deviceType)) {
                $result = false;
                if (!empty($deviceRepo->getDevice($lead, $deviceType))) {
                    $result = true;
                }
            }

            if (!empty($deviceBrands)) {
                $result = false;
                if (!empty($deviceRepo->getDevice($lead, null, $deviceBrands))) {
                    $result = true;
                }
            }

            if (!empty($deviceOs)) {
                $result = false;
                if (!empty($deviceRepo->getDevice($lead, null, null, null, $deviceOs))) {
                    $result = true;
                }
            }
        } elseif ($event->checkContext('lead.tags')) {
            $tagRepo = $this->leadModel->getTagRepository();
            $result  = $tagRepo->checkLeadByTags($lead, $event->getConfig()['tags']);
        } elseif ($event->checkContext('lead.segments')) {
            $listRepo = $this->listModel->getRepository();
            $result   = $listRepo->checkLeadSegmentsByIds($lead, $event->getConfig()['segments']);
        } elseif ($event->checkContext('lead.owner')) {
            $result = $this->leadModel->getRepository()->checkLeadOwner($lead, $event->getConfig()['owner']);
        } elseif ($event->checkContext('lead.campaigns')) {
            $result = $this->campaignModel->getCampaignLeadRepository()->checkLeadInCampaigns($lead, $event->getConfig());
        } elseif ($event->checkContext('lead.field_value')) {
            if ('date' === $event->getConfig()['operator']) {
                // Set the date in system timezone since this is triggered by cron
                $triggerDate = new \DateTime('now', new \DateTimeZone($this->coreParametersHelper->get('default_timezone')));
                $interval    = substr($event->getConfig()['value'], 1); // remove 1st character + or -

                if (false !== strpos($event->getConfig()['value'], '+P')) { //add date
                    $triggerDate->add(new \DateInterval($interval)); //add the today date with interval
                    $result = $this->compareDateValue($lead, $event, $triggerDate);
                } elseif (false !== strpos($event->getConfig()['value'], '-P')) { //subtract date
                    $triggerDate->sub(new \DateInterval($interval)); //subtract the today date with interval
                    $result = $this->compareDateValue($lead, $event, $triggerDate);
                } elseif ('anniversary' === $event->getConfig()['value']) {
                    /**
                     * note: currently autoborna campaign only one time execution
                     * ( to integrate with: recursive campaign (future)).
                     */
                    $result = $this->leadFieldModel->getRepository()->compareDateMonthValue(
                        $lead->getId(), $event->getConfig()['field'], $triggerDate);
                }
            } else {
                $operators = $this->leadModel->getFilterExpressionFunctions();
                $field     = $event->getConfig()['field'];
                $value     = $event->getConfig()['value'];
                $fields    = $this->getFields($lead);

                $fieldValue = isset($fields[$field]) ? CustomFieldHelper::fieldValueTransfomer($fields[$field], $value) : $value;
                $result     = $this->leadFieldModel->getRepository()->compareValue(
                    $lead->getId(),
                    $field,
                    $fieldValue,
                    $operators[$event->getConfig()['operator']]['expr']
                );
            }
        }

        return $event->setResult($result);
    }

    /**
     * Function to compare date value.
     *
     * @return bool
     */
    private function compareDateValue(Lead $lead, CampaignExecutionEvent $event, \DateTime $triggerDate)
    {
        return $this->leadFieldModel->getRepository()->compareDateValue(
            $lead->getId(),
            $event->getConfig()['field'],
            $triggerDate->format('Y-m-d')
        );
    }

    protected function getFields(Lead $lead): array
    {
        if (!$this->fields) {
            $contactFields = $lead->getFields(true);
            $companyFields = $this->leadFieldModel->getFieldListWithProperties('company');
            $this->fields  = array_merge($contactFields, $companyFields);
        }

        return $this->fields;
    }
}
