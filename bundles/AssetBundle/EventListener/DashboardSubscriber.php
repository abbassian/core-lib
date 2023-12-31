<?php

namespace Autoborna\AssetBundle\EventListener;

use Autoborna\AssetBundle\Model\AssetModel;
use Autoborna\DashboardBundle\Event\WidgetDetailEvent;
use Autoborna\DashboardBundle\EventListener\DashboardSubscriber as MainDashboardSubscriber;
use Symfony\Component\Routing\RouterInterface;

class DashboardSubscriber extends MainDashboardSubscriber
{
    /**
     * Define the name of the bundle/category of the widget(s).
     *
     * @var string
     */
    protected $bundle = 'asset';

    /**
     * Define the widget(s).
     *
     * @var array
     */
    protected $types = [
        'asset.downloads.in.time'        => [],
        'unique.vs.repetitive.downloads' => [],
        'popular.assets'                 => [],
        'created.assets'                 => [],
    ];

    /**
     * Define permissions to see those widgets.
     *
     * @var array
     */
    protected $permissions = [
        'asset:assets:viewown',
        'asset:assets:viewother',
    ];

    /**
     * @var AssetModel
     */
    protected $assetModel;

    /**
     * @var RouterInterface
     */
    protected $router;

    public function __construct(AssetModel $assetModel, RouterInterface $router)
    {
        $this->assetModel = $assetModel;
        $this->router     = $router;
    }

    /**
     * Set a widget detail when needed.
     */
    public function onWidgetDetailGenerate(WidgetDetailEvent $event)
    {
        $this->checkPermissions($event);
        $canViewOthers = $event->hasPermission('asset:assets:viewother');

        if ('asset.downloads.in.time' == $event->getType()) {
            $widget = $event->getWidget();
            $params = $widget->getParams();

            if (!$event->isCached()) {
                $event->setTemplateData([
                    'chartType'   => 'line',
                    'chartHeight' => $widget->getHeight() - 80,
                    'chartData'   => $this->assetModel->getDownloadsLineChartData(
                        $params['timeUnit'],
                        $params['dateFrom'],
                        $params['dateTo'],
                        $params['dateFormat'],
                        $canViewOthers
                    ),
                ]);
            }

            $event->setTemplate('AutobornaCoreBundle:Helper:chart.html.php');
            $event->stopPropagation();
        }

        if ('unique.vs.repetitive.downloads' == $event->getType()) {
            if (!$event->isCached()) {
                $params = $event->getWidget()->getParams();
                $event->setTemplateData([
                    'chartType'   => 'pie',
                    'chartHeight' => $event->getWidget()->getHeight() - 80,
                    'chartData'   => $this->assetModel->getUniqueVsRepetitivePieChartData($params['dateFrom'], $params['dateTo'], $canViewOthers),
                ]);
            }

            $event->setTemplate('AutobornaCoreBundle:Helper:chart.html.php');
            $event->stopPropagation();
        }

        if ('popular.assets' == $event->getType()) {
            if (!$event->isCached()) {
                $params = $event->getWidget()->getParams();

                if (empty($params['limit'])) {
                    // Count the pages limit from the widget height
                    $limit = round((($event->getWidget()->getHeight() - 80) / 35) - 1);
                } else {
                    $limit = $params['limit'];
                }

                $assets = $this->assetModel->getPopularAssets($limit, $params['dateFrom'], $params['dateTo'], $canViewOthers);
                $items  = [];

                // Build table rows with links
                if ($assets) {
                    foreach ($assets as &$asset) {
                        $assetUrl = $this->router->generate('autoborna_asset_action', ['objectAction' => 'view', 'objectId' => $asset['id']]);
                        $row      = [
                            [
                                'value' => $asset['title'],
                                'type'  => 'link',
                                'link'  => $assetUrl,
                            ],
                            [
                                'value' => $asset['download_count'],
                            ],
                        ];
                        $items[] = $row;
                    }
                }

                $event->setTemplateData([
                    'headItems' => [
                        'autoborna.dashboard.label.title',
                        'autoborna.dashboard.label.downloads',
                    ],
                    'bodyItems' => $items,
                    'raw'       => $assets,
                ]);
            }

            $event->setTemplate('AutobornaCoreBundle:Helper:table.html.php');
            $event->stopPropagation();
        }

        if ('created.assets' == $event->getType()) {
            if (!$event->isCached()) {
                $params = $event->getWidget()->getParams();

                if (empty($params['limit'])) {
                    // Count the assets limit from the widget height
                    $limit = round((($event->getWidget()->getHeight() - 80) / 35) - 1);
                } else {
                    $limit = $params['limit'];
                }

                $assets = $this->assetModel->getAssetList($limit, $params['dateFrom'], $params['dateTo'], [], ['canViewOthers' => $canViewOthers]);
                $items  = [];

                // Build table rows with links
                if ($assets) {
                    foreach ($assets as &$asset) {
                        $assetUrl = $this->router->generate('autoborna_asset_action', ['objectAction' => 'view', 'objectId' => $asset['id']]);
                        $row      = [
                            [
                                'value' => $asset['name'],
                                'type'  => 'link',
                                'link'  => $assetUrl,
                            ],
                        ];
                        $items[] = $row;
                    }
                }

                $event->setTemplateData([
                    'headItems' => [
                        'autoborna.dashboard.label.title',
                    ],
                    'bodyItems' => $items,
                    'raw'       => $assets,
                ]);
            }

            $event->setTemplate('AutobornaCoreBundle:Helper:table.html.php');
            $event->stopPropagation();
        }
    }
}
