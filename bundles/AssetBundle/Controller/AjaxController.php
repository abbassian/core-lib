<?php

namespace Autoborna\AssetBundle\Controller;

use Gaufrette\Filesystem;
use Autoborna\AssetBundle\AssetEvents;
use Autoborna\AssetBundle\Event\RemoteAssetBrowseEvent;
use Autoborna\CoreBundle\Controller\AjaxController as CommonAjaxController;
use Autoborna\CoreBundle\Helper\InputHelper;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AjaxController.
 */
class AjaxController extends CommonAjaxController
{
    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function categoryListAction(Request $request)
    {
        $filter    = InputHelper::clean($request->query->get('filter'));
        $results   = $this->getModel('asset')->getLookupResults('category', $filter, 10);
        $dataArray = [];
        foreach ($results as $r) {
            $dataArray[] = [
                'label' => $r['title']." ({$r['id']})",
                'value' => $r['id'],
            ];
        }

        return $this->sendJsonResponse($dataArray);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @throws \Exception
     */
    protected function fetchRemoteFilesAction(Request $request)
    {
        $provider   = InputHelper::string($request->request->get('provider'));
        $path       = InputHelper::string($request->request->get('path', ''));
        $dispatcher = $this->dispatcher;
        $name       = AssetEvents::ASSET_ON_REMOTE_BROWSE;

        if (!$dispatcher->hasListeners($name)) {
            return $this->sendJsonResponse(['success' => 0]);
        }

        /** @var \Autoborna\PluginBundle\Helper\IntegrationHelper $integrationHelper */
        $integrationHelper = $this->factory->getHelper('integration');

        /** @var \Autoborna\PluginBundle\Integration\AbstractIntegration $integration */
        $integration = $integrationHelper->getIntegrationObject($provider);

        $event = new RemoteAssetBrowseEvent($integration);

        $dispatcher->dispatch($name, $event);

        if (!$adapter = $event->getAdapter()) {
            return $this->sendJsonResponse(['success' => 0]);
        }

        $connector = new Filesystem($adapter);

        $output = $this->renderView(
            'AutobornaAssetBundle:Remote:list.html.php',
            [
                'connector'   => $connector,
                'integration' => $integration,
                'items'       => $connector->listKeys($path),
            ]
        );

        return $this->sendJsonResponse(['success' => 1, 'output' => $output]);
    }
}
