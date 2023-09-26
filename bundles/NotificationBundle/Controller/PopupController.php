<?php

namespace Autoborna\NotificationBundle\Controller;

use Autoborna\CoreBundle\Controller\CommonController;
use Autoborna\PageBundle\Entity\Page;
use Autoborna\PageBundle\Event\PageDisplayEvent;
use Autoborna\PageBundle\PageEvents;

class PopupController extends CommonController
{
    public function indexAction()
    {
        /** @var \Autoborna\CoreBundle\Templating\Helper\AssetsHelper $assetsHelper */
        $assetsHelper = $this->container->get('templating.helper.assets');
        $assetsHelper->addStylesheet('/app/bundles/NotificationBundle/Assets/css/popup/popup.css');

        $response = $this->render(
            'AutobornaNotificationBundle:Popup:index.html.php',
            [
                'siteUrl' => $this->coreParametersHelper->get('site_url'),
            ]
        );

        $content = $response->getContent();

        $event = new PageDisplayEvent($content, new Page());
        $this->dispatcher->dispatch(PageEvents::PAGE_ON_DISPLAY, $event);
        $content = $event->getContent();

        return $response->setContent($content);
    }
}
