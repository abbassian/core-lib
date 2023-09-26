<?php

namespace Autoborna\CoreBundle\Controller;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Interface AutobornaController.
 *
 * A dummy interface to ensure that only Autoborna bundles are affected by Autoborna onKernelController events
 */
interface AutobornaController
{
    /**
     * Initialize the controller.
     *
     * @return mixed
     */
    public function initialize(FilterControllerEvent $event);
}
