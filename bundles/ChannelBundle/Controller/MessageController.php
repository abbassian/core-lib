<?php

namespace Autoborna\ChannelBundle\Controller;

use Autoborna\ChannelBundle\Entity\Channel;
use Autoborna\ChannelBundle\Model\MessageModel;
use Autoborna\CoreBundle\Controller\AbstractStandardFormController;
use Autoborna\CoreBundle\Helper\Chart\LineChart;
use Autoborna\LeadBundle\Controller\EntityContactsTrait;
use Symfony\Component\Form\Form;

/**
 * Class MessageController.
 */
class MessageController extends AbstractStandardFormController
{
    use EntityContactsTrait;

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function batchDeleteAction()
    {
        return $this->batchDeleteStandard();
    }

    /**
     * @param $objectId
     *
     * @return \Autoborna\CoreBundle\Controller\Response|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function cloneAction($objectId)
    {
        return $this->cloneStandard($objectId);
    }

    /**
     * @param      $objectId
     * @param bool $ignorePost
     *
     * @return \Autoborna\CoreBundle\Controller\Response|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function editAction($objectId, $ignorePost = false)
    {
        return $this->editStandard($objectId, $ignorePost);
    }

    /**
     * @param int $page
     *
     * @return \Autoborna\CoreBundle\Controller\Response|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function indexAction($page = 1)
    {
        return $this->indexStandard($page);
    }

    /**
     * @return \Autoborna\CoreBundle\Controller\Response|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function newAction()
    {
        return $this->newStandard();
    }

    /**
     * @param $objectId
     *
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function viewAction($objectId)
    {
        return $this->viewStandard($objectId, 'message', 'channel');
    }

    /**
     * @param $args
     * @param $action
     *
     * @return mixed
     */
    protected function getViewArguments(array $args, $action)
    {
        /** @var MessageModel $model */
        $model          = $this->getModel($this->getModelName());
        $viewParameters = [];
        switch ($action) {
            case 'index':
                $viewParameters = [
                    'headerTitle' => $this->get('translator')->trans('autoborna.channel.messages'),
                    'listHeaders' => [
                        [
                            'text'  => 'autoborna.core.channels',
                            'class' => 'visible-md visible-lg',
                        ],
                    ],
                    'listItemTemplate'  => 'AutobornaChannelBundle:Message:list_item.html.php',
                    'enableCloneButton' => true,
                ];

                break;
            case 'view':
                $message = $args['viewParameters']['item'];

                // Init the date range filter form
                $returnUrl = $this->generateUrl(
                    'autoborna_message_action',
                    [
                        'objectAction' => 'view',
                        'objectId'     => $message->getId(),
                    ]
                );

                list($dateFrom, $dateTo) = $this->getViewDateRange($message->getId(), $returnUrl, 'local', $dateRangeForm);
                $chart                   = new LineChart(null, $dateFrom, $dateTo);

                /** @var Channel[] $channels */
                $channels        = $model->getChannels();
                $messageChannels = $message->getChannels();
                $chart->setDataset(
                    $this->get('translator')->trans('autoborna.core.all'),
                    $model->getLeadStatsPost($message->getId(), $dateFrom, $dateTo)
                );

                $messagedLeads = [
                    'all' => $this->forward(
                        'AutobornaChannelBundle:Message:contacts',
                        [
                            'objectId'   => $message->getId(),
                            'page'       => $this->get('session')->get('autoborna.'.$this->getSessionBase('all').'.contact.page', 1),
                            'ignoreAjax' => true,
                            'channel'    => 'all',
                        ]
                    )->getContent(),
                ];

                foreach ($messageChannels as $channel) {
                    if ($channel->isEnabled() && isset($channels[$channel->getChannel()])) {
                        $chart->setDataset(
                            $channels[$channel->getChannel()]['label'],
                            $model->getLeadStatsPost($message->getId(), $dateFrom, $dateTo, $channel->getChannel())
                        );

                        $messagedLeads[$channel->getChannel()] = $this->forward(
                            'AutobornaChannelBundle:Message:contacts',
                            [
                                'objectId' => $message->getId(),
                                'page'     => $this->get('session')->get(
                                    'autoborna.'.$this->getSessionBase($channel->getChannel()).'.contact.page',
                                    1
                                ),
                                'ignoreAjax' => true,
                                'channel'    => $channel->getChannel(),
                            ]
                        )->getContent();
                    }
                }

                $viewParameters = [
                    'channels'        => $channels,
                    'channelContents' => $model->getMessageChannels($message->getId()),
                    'dateRangeForm'   => $dateRangeForm->createView(),
                    'eventCounts'     => $chart->render(),
                    'messagedLeads'   => $messagedLeads,
                ];
                break;
            case 'new':
            case 'edit':
                $viewParameters = [
                    'channels' => $model->getChannels(),
                ];

                break;
        }

        $args['viewParameters'] = array_merge($args['viewParameters'], $viewParameters);

        return $args;
    }

    /**
     * @param $objectId
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function deleteAction($objectId)
    {
        return $this->deleteStandard($objectId);
    }

    /**
     * {@inheritdoc}
     */
    protected function getControllerBase()
    {
        return 'AutobornaChannelBundle:Message';
    }

    /**
     * @param $view
     *
     * @return \Symfony\Component\Form\FormView
     */
    protected function getFormView(Form $form, $view)
    {
        $themes = ['AutobornaChannelBundle:FormTheme'];
        /** @var MessageModel $model */
        $model    = $this->getModel($this->getModelName());
        $channels = $model->getChannels();
        foreach ($channels as $channel) {
            if (isset($channel['formTheme'])) {
                $themes[] = $channel['formTheme'];
            }
        }

        return $this->setFormTheme($form, 'AutobornaChannelBundle:Message:form.html.php', $themes);
    }

    /**
     * {@inheritdoc}
     */
    protected function getJsLoadMethodPrefix()
    {
        return 'messages';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelName()
    {
        return 'channel.message';
    }

    /**
     * {@inheritdoc}
     */
    protected function getRouteBase()
    {
        return 'message';
    }

    /***
     * @param null $objectId
     *
     * @return string
     */
    protected function getSessionBase($objectId = null)
    {
        return 'message'.(($objectId) ? '.'.$objectId : '');
    }

    /**
     * {@inheritdoc}
     */
    protected function getTranslationBase()
    {
        return 'autoborna.channel.message';
    }

    /**
     * @param     $objectId
     * @param int $page
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function contactsAction($objectId, $channel, $page = 1)
    {
        $filter = [];
        if ('all' !== $channel) {
            $returnUrl = $this->generateUrl(
                'autoborna_message_action',
                [
                    'objectAction' => 'view',
                    'objectId'     => $objectId,
                ]
            );
            list($dateFrom, $dateTo) = $this->getViewDateRange($objectId, $returnUrl, 'UTC');

            $filter = [
                'channel' => $channel,
                [
                    'col'  => 'entity.date_triggered',
                    'expr' => 'between',
                    'val'  => [
                        $dateFrom->format('Y-m-d H:i:s'),
                        $dateTo->format('Y-m-d H:i:s'),
                    ],
                ],
            ];
        }

        return $this->generateContactsGrid(
            $objectId,
            $page,
            'channel:messages:view',
            'message.'.$channel,
            'campaign_lead_event_log',
            $channel,
            null,
            $filter,
            [
                [
                    'type'       => 'join',
                    'from_alias' => 'entity',
                    'table'      => 'campaign_events',
                    'alias'      => 'event',
                    'condition'  => "entity.event_id = event.id and event.channel = 'channel.message' and event.channel_id = ".(int) $objectId,
                ],
            ],
            null,
            [
                'channel' => ($channel) ? $channel : 'all',
            ],
            '.message-'.$channel
        );
    }
}
