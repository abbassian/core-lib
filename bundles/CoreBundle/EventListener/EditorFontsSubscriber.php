<?php

declare(strict_types=1);

namespace Autoborna\CoreBundle\EventListener;

use Autoborna\CoreBundle\CoreEvents;
use Autoborna\CoreBundle\Event\CustomAssetsEvent;
use Autoborna\CoreBundle\Helper\CoreParametersHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EditorFontsSubscriber implements EventSubscriberInterface
{
    public const PARAMETER_EDITOR_FONTS = 'editor_fonts';

    /**
     * @var CoreParametersHelper
     */
    private $coreParametersHelper;

    public function __construct(CoreParametersHelper $coreParametersHelper)
    {
        $this->coreParametersHelper = $coreParametersHelper;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CoreEvents::VIEW_INJECT_CUSTOM_ASSETS => ['addGlobalAssets', 0],
        ];
    }

    public function addGlobalAssets(CustomAssetsEvent $customAssetsEvent): void
    {
        $this->addEditorFonts($customAssetsEvent);
    }

    private function addEditorFonts(CustomAssetsEvent $customAssetsEvent): void
    {
        $fonts = (array) $this->coreParametersHelper->get(static::PARAMETER_EDITOR_FONTS, []);
        foreach ($fonts as $font) {
            if (empty($font['url'])) {
                continue;
            }

            $customAssetsEvent->addStylesheet($font['url']);
        }
    }
}
