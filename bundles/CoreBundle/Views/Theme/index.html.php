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
$view['slots']->set('autobornaContent', 'theme');
$view['slots']->set('headerTitle', $view['translator']->trans('autoborna.core.themes'));
$view['slots']->set('actions', $view->render('AutobornaCoreBundle:Theme:form.html.php', ['form' => $form]));
?>

<div class="panel panel-default bdr-t-wdh-0 mb-0">
    <?php echo $view->render(
        'AutobornaCoreBundle:Helper:list_toolbar.html.php',
        [
            'action' => $currentRoute,
        ]
    ); ?>
    <div class="page-list">
        <?php $view['slots']->output('_content'); ?>
    </div>
</div>
