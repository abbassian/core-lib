<?php

namespace Autoborna\StageBundle\EventListener;

use Autoborna\CoreBundle\CoreEvents;
use Autoborna\CoreBundle\Event as AutobornaEvents;
use Autoborna\CoreBundle\Helper\TemplatingHelper;
use Autoborna\CoreBundle\Security\Permissions\CorePermissions;
use Autoborna\StageBundle\Model\StageModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SearchSubscriber implements EventSubscriberInterface
{
    /**
     * @var StageModel
     */
    private $stageModel;

    /**
     * @var CorePermissions
     */
    private $security;

    /**
     * @var TemplatingHelper
     */
    private $templating;

    public function __construct(
        StageModel $stageModel,
        CorePermissions $security,
        TemplatingHelper $templating
    ) {
        $this->stageModel = $stageModel;
        $this->security   = $security;
        $this->templating = $templating;
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
        if ($this->security->isGranted('stage:stages:view')) {
            $str = $event->getSearchString();
            if (empty($str)) {
                return;
            }

            $items = $this->stageModel->getEntities(
                [
                    'limit'  => 5,
                    'filter' => $str,
                ]);
            $stageCount = count($items);
            if ($stageCount > 0) {
                $stagesResults = [];
                $canEdit       = $this->security->isGranted('stage:stages:edit');
                foreach ($items as $item) {
                    $stagesResults[] = $this->templating->getTemplating()->renderResponse(
                        'AutobornaStageBundle:SubscribedEvents\Search:global.html.php',
                        [
                            'item'    => $item,
                            'canEdit' => $canEdit,
                        ]
                    )->getContent();
                }
                if ($stageCount > 5) {
                    $stagesResults[] = $this->templating->getTemplating()->renderResponse(
                        'AutobornaStageBundle:SubscribedEvents\Search:global.html.php',
                        [
                            'showMore'     => true,
                            'searchString' => $str,
                            'remaining'    => ($stageCount - 5),
                        ]
                    )->getContent();
                }
                $stagesResults['count'] = $stageCount;
                $event->addResults('autoborna.stage.actions.header.index', $stagesResults);
            }
        }
    }

    public function onBuildCommandList(AutobornaEvents\CommandListEvent $event)
    {
        if ($this->security->isGranted('stage:stages:view')) {
            $event->addCommands(
                'autoborna.stage.actions.header.index',
                $this->stageModel->getCommandList()
            );
        }
    }
}
