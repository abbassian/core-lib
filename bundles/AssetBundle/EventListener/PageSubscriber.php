<?php

namespace Autoborna\AssetBundle\EventListener;

use Autoborna\AssetBundle\AssetEvents;
use Autoborna\PageBundle\Event\PageBuilderEvent;
use Autoborna\PageBundle\PageEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PageSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            PageEvents::PAGE_ON_BUILD => ['OnPageBuild', 0],
        ];
    }

    /**
     * Add forms to available page tokens.
     */
    public function onPageBuild(PageBuilderEvent $event)
    {
        if ($event->abTestWinnerCriteriaRequested()) {
            //add AB Test Winner Criteria
            $assetDownloads = [
                'group'    => 'autoborna.asset.abtest.criteria',
                'label'    => 'autoborna.asset.abtest.criteria.downloads',
                'event'    => AssetEvents::ON_DETERMINE_DOWNLOAD_RATE_WINNER,
            ];
            $event->addAbTestWinnerCriteria('asset.downloads', $assetDownloads);
        }
    }
}
