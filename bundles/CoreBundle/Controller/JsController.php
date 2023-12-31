<?php

namespace Autoborna\CoreBundle\Controller;

use Autoborna\CoreBundle\CoreEvents;
use Autoborna\CoreBundle\Event\BuildJsEvent;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class JsController.
 */
class JsController extends CommonController
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        // Don't store a visitor with this request
        defined('MAUTIC_NON_TRACKABLE_REQUEST') || define('MAUTIC_NON_TRACKABLE_REQUEST', 1);

        $dispatcher = $this->dispatcher;
        $debug      = $this->factory->getKernel()->isDebug();
        $event      = new BuildJsEvent($this->getJsHeader(), $debug);

        if ($dispatcher->hasListeners(CoreEvents::BUILD_MAUTIC_JS)) {
            $dispatcher->dispatch(CoreEvents::BUILD_MAUTIC_JS, $event);
        }

        return new Response($event->getJs(), 200, ['Content-Type' => 'application/javascript']);
    }

    /**
     * Build a JS header for the Autoborna embedded JS.
     *
     * @return string
     */
    protected function getJsHeader()
    {
        $year = date('Y');

        return <<<JS
/**
 * @package     AutobornaJS
 * @copyright   {$year} Autoborna Contributors. All rights reserved.
 * @author      Autoborna
 * @link        http://autoborna.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
JS;
    }
}
