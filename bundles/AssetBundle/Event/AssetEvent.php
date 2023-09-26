<?php

namespace Autoborna\AssetBundle\Event;

use Autoborna\AssetBundle\Entity\Asset;
use Autoborna\CoreBundle\Event\CommonEvent;

/**
 * Class AssetEvent.
 */
class AssetEvent extends CommonEvent
{
    /**
     * @param bool $isNew
     */
    public function __construct(Asset $asset, $isNew = false)
    {
        $this->entity = $asset;
        $this->isNew  = $isNew;
    }

    /**
     * Returns the Asset entity.
     *
     * @return Asset
     */
    public function getAsset()
    {
        return $this->entity;
    }

    /**
     * Sets the Asset entity.
     */
    public function setAsset(Asset $asset)
    {
        $this->entity = $asset;
    }
}
