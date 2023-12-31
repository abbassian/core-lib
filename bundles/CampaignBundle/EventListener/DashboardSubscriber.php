<?php

namespace Autoborna\CampaignBundle\EventListener;

use Autoborna\CampaignBundle\Model\CampaignModel;
use Autoborna\CampaignBundle\Model\EventModel;
use Autoborna\DashboardBundle\Event\WidgetDetailEvent;
use Autoborna\DashboardBundle\EventListener\DashboardSubscriber as MainDashboardSubscriber;

class DashboardSubscriber extends MainDashboardSubscriber
{
    /**
     * Define the name of the bundle/category of the widget(s).
     *
     * @var string
     */
    protected $bundle = 'campaign';

    /**
     * Define the widget(s).
     *
     * @var string
     */
    protected $types = [
        'events.in.time'      => [],
        'leads.added.in.time' => [],
    ];

    /**
     * Define permissions to see those widgets.
     *
     * @var array
     */
    protected $permissions = [
        'campaign:campaigns:viewown',
        'campaign:campaigns:viewother',
    ];

    /**
     * @var EventModel
     */
    protected $campaignEventModel;

    /**
     * @var CampaignModel
     */
    protected $campaignModel;

    public function __construct(CampaignModel $campaignModel, EventModel $campaignEventModel)
    {
        $this->campaignModel      = $campaignModel;
        $this->campaignEventModel = $campaignEventModel;
    }

    /**
     * Set a widget detail when needed.
     */
    public function onWidgetDetailGenerate(WidgetDetailEvent $event)
    {
        $this->checkPermissions($event);
        $canViewOthers = $event->hasPermission('campaign:campaigns:viewother');

        if ('events.in.time' == $event->getType()) {
            $widget = $event->getWidget();
            $params = $widget->getParams();

            if (!$event->isCached()) {
                $event->setTemplateData([
                    'chartType'   => 'line',
                    'chartHeight' => $widget->getHeight() - 80,
                    'chartData'   => $this->campaignEventModel->getEventLineChartData(
                        $params['timeUnit'],
                        $params['dateFrom'],
                        $params['dateTo'],
                        $params['dateFormat'],
                        [],
                        $canViewOthers
                    ),
                ]);
            }

            $event->setTemplate('AutobornaCoreBundle:Helper:chart.html.php');
            $event->stopPropagation();
        }

        if ('leads.added.in.time' == $event->getType()) {
            $widget = $event->getWidget();
            $params = $widget->getParams();

            if (!$event->isCached()) {
                $event->setTemplateData([
                    'chartType'   => 'line',
                    'chartHeight' => $widget->getHeight() - 80,
                    'chartData'   => $this->campaignModel->getLeadsAddedLineChartData(
                        $params['timeUnit'],
                        $params['dateFrom'],
                        $params['dateTo'],
                        $params['dateFormat'],
                        [],
                        $canViewOthers
                    ),
                ]);
            }

            $event->setTemplate('AutobornaCoreBundle:Helper:chart.html.php');
            $event->stopPropagation();
        }
    }
}
