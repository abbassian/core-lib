<?php

namespace Autoborna\AssetBundle\EventListener;

use Autoborna\AssetBundle\Model\AssetModel;
use Autoborna\CoreBundle\CoreEvents;
use Autoborna\CoreBundle\Event as AutobornaEvents;
use Autoborna\CoreBundle\Helper\TemplatingHelper;
use Autoborna\CoreBundle\Helper\UserHelper;
use Autoborna\CoreBundle\Security\Permissions\CorePermissions;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SearchSubscriber implements EventSubscriberInterface
{
    /**
     * @var AssetModel
     */
    private $assetModel;

    /**
     * @var CorePermissions
     */
    private $security;

    /**
     * @var UserHelper
     */
    private $userHelper;

    /**
     * @var TemplatingHelper
     */
    private $templating;

    public function __construct(AssetModel $assetModel, CorePermissions $security, UserHelper $userHelper, TemplatingHelper $templating)
    {
        $this->assetModel = $assetModel;
        $this->security   = $security;
        $this->userHelper = $userHelper;
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
        $str = $event->getSearchString();
        if (empty($str)) {
            return;
        }

        $filter = ['string' => $str, 'force' => []];

        $permissions = $this->security->isGranted(
            ['asset:assets:viewown', 'asset:assets:viewother'],
            'RETURN_ARRAY'
        );
        if ($permissions['asset:assets:viewown'] || $permissions['asset:assets:viewother']) {
            if (!$permissions['asset:assets:viewother']) {
                $filter['force'][] = [
                    'column' => 'IDENTITY(a.createdBy)',
                    'expr'   => 'eq',
                    'value'  => $this->userHelper->getUser()->getId(),
                ];
            }

            $assets = $this->assetModel->getEntities(
                [
                    'limit'  => 5,
                    'filter' => $filter,
                ]);

            if (count($assets) > 0) {
                $assetResults = [];

                foreach ($assets as $asset) {
                    $assetResults[] = $this->templating->getTemplating()->renderResponse(
                        'AutobornaAssetBundle:SubscribedEvents\Search:global.html.php',
                        ['asset' => $asset]
                    )->getContent();
                }
                if (count($assets) > 5) {
                    $assetResults[] = $this->templating->getTemplating()->renderResponse(
                        'AutobornaAssetBundle:SubscribedEvents\Search:global.html.php',
                        [
                            'showMore'     => true,
                            'searchString' => $str,
                            'remaining'    => (count($assets) - 5),
                        ]
                    )->getContent();
                }
                $assetResults['count'] = count($assets);
                $event->addResults('autoborna.asset.assets', $assetResults);
            }
        }
    }

    public function onBuildCommandList(AutobornaEvents\CommandListEvent $event)
    {
        if ($this->security->isGranted(['asset:assets:viewown', 'asset:assets:viewother'], 'MATCH_ONE')) {
            $event->addCommands(
                'autoborna.asset.assets',
                $this->assetModel->getCommandList()
            );
        }
    }
}
