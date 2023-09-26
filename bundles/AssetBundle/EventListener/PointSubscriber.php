<?php

namespace Autoborna\AssetBundle\EventListener;

use Autoborna\AssetBundle\AssetEvents;
use Autoborna\AssetBundle\Event\AssetLoadEvent;
use Autoborna\AssetBundle\Form\Type\PointActionAssetDownloadType;
use Autoborna\PointBundle\Event\PointBuilderEvent;
use Autoborna\PointBundle\Model\PointModel;
use Autoborna\PointBundle\PointEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PointSubscriber implements EventSubscriberInterface
{
    /**
     * @var PointModel
     */
    private $pointModel;

    public function __construct(PointModel $pointModel)
    {
        $this->pointModel = $pointModel;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            PointEvents::POINT_ON_BUILD => ['onPointBuild', 0],
            AssetEvents::ASSET_ON_LOAD  => ['onAssetDownload', 0],
        ];
    }

    public function onPointBuild(PointBuilderEvent $event)
    {
        $action = [
            'group'       => 'autoborna.asset.actions',
            'label'       => 'autoborna.asset.point.action.download',
            'description' => 'autoborna.asset.point.action.download_descr',
            'callback'    => ['\\Autoborna\\AssetBundle\\Helper\\PointActionHelper', 'validateAssetDownload'],
            'formType'    => PointActionAssetDownloadType::class,
        ];

        $event->addAction('asset.download', $action);
    }

    /**
     * Trigger point actions for asset download.
     */
    public function onAssetDownload(AssetLoadEvent $event)
    {
        $asset = $event->getRecord()->getAsset();

        if (null !== $asset) {
            $this->pointModel->triggerAction('asset.download', $asset);
        }
    }
}
