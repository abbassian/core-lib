<?php

namespace Autoborna\FormBundle\EventListener;

use Autoborna\DashboardBundle\Event\WidgetDetailEvent;
use Autoborna\DashboardBundle\EventListener\DashboardSubscriber as MainDashboardSubscriber;
use Autoborna\FormBundle\Model\FormModel;
use Autoborna\FormBundle\Model\SubmissionModel;
use Symfony\Component\Routing\RouterInterface;

class DashboardSubscriber extends MainDashboardSubscriber
{
    /**
     * Define the name of the bundle/category of the widget(s).
     *
     * @var string
     */
    protected $bundle = 'form';

    /**
     * Define the widget(s).
     *
     * @var string
     */
    protected $types = [
        'submissions.in.time'      => [],
        'top.submission.referrers' => [],
        'top.submitters'           => [],
        'created.forms'            => [],
    ];

    /**
     * Define permissions to see those widgets.
     *
     * @var array
     */
    protected $permissions = [
        'form:forms:viewown',
        'form:forms:viewother',
    ];

    /**
     * @var SubmissionModel
     */
    protected $formSubmissionModel;

    /**
     * @var FormModel
     */
    protected $formModel;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(SubmissionModel $formSubmissionModel, FormModel $formModel, RouterInterface $router)
    {
        $this->formModel           = $formModel;
        $this->formSubmissionModel = $formSubmissionModel;
        $this->router              = $router;
    }

    /**
     * Set a widget detail when needed.
     */
    public function onWidgetDetailGenerate(WidgetDetailEvent $event)
    {
        $this->checkPermissions($event);
        $canViewOthers = $event->hasPermission('form:forms:viewother');

        if ('submissions.in.time' == $event->getType()) {
            $widget = $event->getWidget();
            $params = $widget->getParams();

            if (!$event->isCached()) {
                $event->setTemplateData([
                    'chartType'   => 'line',
                    'chartHeight' => $widget->getHeight() - 80,
                    'chartData'   => $this->formSubmissionModel->getSubmissionsLineChartData(
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

        if ('top.submission.referrers' == $event->getType()) {
            if (!$event->isCached()) {
                $params = $event->getWidget()->getParams();

                if (empty($params['limit'])) {
                    // Count the pages limit from the widget height
                    $limit = round((($event->getWidget()->getHeight() - 80) / 35) - 1);
                } else {
                    $limit = $params['limit'];
                }

                $referrers = $this->formSubmissionModel->getTopSubmissionReferrers($limit, $params['dateFrom'], $params['dateTo'], $canViewOthers);
                $items     = [];

                // Build table rows with links
                if ($referrers) {
                    foreach ($referrers as &$referrer) {
                        $row = [
                            [
                                'value'    => $referrer['referer'],
                                'type'     => 'link',
                                'external' => true,
                                'link'     => $referrer['referer'],
                            ],
                            [
                                'value' => $referrer['submissions'],
                            ],
                        ];
                        $items[] = $row;
                    }
                }

                $event->setTemplateData([
                    'headItems' => [
                        'autoborna.form.result.thead.referrer',
                        'autoborna.form.graph.line.submissions',
                    ],
                    'bodyItems' => $items,
                    'raw'       => $referrers,
                ]);
            }

            $event->setTemplate('AutobornaCoreBundle:Helper:table.html.php');
            $event->stopPropagation();
        }

        if ('top.submitters' == $event->getType()) {
            if (!$event->isCached()) {
                $params = $event->getWidget()->getParams();

                if (empty($params['limit'])) {
                    // Count the pages limit from the widget height
                    $limit = round((($event->getWidget()->getHeight() - 80) / 35) - 1);
                } else {
                    $limit = $params['limit'];
                }

                $submitters = $this->formSubmissionModel->getTopSubmitters($limit, $params['dateFrom'], $params['dateTo'], $canViewOthers);
                $items      = [];

                // Build table rows with links
                if ($submitters) {
                    foreach ($submitters as &$submitter) {
                        $name    = $submitter['lead_id'];
                        $leadUrl = $this->router->generate('autoborna_contact_action', ['objectAction' => 'view', 'objectId' => $submitter['lead_id']]);
                        if ($submitter['firstname'] || $submitter['lastname']) {
                            $name = trim($submitter['firstname'].' '.$submitter['lastname']);
                        } elseif ($submitter['email']) {
                            $name = $submitter['email'];
                        }

                        $row = [
                            [
                                'value' => $name,
                                'type'  => 'link',
                                'link'  => $leadUrl,
                            ],
                            [
                                'value' => $submitter['submissions'],
                            ],
                        ];
                        $items[] = $row;
                    }
                }

                $event->setTemplateData([
                    'headItems' => [
                        'autoborna.form.lead',
                        'autoborna.form.graph.line.submissions',
                    ],
                    'bodyItems' => $items,
                    'raw'       => $submitters,
                ]);
            }

            $event->setTemplate('AutobornaCoreBundle:Helper:table.html.php');
            $event->stopPropagation();
        }

        if ('created.forms' == $event->getType()) {
            if (!$event->isCached()) {
                $params = $event->getWidget()->getParams();

                if (empty($params['limit'])) {
                    // Count the forms limit from the widget height
                    $limit = round((($event->getWidget()->getHeight() - 80) / 35) - 1);
                } else {
                    $limit = $params['limit'];
                }

                $forms = $this->formModel->getFormList($limit, $params['dateFrom'], $params['dateTo'], [], ['canViewOthers' => true]);
                $items = [];

                // Build table rows with links
                if ($forms) {
                    foreach ($forms as &$form) {
                        $formUrl = $this->router->generate('autoborna_form_action', ['objectAction' => 'view', 'objectId' => $form['id']]);
                        $row     = [
                            [
                                'value' => $form['name'],
                                'type'  => 'link',
                                'link'  => $formUrl,
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
                    'raw'       => $forms,
                ]);
            }

            $event->setTemplate('AutobornaCoreBundle:Helper:table.html.php');
            $event->stopPropagation();
        }
    }
}
