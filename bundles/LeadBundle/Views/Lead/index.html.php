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
$view['slots']->set('autobornaContent', 'lead');
$view['slots']->set('headerTitle', $view['translator']->trans('autoborna.lead.leads'));

$pageButtons = [];
if ($permissions['lead:leads:create']) {
    $pageButtons[] = [
        'attr' => [
            'class'       => 'btn btn-default btn-nospin quickadd',
            'data-toggle' => 'ajaxmodal',
            'data-target' => '#AutobornaSharedModal',
            'href'        => $view['router']->path('autoborna_contact_action', ['objectAction' => 'quickAdd']),
            'data-header' => $view['translator']->trans('autoborna.lead.lead.menu.quickadd'),
        ],
        'iconClass' => 'fa fa-bolt',
        'btnText'   => 'autoborna.lead.lead.menu.quickadd',
        'primary'   => true,
    ];

    if ($permissions['lead:imports:create']) {
        $pageButtons[] = [
            'attr' => [
                'href' => $view['router']->path('autoborna_import_action', ['object' => 'contacts', 'objectAction' => 'new']),
            ],
            'iconClass' => 'fa fa-upload',
            'btnText'   => 'autoborna.lead.lead.import',
        ];
    }

    if ($permissions['lead:imports:view']) {
        $pageButtons[] = [
            'attr' => [
                'href' => $view['router']->path('autoborna_import_index', ['object' => 'contacts']),
            ],
            'iconClass' => 'fa fa-history',
            'btnText'   => 'autoborna.lead.lead.import.index',
        ];
    }
}

// Only show toggle buttons for accessibility
$extraHtml = <<<button
<div class="btn-group ml-5 sr-only ">
    <span data-toggle="tooltip" title="{$view['translator']->trans(
    'autoborna.lead.tooltip.list'
)}" data-placement="left"><a id="table-view" href="{$view['router']->path('autoborna_contact_index', ['page' => $page, 'view' => 'list'])}" data-toggle="ajax" class="btn btn-default"><i class="fa fa-fw fa-table"></i></span></a>
    <span data-toggle="tooltip" title="{$view['translator']->trans(
    'autoborna.lead.tooltip.grid'
)}" data-placement="left"><a id="card-view" href="{$view['router']->path('autoborna_contact_index', ['page' => $page, 'view' => 'grid'])}" data-toggle="ajax" class="btn btn-default"><i class="fa fa-fw fa-th-large"></i></span></a>
</div>
button;

$view['slots']->set(
    'actions',
    $view->render(
        'AutobornaCoreBundle:Helper:page_actions.html.php',
        [
            'templateButtons' => [
                'new' => $permissions['lead:leads:create'],
            ],
            'routeBase'     => 'contact',
            'langVar'       => 'lead.lead',
            'customButtons' => $pageButtons,
            'extraHtml'     => $extraHtml,
        ]
    )
);

$toolbarButtons = [
    [
        'attr' => [
            'class'       => 'hidden-xs btn btn-default btn-sm btn-nospin',
            'href'        => 'javascript: void(0)',
            'onclick'     => 'Autoborna.toggleLiveLeadListUpdate();',
            'id'          => 'liveModeButton',
            'data-toggle' => false,
            'data-max-id' => $maxLeadId,
        ],
        'tooltip'   => $view['translator']->trans('autoborna.lead.lead.live_update'),
        'iconClass' => 'fa fa-bolt',
    ],
];

if ('list' == $indexMode) {
    $toolbarButtons[] = [
        'attr' => [
            'class'          => 'hidden-xs btn btn-default btn-sm btn-nospin'.(($anonymousShowing) ? ' btn-primary' : ''),
            'href'           => 'javascript: void(0)',
            'onclick'        => 'Autoborna.toggleAnonymousLeads();',
            'id'             => 'anonymousLeadButton',
            'data-anonymous' => $view['translator']->trans('autoborna.lead.lead.searchcommand.isanonymous'),
        ],
        'tooltip'   => $view['translator']->trans('autoborna.lead.lead.anonymous_leads'),
        'iconClass' => 'fa fa-user-secret',
    ];
}
?>

<div class="panel panel-default bdr-t-wdh-0 mb-0">
    <?php echo $view->render(
        'AutobornaCoreBundle:Helper:list_toolbar.html.php',
        [
            'searchValue'   => $searchValue,
            'searchHelp'    => 'autoborna.lead.lead.help.searchcommands',
            'action'        => $currentRoute,
            'customButtons' => $toolbarButtons,
        ]
    ); ?>
    <div class="page-list">
        <?php $view['slots']->output('_content'); ?>
    </div>
</div>
