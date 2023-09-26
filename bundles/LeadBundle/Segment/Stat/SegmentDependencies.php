<?php

namespace Autoborna\LeadBundle\Segment\Stat;

use Autoborna\CampaignBundle\Model\CampaignModel;
use Autoborna\EmailBundle\Model\EmailModel;
use Autoborna\FormBundle\Model\ActionModel;
use Autoborna\LeadBundle\Model\ListModel;
use Autoborna\PointBundle\Model\TriggerEventModel;
use Autoborna\ReportBundle\Model\ReportModel;

class SegmentDependencies
{
    /**
     * @var EmailModel
     */
    private $emailModel;

    /**
     * @var CampaignModel
     */
    private $campaignModel;

    /**
     * @var ActionModel
     */
    private $actionModel;

    /**
     * @var ListModel
     */
    private $listModel;

    /**
     * @var TriggerEventModel
     */
    private $triggerEventModel;

    /**
     * @var ReportModel
     */
    private $reportModel;

    public function __construct(EmailModel $emailModel, CampaignModel $campaignModel, ActionModel $actionModel, ListModel $listModel, TriggerEventModel $triggerEventModel, ReportModel $reportModel)
    {
        $this->emailModel        = $emailModel;
        $this->campaignModel     = $campaignModel;
        $this->actionModel       = $actionModel;
        $this->listModel         = $listModel;
        $this->triggerEventModel = $triggerEventModel;
        $this->reportModel       = $reportModel;
    }

    /**
     * @param $segmentId
     *
     * @return array
     */
    public function getChannelsIds($segmentId)
    {
        $usage   = [];
        $usage[] = [
            'label' => 'autoborna.email.emails',
            'route' => 'autoborna_email_index',
            'ids'   => $this->emailModel->getEmailsIdsWithDependenciesOnSegment($segmentId),
        ];

        $usage[] = [
            'label' => 'autoborna.campaign.campaigns',
            'route' => 'autoborna_campaign_index',
            'ids'   => $this->campaignModel->getCampaignIdsWithDependenciesOnSegment($segmentId),
        ];

        $usage[] = [
            'label' => 'autoborna.lead.lead.lists',
            'route' => 'autoborna_segment_index',
            'ids'   => $this->listModel->getSegmentsWithDependenciesOnSegment($segmentId, 'id'),
        ];

        $usage[] = [
            'label' => 'autoborna.report.reports',
            'route' => 'autoborna_report_index',
            'ids'   => $this->reportModel->getReportsIdsWithDependenciesOnSegment($segmentId),
        ];

        $usage[] = [
            'label' => 'autoborna.form.forms',
            'route' => 'autoborna_form_index',
            'ids'   => $this->actionModel->getFormsIdsWithDependenciesOnSegment($segmentId),
        ];

        $usage[] = [
            'label' => 'autoborna.point.trigger.header.index',
            'route' => 'autoborna_pointtrigger_index',
            'ids'   => $this->triggerEventModel->getReportIdsWithDependenciesOnSegment($segmentId),
        ];

        return $usage;
    }
}
