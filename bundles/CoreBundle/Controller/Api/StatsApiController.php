<?php

namespace Autoborna\CoreBundle\Controller\Api;

use Autoborna\ApiBundle\Controller\CommonApiController;
use Autoborna\CoreBundle\CoreEvents;
use Autoborna\CoreBundle\Event\StatsEvent;
use Autoborna\CoreBundle\Helper\InputHelper;

/**
 * Class StatsApiController.
 */
class StatsApiController extends CommonApiController
{
    /**
     * Lists stats for a database table.
     *
     * @param string $table
     * @param string $itemsName
     * @param array  $order
     * @param array  $where
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction($table = null, $itemsName = 'stats', $order = [], $where = [], $start = 0, $limit = 100)
    {
        $response = [];
        $where    = InputHelper::cleanArray(empty($where) ? $this->request->query->get('where', []) : $where);
        $order    = InputHelper::cleanArray(empty($order) ? $this->request->query->get('order', []) : $order);
        $start    = (int) $this->request->query->get('start', $start);
        $limit    = (int) $this->request->query->get('limit', $limit);

        // Ensure internal flag is not spoofed
        $this->sanitizeWhereClauseArrayFromRequest($where);

        try {
            $event = new StatsEvent($table, $start, $limit, $order, $where, $this->get('autoborna.helper.user')->getUser());
            $this->get('event_dispatcher')->dispatch(CoreEvents::LIST_STATS, $event);

            // Return available tables if no result was set
            if (!$event->hasResults()) {
                $response['availableTables'] = $event->getTables();
                $response['tableColumns']    = $event->getTableColumns();
            } else {
                $results              = $event->getResults();
                $response['total']    = $results['total'];
                $response[$itemsName] = $results['results'];
            }
        } catch (\Exception $e) {
            $response['errors'] = [
                [
                    'message' => $e->getMessage(),
                    'code'    => $e->getCode(),
                ],
            ];
        }

        $view = $this->view($response);

        return $this->handleView($view);
    }
}
