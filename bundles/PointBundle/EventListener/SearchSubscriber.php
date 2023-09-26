<?php

namespace Autoborna\PointBundle\EventListener;

use Autoborna\CoreBundle\CoreEvents;
use Autoborna\CoreBundle\Event as AutobornaEvents;
use Autoborna\CoreBundle\Helper\TemplatingHelper;
use Autoborna\CoreBundle\Security\Permissions\CorePermissions;
use Autoborna\PointBundle\Model\PointModel;
use Autoborna\PointBundle\Model\TriggerModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SearchSubscriber implements EventSubscriberInterface
{
    /**
     * @var PointModel
     */
    private $pointModel;

    /**
     * @var TriggerModel
     */
    private $pointTriggerModel;

    /**
     * @var CorePermissions
     */
    private $security;

    /**
     * @var TemplatingHelper
     */
    private $templating;

    public function __construct(
        PointModel $pointModel,
        TriggerModel $pointTriggerModel,
        CorePermissions $security,
        TemplatingHelper $templating
    ) {
        $this->pointModel        = $pointModel;
        $this->pointTriggerModel = $pointTriggerModel;
        $this->security          = $security;
        $this->templating        = $templating;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            CoreEvents::GLOBAL_SEARCH      => ['onGlobalSearch', 0],
            CoreEvents::BUILD_COMMAND_LIST => ['onBuildCommandList', 0],
        ];
    }

    public function onGlobalSearch(AutobornaEvents\GlobalSearchEvent $event)
    {
        if ($this->security->isGranted('point:points:view')) {
            $str = $event->getSearchString();
            if (empty($str)) {
                return;
            }

            $items = $this->pointModel->getEntities(
                [
                    'limit'  => 5,
                    'filter' => $str,
                ]);
            $pointCount = count($items);
            if ($pointCount > 0) {
                $pointsResults = [];
                $canEdit       = $this->security->isGranted('point:points:edit');
                foreach ($items as $item) {
                    $pointsResults[] = $this->templating->getTemplating()->renderResponse(
                        'AutobornaPointBundle:SubscribedEvents\Search:global_point.html.php',
                        [
                            'item'    => $item,
                            'canEdit' => $canEdit,
                        ]
                    )->getContent();
                }
                if ($pointCount > 5) {
                    $pointsResults[] = $this->templating->getTemplating()->renderResponse(
                        'AutobornaPointBundle:SubscribedEvents\Search:global_point.html.php',
                        [
                            'showMore'     => true,
                            'searchString' => $str,
                            'remaining'    => ($pointCount - 5),
                        ]
                    )->getContent();
                }
                $pointsResults['count'] = $pointCount;
                $event->addResults('autoborna.point.actions.header.index', $pointsResults);
            }
        }

        if ($this->security->isGranted('point:triggers:view')) {
            $str = $event->getSearchString();
            if (empty($str)) {
                return;
            }

            $items = $this->pointTriggerModel->getEntities(
                [
                    'limit'  => 5,
                    'filter' => $str,
                ]);
            $count = count($items);
            if ($count > 0) {
                $results = [];
                $canEdit = $this->security->isGranted('point:triggers:edit');
                foreach ($items as $item) {
                    $results[] = $this->templating->getTemplating()->renderResponse(
                        'AutobornaPointBundle:SubscribedEvents\Search:global_trigger.html.php',
                        [
                            'item'    => $item,
                            'canEdit' => $canEdit,
                        ]
                    )->getContent();
                }
                if ($count > 5) {
                    $results[] = $this->templating->getTemplating()->renderResponse(
                        'AutobornaPointBundle:SubscribedEvents\Search:global_trigger.html.php',
                        [
                            'showMore'     => true,
                            'searchString' => $str,
                            'remaining'    => ($count - 5),
                        ]
                    )->getContent();
                }
                $results['count'] = $count;
                $event->addResults('autoborna.point.trigger.header.index', $results);
            }
        }
    }

    public function onBuildCommandList(AutobornaEvents\CommandListEvent $event)
    {
        $security = $this->security;
        if ($security->isGranted('point:points:view')) {
            $event->addCommands(
                'autoborna.point.actions.header.index',
                $this->pointModel->getCommandList()
            );
        }
    }
}
