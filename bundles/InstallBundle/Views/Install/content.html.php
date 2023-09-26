<?php

/*
 * @copyright   2014 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
if (!$app->getRequest()->isXmlHttpRequest() && false === $view['slots']->get('contentOnly', false)) :
    //load base template
    $view->extend('AutobornaInstallBundle:Install:base.html.php');
endif;
?>

<?php echo $view->render('AutobornaCoreBundle:Notification:flashes.html.php'); ?>

<?php $view['slots']->output('_content'); ?>
