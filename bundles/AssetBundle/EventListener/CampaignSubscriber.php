<?php

namespace Autoborna\AssetBundle\EventListener;

use Autoborna\AssetBundle\AssetEvents;
use Autoborna\AssetBundle\Event\AssetLoadEvent;
use Autoborna\AssetBundle\Form\Type\CampaignEventAssetDownloadType;
use Autoborna\CampaignBundle\CampaignEvents;
use Autoborna\CampaignBundle\Event\CampaignBuilderEvent;
use Autoborna\CampaignBundle\Event\CampaignExecutionEvent;
use Autoborna\CampaignBundle\Executioner\RealTimeExecutioner;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CampaignSubscriber implements EventSubscriberInterface
{
    /**
     * @var RealTimeExecutioner
     */
    private $realTimeExecutioner;

    public function __construct(RealTimeExecutioner $realTimeExecutioner)
    {
        $this->realTimeExecutioner = $realTimeExecutioner;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            CampaignEvents::CAMPAIGN_ON_BUILD         => ['onCampaignBuild', 0],
            AssetEvents::ASSET_ON_LOAD                => ['onAssetDownload', 0],
            AssetEvents::ON_CAMPAIGN_TRIGGER_DECISION => ['onCampaignTriggerDecision', 0],
        ];
    }

    public function onCampaignBuild(CampaignBuilderEvent $event)
    {
        $trigger = [
            'label'          => 'autoborna.asset.campaign.event.download',
            'description'    => 'autoborna.asset.campaign.event.download_descr',
            'eventName'      => AssetEvents::ON_CAMPAIGN_TRIGGER_DECISION,
            'formType'       => CampaignEventAssetDownloadType::class,
            'channel'        => 'asset',
            'channelIdField' => 'assets',
        ];

        $event->addDecision('asset.download', $trigger);
    }

    /**
     * Trigger point actions for asset download.
     */
    public function onAssetDownload(AssetLoadEvent $event)
    {
        $asset = $event->getRecord()->getAsset();

        if (null !== $asset) {
            $this->realTimeExecutioner->execute('asset.download', $asset, 'asset', $asset->getId());
        }
    }

    public function onCampaignTriggerDecision(CampaignExecutionEvent $event)
    {
        $eventDetails = $event->getEventDetails();

        if (null == $eventDetails) {
            return $event->setResult(true);
        }

        $assetId       = $eventDetails->getId();
        $limitToAssets = $event->getConfig()['assets'];

        if (!empty($limitToAssets) && !in_array($assetId, $limitToAssets)) {
            //no points change
            return $event->setResult(false);
        }

        $event->setResult(true);
    }
}
