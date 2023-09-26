<?php

namespace Autoborna\EmailBundle\EventListener;

use Autoborna\CoreBundle\Helper\ArrayHelper;
use Autoborna\DashboardBundle\Entity\Widget;
use Autoborna\DashboardBundle\Event\WidgetDetailEvent;
use Autoborna\DashboardBundle\EventListener\DashboardSubscriber as MainDashboardSubscriber;
use Autoborna\EmailBundle\Form\Type\DashboardEmailsInTimeWidgetType;
use Autoborna\EmailBundle\Form\Type\DashboardMostHitEmailRedirectsWidgetType;
use Autoborna\EmailBundle\Form\Type\DashboardSentEmailToContactsWidgetType;
use Autoborna\EmailBundle\Model\EmailModel;
use Symfony\Component\Routing\RouterInterface;

class DashboardSubscriber extends MainDashboardSubscriber
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
        'emails.in.time' => [
            'formAlias' => DashboardEmailsInTimeWidgetType::class,
        ],
        'sent.email.to.contacts' => [
            'formAlias' => DashboardSentEmailToContactsWidgetType::class,
        ],
        'most.hit.email.redirects' => [
            'formAlias' => DashboardMostHitEmailRedirectsWidgetType::class,
        ],
        'ignored.vs.read.emails'   => [],
        'upcoming.emails'          => [],
        'most.sent.emails'         => [],
        'most.read.emails'         => [],
        'created.emails'           => [],
        'device.granularity.email' => [],
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
     * @var RouterInterface
     */
    private $router;

    public function __construct(EmailModel $emailModel, RouterInterface $router)
    {
        $this->emailModel = $emailModel;
        $this->router     = $router;
    }

    /**
     * Set a widget detail when needed.
     */
    public function onWidgetDetailGenerate(WidgetDetailEvent $event)
    {
        $this->checkPermissions($event);
        $canViewOthers = $event->hasPermission('email:emails:viewother');
        $defaultLimit  = $this->getDefaultLimit($event->getWidget());

        if ('emails.in.time' == $event->getType()) {
            $widget     = $event->getWidget();
            $params     = $widget->getParams();
            $filterKeys = ['flag', 'dataset', 'companyId', 'campaignId', 'segmentId'];

            if (!$event->isCached()) {
                $event->setTemplateData([
                    'chartType'   => 'line',
                    'chartHeight' => $widget->getHeight() - 80,
                    'chartData'   => $this->emailModel->getEmailsLineChartData(
                        $params['timeUnit'],
                        $params['dateFrom'],
                        $params['dateTo'],
                        $params['dateFormat'],
                        ArrayHelper::select($filterKeys, $params),
                        $canViewOthers
                    ),
                ]);
            }

            $event->setTemplate('AutobornaCoreBundle:Helper:chart.html.php');
            $event->stopPropagation();
        }

        if ('sent.email.to.contacts' == $event->getType()) {
            $widget = $event->getWidget();
            $params = $widget->getParams();

            if (!$event->isCached()) {
                $headItems  = [
                    'autoborna.dashboard.label.contact.id',
                    'autoborna.dashboard.label.contact.email.address',
                    'autoborna.dashboard.label.contact.open',
                    'autoborna.dashboard.label.contact.click',
                    'autoborna.dashboard.label.contact.links.clicked',
                    'autoborna.dashboard.label.email.id',
                    'autoborna.dashboard.label.email.name',
                    'autoborna.dashboard.label.segment.id',
                    'autoborna.dashboard.label.segment.name',
                    'autoborna.dashboard.label.company.id',
                    'autoborna.dashboard.label.company.name',
                    'autoborna.dashboard.label.campaign.id',
                    'autoborna.dashboard.label.campaign.name',
                    'autoborna.dashboard.label.date.sent',
                    'autoborna.dashboard.label.date.read',
                ];

                $event->setTemplateData(
                    [
                        'headItems' => $headItems,
                        'bodyItems' => $this->emailModel->getSentEmailToContactData(
                            ArrayHelper::getValue('limit', $params, $defaultLimit),
                            $params['dateFrom'],
                            $params['dateTo'],
                            ['groupBy' => 'sends', 'canViewOthers' => $canViewOthers],
                            ArrayHelper::getValue('companyId', $params),
                            ArrayHelper::getValue('campaignId', $params),
                            ArrayHelper::getValue('segmentId', $params)
                        ),
                    ]
                );
            }

            $event->setTemplate('AutobornaEmailBundle:SubscribedEvents:Dashboard/Sent.email.to.contacts.html.php');
            $event->stopPropagation();
        }

        if ('most.hit.email.redirects' == $event->getType()) {
            $widget = $event->getWidget();
            $params = $widget->getParams();

            if (!$event->isCached()) {
                $event->setTemplateData([
                    'headItems' => [
                        'autoborna.dashboard.label.url',
                        'autoborna.dashboard.label.unique.hit.count',
                        'autoborna.dashboard.label.total.hit.count',
                        'autoborna.dashboard.label.email.id',
                        'autoborna.dashboard.label.email.name',
                    ],
                    'bodyItems' => $this->emailModel->getMostHitEmailRedirects(
                        ArrayHelper::getValue('limit', $params, $defaultLimit),
                        $params['dateFrom'],
                        $params['dateTo'],
                        ['groupBy' => 'sends', 'canViewOthers' => $canViewOthers],
                        ArrayHelper::getValue('companyId', $params),
                        ArrayHelper::getValue('campaignId', $params),
                        ArrayHelper::getValue('segmentId', $params)
                    ),
                ]);
            }

            $event->setTemplate('AutobornaEmailBundle:SubscribedEvents:Dashboard/Most.hit.email.redirects.html.php');
            $event->stopPropagation();
        }

        if ('ignored.vs.read.emails' == $event->getType()) {
            $widget = $event->getWidget();
            $params = $widget->getParams();

            if (!$event->isCached()) {
                $event->setTemplateData([
                    'chartType'   => 'pie',
                    'chartHeight' => $widget->getHeight() - 80,
                    'chartData'   => $this->emailModel->getIgnoredVsReadPieChartData($params['dateFrom'], $params['dateTo'], [], $canViewOthers),
                ]);
            }

            $event->setTemplate('AutobornaCoreBundle:Helper:chart.html.php');
            $event->stopPropagation();
        }

        if ('upcoming.emails' == $event->getType()) {
            $widget = $event->getWidget();
            $params = $widget->getParams();
            $height = $widget->getHeight();
            $limit  = round(($height - 80) / 60);

            $upcomingEmails = $this->emailModel->getUpcomingEmails($limit, $canViewOthers);

            $event->setTemplate('AutobornaDashboardBundle:Dashboard:upcomingemails.html.php');
            $event->setTemplateData(['upcomingEmails' => $upcomingEmails]);
            $event->stopPropagation();
        }

        if ('most.sent.emails' == $event->getType()) {
            if (!$event->isCached()) {
                $params = $event->getWidget()->getParams();
                $emails = $this->emailModel->getEmailStatList(
                    ArrayHelper::getValue('limit', $params, $defaultLimit),
                    $params['dateFrom'],
                    $params['dateTo'],
                    [],
                    ['groupBy' => 'sends', 'canViewOthers' => $canViewOthers]
                );
                $items = [];

                // Build table rows with links
                if ($emails) {
                    foreach ($emails as &$email) {
                        $emailUrl = $this->router->generate('autoborna_email_action', ['objectAction' => 'view', 'objectId' => $email['id']]);
                        $row      = [
                            [
                                'value' => $email['name'],
                                'type'  => 'link',
                                'link'  => $emailUrl,
                            ],
                            [
                                'value' => $email['count'],
                            ],
                        ];
                        $items[] = $row;
                    }
                }

                $event->setTemplateData([
                    'headItems' => [
                        'autoborna.dashboard.label.title',
                        'autoborna.email.label.sends',
                    ],
                    'bodyItems' => $items,
                    'raw'       => $emails,
                ]);
            }

            $event->setTemplate('AutobornaCoreBundle:Helper:table.html.php');
            $event->stopPropagation();
        }

        if ('most.read.emails' == $event->getType()) {
            if (!$event->isCached()) {
                $params = $event->getWidget()->getParams();
                $emails = $this->emailModel->getEmailStatList(
                    ArrayHelper::getValue('limit', $params, $defaultLimit),
                    $params['dateFrom'],
                    $params['dateTo'],
                    [],
                    ['groupBy' => 'reads', 'canViewOthers' => $canViewOthers]
                );
                $items = [];

                // Build table rows with links
                if ($emails) {
                    foreach ($emails as &$email) {
                        $emailUrl = $this->router->generate('autoborna_email_action', ['objectAction' => 'view', 'objectId' => $email['id']]);
                        $row      = [
                            [
                                'value' => $email['name'],
                                'type'  => 'link',
                                'link'  => $emailUrl,
                            ],
                            [
                                'value' => $email['count'],
                            ],
                        ];
                        $items[] = $row;
                    }
                }

                $event->setTemplateData([
                    'headItems' => [
                        'autoborna.dashboard.label.title',
                        'autoborna.email.label.reads',
                    ],
                    'bodyItems' => $items,
                    'raw'       => $emails,
                ]);
            }

            $event->setTemplate('AutobornaCoreBundle:Helper:table.html.php');
            $event->stopPropagation();
        }

        if ('created.emails' == $event->getType()) {
            if (!$event->isCached()) {
                $params = $event->getWidget()->getParams();
                $emails = $this->emailModel->getEmailList(
                    ArrayHelper::getValue('limit', $params, $defaultLimit),
                    $params['dateFrom'],
                    $params['dateTo'],
                    [],
                    ['groupBy' => 'creations', 'canViewOthers' => $canViewOthers]
                );
                $items = [];

                // Build table rows with links
                if ($emails) {
                    foreach ($emails as &$email) {
                        $emailUrl = $this->router->generate(
                            'autoborna_email_action',
                            [
                                'objectAction' => 'view',
                                'objectId'     => $email['id'],
                            ]
                        );
                        $row = [
                            [
                                'value' => $email['name'],
                                'type'  => 'link',
                                'link'  => $emailUrl,
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
                    'raw'       => $emails,
                ]);
            }

            $event->setTemplate('AutobornaCoreBundle:Helper:table.html.php');
            $event->stopPropagation();
        }
        if ('device.granularity.email' == $event->getType()) {
            $widget = $event->getWidget();
            $params = $widget->getParams();

            if (!$event->isCached()) {
                $event->setTemplateData([
                    'chartType'   => 'pie',
                    'chartHeight' => $widget->getHeight() - 80,
                    'chartData'   => $this->emailModel->getDeviceGranularityPieChartData(
                        $params['dateFrom'],
                        $params['dateTo'],
                        $canViewOthers
                    ),
                ]);
            }

            $event->setTemplate('AutobornaCoreBundle:Helper:chart.html.php');
            $event->stopPropagation();
        }
    }

    /**
     * Count the row limit from the widget height.
     *
     * @return int
     */
    private function getDefaultLimit(Widget $widget)
    {
        return round((($widget->getHeight() - 80) / 35) - 1);
    }
}
