<?php

namespace Autoborna\DynamicContentBundle\EventListener;

use Autoborna\CoreBundle\CoreEvents;
use Autoborna\CoreBundle\Event\BuildJsEvent;
use Autoborna\CoreBundle\Templating\Helper\AssetsHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class BuildJsSubscriber implements EventSubscriberInterface
{
    /**
     * @var AssetsHelper
     */
    private $assetsHelper;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        AssetsHelper $assetsHelper,
        TranslatorInterface $translator,
        RequestStack $requestStack,
        RouterInterface $router
    ) {
        $this->assetsHelper = $assetsHelper;
        $this->translator   = $translator;
        $this->requestStack = $requestStack;
        $this->router       = $router;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            CoreEvents::BUILD_MAUTIC_JS => ['onBuildJs', 200],
        ];
    }

    /**
     * Adds the AutobornaJS definition and core
     * JS functions for use in Bundles. This
     * must retain top priority of 1000.
     */
    public function onBuildJs(BuildJsEvent $event)
    {
        $dwcUrl = $this->router->generate('autoborna_api_dynamicContent_action', ['objectAlias' => 'slotNamePlaceholder'], UrlGeneratorInterface::ABSOLUTE_URL);

        $js = <<<JS
        
           // call variable if doesnt exist
            if (typeof AutobornaDomain == 'undefined') {
                var AutobornaDomain = '{$this->requestStack->getCurrentRequest()->getSchemeAndHttpHost()}';
            }            
            if (typeof AutobornaLang == 'undefined') {
                var AutobornaLang = {
                     'submittingMessage': "{$this->translator->trans('autoborna.form.submission.pleasewait')}"
        };
            }
AutobornaJS.replaceDynamicContent = function (params) {
    params = params || {};

    var dynamicContentSlots = document.querySelectorAll('.autoborna-slot, [data-slot="dwc"]');
    if (dynamicContentSlots.length) {
        AutobornaJS.iterateCollection(dynamicContentSlots)(function(node, i) {
            var slotName = node.dataset['slotName'];
            if ('undefined' === typeof slotName) {
                slotName = node.dataset['paramSlotName'];
            }
            if ('undefined' === typeof slotName) {
                node.innerHTML = '';
                return;
            }
            var url = '{$dwcUrl}'.replace('slotNamePlaceholder', slotName);

            AutobornaJS.makeCORSRequest('GET', url, params, function(response, xhr) {
                if (response.content) {
                    var dwcContent = response.content;
                    node.innerHTML = dwcContent;

                    if (response.id && response.sid) {
                        AutobornaJS.setTrackedContact(response);
                    }

                    // form load library
                    if (dwcContent.search("autobornaform_wrapper") > 0) {
                        // if doesn't exist
                        if (typeof AutobornaSDK == 'undefined') {
                            AutobornaJS.insertScript('{$this->assetsHelper->getUrl('media/js/autoborna-form.js', null, null, true)}');
                            
                            // check initialize form library
                            var fileInterval = setInterval(function() {
                                if (typeof AutobornaSDK != 'undefined') {
                                    AutobornaSDK.onLoad(); 
                                    clearInterval(fileInterval); // clear interval
                                 }
                             }, 100); // check every 100ms
                        } else {
                            AutobornaSDK.onLoad();
                         }
                    }

                    var m;
                    var regEx = /<script[^>]+src="?([^"\s]+)"?\s/g;                    
                    
                    while (m = regEx.exec(dwcContent)) {
                        if ((m[1]).search("/focus/") > 0) {
                            AutobornaJS.insertScript(m[1]);
                        }
                    }

                    if (dwcContent.search("fr-gatedvideo") > 0) {
                        AutobornaJS.initGatedVideo();
                    }
                }
            });
        });
    }
};

AutobornaJS.beforeFirstEventDelivery(AutobornaJS.replaceDynamicContent);
JS;
        $event->appendJs($js, 'Autoborna Dynamic Content');
    }
}
