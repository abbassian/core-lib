<?php

namespace Autoborna\StageBundle\EventListener;

use Autoborna\CampaignBundle\CampaignEvents;
use Autoborna\CampaignBundle\Entity\LeadEventLog;
use Autoborna\CampaignBundle\Event\CampaignBuilderEvent;
use Autoborna\CampaignBundle\Event\PendingEvent;
use Autoborna\LeadBundle\Entity\Lead;
use Autoborna\LeadBundle\Model\LeadModel;
use Autoborna\StageBundle\Entity\Stage;
use Autoborna\StageBundle\Form\Type\StageActionChangeType;
use Autoborna\StageBundle\Model\StageModel;
use Autoborna\StageBundle\StageEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;

class CampaignSubscriber implements EventSubscriberInterface
{
    /**
     * @var LeadModel
     */
    private $leadModel;

    /**
     * @var StageModel
     */
    private $stageModel;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(LeadModel $leadModel, StageModel $stageModel, TranslatorInterface $translator)
    {
        $this->leadModel  = $leadModel;
        $this->stageModel = $stageModel;
        $this->translator = $translator;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            CampaignEvents::CAMPAIGN_ON_BUILD     => ['onCampaignBuild', 0],
            StageEvents::ON_CAMPAIGN_BATCH_ACTION => ['onCampaignTriggerStageChange', 0],
        ];
    }

    public function onCampaignBuild(CampaignBuilderEvent $event)
    {
        $action = [
            'label'            => 'autoborna.stage.campaign.event.change',
            'description'      => 'autoborna.stage.campaign.event.change_descr',
            'batchEventName'   => StageEvents::ON_CAMPAIGN_BATCH_ACTION,
            'formType'         => StageActionChangeType::class,
            'formTheme'        => 'AutobornaStageBundle:FormTheme\StageActionChange',
        ];
        $event->addAction('stage.change', $action);
    }

    public function onCampaignTriggerStageChange(PendingEvent $event): void
    {
        $logs    = $event->getPending();
        $config  = $event->getEvent()->getProperties();
        $stageId = (int) $config['stage'];
        $stage   = $this->stageModel->getEntity($stageId);

        if (!$stage || !$stage->isPublished()) {
            $event->passAllWithError($this->translator->trans('autoborna.stage.campaign.event.stage_missing'));

            return;
        }

        foreach ($logs as $log) {
            $this->changeStage($log, $stage, $event);
        }
    }

    private function changeStage(LeadEventLog $log, Stage $stage, PendingEvent $pendingEvent): void
    {
        $lead      = $log->getLead();
        $leadStage = ($lead instanceof Lead) ? $lead->getStage() : null;

        if ($leadStage) {
            if ($leadStage->getId() === $stage->getId()) {
                $pendingEvent->passWithError($log, $this->translator->trans('autoborna.stage.campaign.event.already_in_stage'));

                return;
            }

            if ($leadStage->getWeight() > $stage->getWeight()) {
                $pendingEvent->passWithError($log, $this->translator->trans('autoborna.stage.campaign.event.stage_invalid'));

                return;
            }
        }

        $lead->stageChangeLogEntry(
            $stage,
            $stage->getId().': '.$stage->getName(),
            $log->getEvent()->getName()
        );
        $lead->setStage($stage);

        $this->leadModel->saveEntity($lead);

        $pendingEvent->pass($log);
    }
}
