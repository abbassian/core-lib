<?php

namespace Autoborna\CoreBundle\Controller;

use Autoborna\CoreBundle\CoreEvents;
use Autoborna\CoreBundle\Event\GlobalSearchEvent;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DefaultController.
 *
 * Almost all other Autoborna Bundle controllers extend this default controller
 */
class DefaultController extends CommonController
{
    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $root = $this->coreParametersHelper->get('webroot');

        if (empty($root)) {
            return $this->redirect($this->generateUrl('autoborna_dashboard_index'));
        } else {
            /** @var \Autoborna\PageBundle\Model\PageModel $pageModel */
            $pageModel = $this->getModel('page');
            $page      = $pageModel->getEntity($root);

            if (empty($page)) {
                return $this->notFound();
            }

            $slug = $pageModel->generateSlug($page);

            $request->attributes->set('ignore_mismatch', true);

            return $this->forward('AutobornaPageBundle:Public:index', ['slug' => $slug]);
        }
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function globalSearchAction()
    {
        $searchStr = $this->request->get('global_search', $this->get('session')->get('autoborna.global_search', ''));
        $this->get('session')->set('autoborna.global_search', $searchStr);

        if (!empty($searchStr)) {
            $event = new GlobalSearchEvent($searchStr, $this->get('translator'));
            $this->get('event_dispatcher')->dispatch(CoreEvents::GLOBAL_SEARCH, $event);
            $results = $event->getResults();
        } else {
            $results = [];
        }

        return $this->render('AutobornaCoreBundle:GlobalSearch:globalsearch.html.twig',
            [
                'results'      => $results,
                'searchString' => $searchStr,
            ]
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function notificationsAction()
    {
        /** @var \Autoborna\CoreBundle\Model\NotificationModel $model */
        $model = $this->getModel('core.notification');

        list($notifications, $showNewIndicator, $updateMessage) = $model->getNotificationContent(null, false, 200);

        return $this->delegateView(
            [
                'contentTemplate' => 'AutobornaCoreBundle:Notification:notifications.html.twig',
                'viewParameters'  => [
                    'showNewIndicator' => $showNewIndicator,
                    'notifications'    => $notifications,
                    'updateMessage'    => $updateMessage,
                ],
            ]
        );
    }
}
