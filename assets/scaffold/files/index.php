<?php

/*
 * @copyright   2014 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
define('MAUTIC_ROOT_DIR', __DIR__);

// Fix for hosts that do not have date.timezone set, it will be reset based on users settings
date_default_timezone_set('UTC');

require_once 'autoload.php';

use Autoborna\CoreBundle\ErrorHandler\ErrorHandler;
use Autoborna\Middleware\MiddlewareBuilder;
use function Stack\run;

ErrorHandler::register('prod');

run((new MiddlewareBuilder(new AppKernel('prod', false)))->resolve());
