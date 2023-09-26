<?php

namespace Autoborna\ConfigBundle\EventListener;

use Autoborna\ConfigBundle\ConfigEvents;
use Autoborna\ConfigBundle\Event\ConfigEvent;
use Autoborna\ConfigBundle\Service\ConfigChangeLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConfigSubscriber implements EventSubscriberInterface
{
    /**
     * @var ConfigChangeLogger
     */
    private $configChangeLogger;

    public function __construct(ConfigChangeLogger $configChangeLogger)
    {
        $this->configChangeLogger = $configChangeLogger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConfigEvents::CONFIG_POST_SAVE => ['onConfigPostSave', 0],
        ];
    }

    public function onConfigPostSave(ConfigEvent $event): void
    {
        if ($originalNormData = $event->getOriginalNormData()) {
            // We have something to log
            $this->configChangeLogger
                ->setOriginalNormData($originalNormData)
                ->log($event->getNormData());
        }
    }
}
