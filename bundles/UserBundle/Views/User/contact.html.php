<?php

/*
 * @copyright   2014 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
$view->extend('AutobornaCoreBundle:Default:content.html.php');
$view['slots']->set('autobornaContent', 'user');
$view['slots']->set('headerTitle', $view['translator']->trans('autoborna.user.user.header.contact', ['%name%' => $user->getName()]));
?>

<div class="panel">
    <div class="panel-body pa-md">
        <?php echo $view['form']->form($form); ?>
    </div>
</div>
