<?php

namespace Autoborna\EmailBundle\EventListener;

use Autoborna\CoreBundle\Helper\ArrayHelper;
use Autoborna\DashboardBundle\Event\WidgetDetailEvent;
use Autoborna\DashboardBundle\EventListener\DashboardSubscriber as MainDashboardSubscriber;
use Autoborna\EmailBundle\Form\Type\DashboardBestHoursWidgetType;
use Autoborna\EmailBundle\Model\EmailModel;

class DashboardBestHoursSubscriber extends MainDashboardSubscriber
{
    /**
     * Define the name of the bundle/category of the widget(s).
     *
     * @var string
     */
    protected $bundle = 'email';

    /**
     * Define the widget(s).
     *
     * @var string
     */
    protected $types = [
        'emails.best.hours' => [
            'formAlias' => DashboardBestHoursWidgetType::class,
        ],
    ];

    /**
     * Define permissions to see those widgets.
     *
     * @var array
     */
    protected $permissions = [
        'email:emails:viewown',
        'email:emails:viewother',
    ];

    /**
     * @var EmailModel
     */
    protected $emailModel;

    /**
     * DashboardSubscriber constructor.
     */
    public function __construct(EmailModel $emailModel)
    {
        $this->emailModel = $emailModel;
    }

    /**
     * Set a widget detail when needed.
     */
    public function onWidgetDetailGenerate(WidgetDetailEvent $event)
    {
        $this->checkPermissions($event);
        $canViewOthers = $event->hasPermission('email:emails:viewother');

        if ('emails.best.hours' == $event->getType()) {
            $widget     = $event->getWidget();
            $params     = $widget->getParams();
            $filterKeys = ['companyId', 'campaignId', 'segmentId'];

            if (!$event->isCached()) {
                $event->setTemplateData([
                    'chartType'   => 'bar',
                    'chartHeight' => $widget->getHeight() - 80,
                    'chartData'   => $this->emailModel->getBestHours(
                        'date_read',
                        $params['dateFrom'],
                        $params['dateTo'],
                        ArrayHelper::select($filterKeys, $params),
                        $canViewOthers,
                        $params['timeFormat']
                    ),
                ]);
            }

            $event->setTemplate('AutobornaCoreBundle:Helper:chart.html.php');
            $event->stopPropagation();
        }
    }
}
