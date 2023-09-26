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
$view['slots']->set('autobornaContent', 'client');
$view['slots']->set('headerTitle', $view['translator']->trans('autoborna.api.client.header.index'));

$view['slots']->set('actions', $view->render('AutobornaCoreBundle:Helper:page_actions.html.php', [
    'templateButtons' => [
        'new' => $permissions['create'],
    ],
    'routeBase' => 'client',
    'langVar'   => 'api.client',
]));
?>

<div class="panel panel-default bdr-t-wdh-0 mb-0">
    <?php echo $view->render('AutobornaCoreBundle:Helper:list_toolbar.html.php', [
        'searchValue' => $searchValue,
        'searchHelp'  => 'autoborna.api.client.help.searchcommands',
        'filters'     => $filters,
    ]); ?>
    <div class="page-list">
        <?php $view['slots']->output('_content'); ?>
    </div>
</div>
