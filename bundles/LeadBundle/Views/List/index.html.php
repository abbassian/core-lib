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
$view['slots']->set('autobornaContent', 'leadlist');
$view['slots']->set('headerTitle', $view['translator']->trans('autoborna.lead.list.header.index'));

$view['slots']->set(
    'actions',
    $view->render(
        'AutobornaCoreBundle:Helper:page_actions.html.php',
        [
            'templateButtons' => [
                'new' => true, // this is intentional. Each user can segment leads
            ],
            'routeBase' => 'segment',
            'langVar'   => 'lead.list',
            'tooltip'   => 'autoborna.lead.lead.segment.add.help',
        ]
    )
);
?>

<div class="panel panel-default bdr-t-wdh-0">
    <?php echo $view->render(
        'AutobornaCoreBundle:Helper:list_toolbar.html.php',
        [
            'searchValue' => $searchValue,
            'searchHelp'  => 'autoborna.lead.list.help.searchcommands',
            'action'      => $currentRoute,
            'filters'     => (isset($filters)) ? $filters : [],
        ]
    ); ?>
    <div class="page-list">
        <?php $view['slots']->output('_content'); ?>
    </div>
</div>
