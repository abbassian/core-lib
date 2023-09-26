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
$view['slots']->set('autobornaContent', 'category');
$view['slots']->set('headerTitle', $view['translator']->trans('autoborna.category.header.index'));

$view['slots']->set('actions', $view->render('AutobornaCoreBundle:Helper:page_actions.html.php', [
    'templateButtons' => [
       'new' => $permissions[$permissionBase.':create'],
    ],
    'routeBase' => 'category',
    'query'     => ['bundle' => $bundle, 'show_bundle_select' => true],
    'editMode'  => 'ajaxmodal',
    'editAttr'  => [
        'data-target' => '#AutobornaSharedModal',
        'data-header' => $view['translator']->trans('autoborna.category.header.new'),
        'data-toggle' => 'ajaxmodal',
    ],
]));
?>

<div class="panel panel-default bdr-t-wdh-0 mb-0">
    <?php //TODO - Restore these buttons to the listactions when custom content is supported
    /*<div class="btn-group">
        <button type="button" class="btn btn-default"><i class="fa fa-upload"></i></button>
        <button type="button" class="btn btn-default"><i class="fa fa-archive"></i></button>
    </div>*/ ?>
    <?php echo $view->render('AutobornaCoreBundle:Helper:list_toolbar.html.php', [
        'searchValue' => $searchValue,
        'searchHelp'  => 'autoborna.category.help.searchcommands',
        'filters'     => [
            'bundle' => [
                'options'         => $categoryTypes,
                'values'          => [$bundle],
                'translateLabels' => true,
            ],
        ],
        'action' => $currentRoute,
    ]); ?>

    <div class="page-list">
        <?php $view['slots']->output('_content'); ?>
    </div>
</div>
