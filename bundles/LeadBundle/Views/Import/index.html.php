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
$view['slots']->set('autobornaContent', $autobornaContent);
$view['slots']->set('headerTitle', $view['translator']->trans('autoborna.lead.import.list'));
$view['slots']->set(
    'actions',
    $view->render(
        'AutobornaCoreBundle:Helper:page_actions.html.php',
        [
            'templateButtons' => [
                'new' => $permissions[$permissionBase.':create'],
            ],
            'routeBase' => 'import',
            'langVar'   => $translationBase,
            'query'     => [
                    'object' => $view['request']->getParameter('object'),
            ],
        ]
    )
);

?>

<div class="panel panel-default bdr-t-wdh-0 mb-0">
    <?php // todo
    // echo $view->render(
    //     'AutobornaCoreBundle:Helper:list_toolbar.html.php',
    //     [
    //         'searchValue'   => $searchValue,
    //         'searchHelp'    => 'autoborna.lead.lead.help.searchcommands',
    //         'action'        => $currentRoute,
    //     ]
    // );
    ?>
    <div class="page-list">
        <?php $view['slots']->output('_content'); ?>
    </div>
</div>
