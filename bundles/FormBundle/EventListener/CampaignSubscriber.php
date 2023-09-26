<?php

namespace Autoborna\FormBundle\EventListener;

use Autoborna\CampaignBundle\CampaignEvents;
use Autoborna\CampaignBundle\Event\CampaignBuilderEvent;
use Autoborna\CampaignBundle\Event\CampaignExecutionEvent;
use Autoborna\CampaignBundle\Executioner\RealTimeExecutioner;
use Autoborna\CoreBundle\Helper\InputHelper;
use Autoborna\FormBundle\Event\SubmissionEvent;
use Autoborna\FormBundle\Form\Type\CampaignEventFormFieldValueType;
use Autoborna\FormBundle\Form\Type\CampaignEventFormSubmitType;
use Autoborna\FormBundle\FormEvents;
use Autoborna\FormBundle\Helper\FormFieldHelper;
use Autoborna\FormBundle\Model\FormModel;
use Autoborna\FormBundle\Model\SubmissionModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CampaignSubscriber implements EventSubscriberInterface
{
    /**
     * @var FormModel
     */
    private $formModel;

    /**
     * @var SubmissionModel
     */
    private $formSubmissionModel;

    /**
     * @var RealTimeExecutioner
     */
    private $realTimeExecutioner;

    /**
     * @var FormFieldHelper
     */
    private $formFieldHelper;

    public function __construct(FormModel $formModel, SubmissionModel $formSubmissionModel, RealTimeExecutioner $realTimeExecutioner, FormFieldHelper $formFieldHelper)
    {
        $this->formModel           = $formModel;
        $this->formSubmissionModel = $formSubmissionModel;
        $this->realTimeExecutioner = $realTimeExecutioner;
        $this->formFieldHelper     = $formFieldHelper;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            CampaignEvents::CAMPAIGN_ON_BUILD         => ['onCampaignBuild', 0],
            FormEvents::FORM_ON_SUBMIT                => ['onFormSubmit', 0],
            FormEvents::ON_CAMPAIGN_TRIGGER_DECISION  => ['onCampaignTriggerDecision', 0],
            FormEvents::ON_CAMPAIGN_TRIGGER_CONDITION => ['onCampaignTriggerCondition', 0],
        ];
    }

    /**
     * Add the option to the list.
     */
    public function onCampaignBuild(CampaignBuilderEvent $event)
    {
        $trigger = [
            'label'       => 'autoborna.form.campaign.event.submit',
            'description' => 'autoborna.form.campaign.event.submit_descr',
            'formType'    => CampaignEventFormSubmitType::class,
            'eventName'   => FormEvents::ON_CAMPAIGN_TRIGGER_DECISION,
        ];
        $event->addDecision('form.submit', $trigger);

        $trigger = [
            'label'       => 'autoborna.form.campaign.event.field_value',
            'description' => 'autoborna.form.campaign.event.field_value_descr',
            'formType'    => CampaignEventFormFieldValueType::class,
            'formTheme'   => 'AutobornaFormBundle:FormTheme\FieldValueCondition',
            'eventName'   => FormEvents::ON_CAMPAIGN_TRIGGER_CONDITION,
        ];
        $event->addCondition('form.field_value', $trigger);
    }

    /**
     * Trigger campaign event for when a form is submitted.
     */
    public function onFormSubmit(SubmissionEvent $event)
    {
        $form = $event->getSubmission()->getForm();
        $this->realTimeExecutioner->execute('form.submit', $form, 'form', $form->getId());
    }

    public function onCampaignTriggerDecision(CampaignExecutionEvent $event)
    {
        $eventDetails = $event->getEventDetails();

        if (null === $eventDetails) {
            return $event->setResult(true);
        }

        $limitToForms = $event->getConfig()['forms'];

        //check against selected forms
        if (!empty($limitToForms) && !in_array($eventDetails->getId(), $limitToForms)) {
            return $event->setResult(false);
        }

        return $event->setResult(true);
    }

    public function onCampaignTriggerCondition(CampaignExecutionEvent $event)
    {
        $lead = $event->getLead();

        if (!$lead || !$lead->getId()) {
            return $event->setResult(false);
        }

        $operators = $this->formModel->getFilterExpressionFunctions();
        $form      = $this->formModel->getRepository()->findOneById($event->getConfig()['form']);

        if (!$form || !$form->getId()) {
            return $event->setResult(false);
        }

        $field = $this->formModel->findFormFieldByAlias($form, $event->getConfig()['field']);

        $filter = $this->formFieldHelper->getFieldFilter($field->getType());
        $value  = InputHelper::_($event->getConfig()['value'], $filter);

        $result = $this->formSubmissionModel->getRepository()->compareValue(
            $lead->getId(),
            $form->getId(),
            $form->getAlias(),
            $event->getConfig()['field'],
            $value,
            $operators[$event->getConfig()['operator']]['expr'],
            $field ? $field->getType() : null
        );

        $event->setChannel('form', $form->getId());

        return $event->setResult($result);
    }
}
