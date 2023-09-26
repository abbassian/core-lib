<?php

namespace Autoborna\AssetBundle\Event;

use Gaufrette\Adapter;
use Autoborna\CoreBundle\Event\CommonEvent;
use Autoborna\PluginBundle\Integration\AbstractIntegration;
use Autoborna\PluginBundle\Integration\UnifiedIntegrationInterface;

/**
 * Class RemoteAssetBrowseEvent.
 */
class RemoteAssetBrowseEvent extends CommonEvent
{
    /**
     * @var Adapter
     */
    private $adapter;

    /**
     * @var AbstractIntegration
     */
    private $integration;

    public function __construct(UnifiedIntegrationInterface $integration)
    {
        $this->integration = $integration;
    }

    /**
     * @return Adapter
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @return AbstractIntegration
     */
    public function getIntegration()
    {
        return $this->integration;
    }

    /**
     * @return $this
     */
    public function setAdapter(Adapter $adapter)
    {
        $this->adapter = $adapter;

        return $this;
    }
}
