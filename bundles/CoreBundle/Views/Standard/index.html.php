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
if (!$view['slots']->get('autobornaContent')) {
    if (isset($autobornaContent)) {
        $view['slots']->set('autobornaContent', $autobornaContent);
    }
}

if (!$view['slots']->get('headerTitle')) {
    if (!isset($headerTitle)) {
        $headerTitle = 'Autoborna';
    }
    $view['slots']->set('headerTitle', $view['translator']->trans($headerTitle));
}

$view['slots']->set(
    'actions',
    $view->render(
        'AutobornaCoreBundle:Helper:page_actions.html.php',
        [
            'templateButtons' => [
                'new' => $permissions[$permissionBase.':create'],
            ],
            'actionRoute'     => $actionRoute,
            'indexRoute'      => $indexRoute,
            'translationBase' => $translationBase,
        ]
    )
);
?>

<div class="panel panel-default bdr-t-wdh-0 mb-0">
    <?php echo $view->render(
        'AutobornaCoreBundle:Helper:list_toolbar.html.php',
        [
            'searchValue'      => $searchValue,
            'searchHelp'       => isset($searchHelp) ? $searchHelp : '',
            'action'           => $currentRoute,
            'actionRoute'      => $actionRoute,
            'indexRoute'       => $indexRoute,
            'translationBase'  => $translationBase,
            'preCustomButtons' => (isset($toolBarButtons)) ? $toolBarButtons : null,
            'templateButtons'  => [
                'delete' => $permissions[$permissionBase.':delete'],
            ],
            'filters' => (isset($filters)) ? $filters : [],
        ]
    ); ?>

    <div class="page-list">
        <?php echo $view['content']->getCustomContent('content.above', $autobornaTemplateVars); ?>
        <?php $view['slots']->output('_content'); ?>
        <?php echo $view['content']->getCustomContent('content.below', $autobornaTemplateVars); ?>
    </div>
</div>