<?php

namespace Autoborna\CampaignBundle\EventListener;

use Autoborna\CampaignBundle\Model\CampaignModel;
use Autoborna\CoreBundle\CoreEvents;
use Autoborna\CoreBundle\Event as AutobornaEvents;
use Autoborna\CoreBundle\Helper\TemplatingHelper;
use Autoborna\CoreBundle\Security\Permissions\CorePermissions;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SearchSubscriber implements EventSubscriberInterface
{
    /**
     * @var CampaignModel
     */
    private $campaignModel;

    /**
     * @var CorePermissions
     */
    private $security;

    /**
     * @var TemplatingHelper
     */
    private $templating;

    public function __construct(
        CampaignModel $campaignModel,
        CorePermissions $security,
        TemplatingHelper $templating
    ) {
        $this->campaignModel = $campaignModel;
        $this->security      = $security;
        $this->templating    = $templating;
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
        if ($this->security->isGranted('campaign:campaigns:view')) {
            $str = $event->getSearchString();
            if (empty($str)) {
                return;
            }

            $campaigns = $this->campaignModel->getEntities(
                [
                    'limit'  => 5,
                    'filter' => $str,
                ]);

            if (count($campaigns) > 0) {
                $campaignResults = [];
                foreach ($campaigns as $campaign) {
                    $campaignResults[] = $this->templating->getTemplating()->renderResponse(
                        'AutobornaCampaignBundle:SubscribedEvents\Search:global.html.php',
                        [
                            'campaign' => $campaign,
                        ]
                    )->getContent();
                }
                if (count($campaigns) > 5) {
                    $campaignResults[] = $this->templating->getTemplating()->renderResponse(
                        'AutobornaCampaignBundle:SubscribedEvents\Search:global.html.php',
                        [
                            'showMore'     => true,
                            'searchString' => $str,
                            'remaining'    => (count($campaigns) - 5),
                        ]
                    )->getContent();
                }
                $campaignResults['count'] = count($campaigns);
                $event->addResults('autoborna.campaign.campaigns', $campaignResults);
            }
        }
    }

    public function onBuildCommandList(AutobornaEvents\CommandListEvent $event)
    {
        $security = $this->security;
        if ($security->isGranted('campaign:campaigns:view')) {
            $event->addCommands(
                'autoborna.campaign.campaigns',
                $this->campaignModel->getCommandList()
            );
        }
    }
}
