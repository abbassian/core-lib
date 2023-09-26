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
$view['slots']->set('autobornaContent', 'company');
$view['slots']->set('headerTitle', $view['translator']->trans('autoborna.companies.menu.root'));

$pageButtons = [];
if ($permissions['lead:leads:create']) {
    $pageButtons[] = [
        'attr' => [
            'href' => $view['router']->path('autoborna_import_action', ['object' => 'companies', 'objectAction' => 'new']),
        ],
        'iconClass' => 'fa fa-upload',
        'btnText'   => 'autoborna.lead.lead.import',
    ];

    $pageButtons[] = [
        'attr' => [
            'href' => $view['router']->path('autoborna_import_index', ['object' => 'companies']),
        ],
        'iconClass' => 'fa fa-history',
        'btnText'   => 'autoborna.lead.lead.import.index',
    ];
}

$view['slots']->set(
    'actions',
    $view->render(
        'AutobornaCoreBundle:Helper:page_actions.html.php',
        [
            'templateButtons' => [
                'new' => $permissions['lead:leads:create'],
            ],
            'routeBase'     => 'company',
            'customButtons' => $pageButtons,
        ]
    )
);
?>

<div class="panel panel-default bdr-t-wdh-0 mb-0">
    <?php echo $view->render(
        'AutobornaCoreBundle:Helper:list_toolbar.html.php',
        [
            'searchValue' => $searchValue,
            'searchHelp'  => 'autoborna.core.help.searchcommands',
            'action'      => $currentRoute,
        ]
    ); ?>
    <div class="page-list">
        <?php $view['slots']->output('_content'); ?>
    </div>
</div>
