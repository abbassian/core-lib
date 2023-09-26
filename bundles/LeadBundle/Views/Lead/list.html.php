<?php

/*
 * @copyright   2014 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
if ('index' == $tmpl) {
    $view->extend('AutobornaLeadBundle:Lead:index.html.php');
}

$customButtons = [];
if ($permissions['lead:leads:editown'] || $permissions['lead:leads:editother']) {
    $customButtons = [
        [
            'attr' => [
                'class'       => 'btn btn-default btn-sm btn-nospin',
                'data-toggle' => 'ajaxmodal',
                'data-target' => '#AutobornaSharedModal',
                'href'        => $view['router']->path('autoborna_segment_batch_contact_view'),
                'data-header' => $view['translator']->trans('autoborna.lead.batch.lists'),
            ],
            'btnText'   => $view['translator']->trans('autoborna.lead.batch.lists'),
            'iconClass' => 'fa fa-pie-chart',
        ],
        [
            'attr' => [
                'class'       => 'btn btn-default btn-sm btn-nospin',
                'data-toggle' => 'ajaxmodal',
                'data-target' => '#AutobornaSharedModal',
                'href'        => $view['router']->path('autoborna_contact_action', ['objectAction' => 'batchStages']),
                'data-header' => $view['translator']->trans('autoborna.lead.batch.stages'),
            ],
            'btnText'   => $view['translator']->trans('autoborna.lead.batch.stages'),
            'iconClass' => 'fa fa-tachometer',
        ],
        [
            'attr' => [
                'class'       => 'btn btn-default btn-sm btn-nospin',
                'data-toggle' => 'ajaxmodal',
                'data-target' => '#AutobornaSharedModal',
                'href'        => $view['router']->path('autoborna_contact_action', ['objectAction' => 'batchCampaigns']),
                'data-header' => $view['translator']->trans('autoborna.lead.batch.campaigns'),
            ],
            'btnText'   => $view['translator']->trans('autoborna.lead.batch.campaigns'),
            'iconClass' => 'fa fa-clock-o',
        ],
        [
            'attr' => [
                'class'       => 'btn btn-default btn-sm btn-nospin',
                'data-toggle' => 'ajaxmodal',
                'data-target' => '#AutobornaSharedModal',
                'href'        => $view['router']->path('autoborna_contact_action', ['objectAction' => 'batchOwners']),
                'data-header' => $view['translator']->trans('autoborna.lead.batch.owner'),
            ],
            'btnText'   => $view['translator']->trans('autoborna.lead.batch.owner'),
            'iconClass' => 'fa fa-user',
        ],
        [
            'attr' => [
                'class'       => 'btn btn-default btn-sm btn-nospin',
                'data-toggle' => 'ajaxmodal',
                'data-target' => '#AutobornaSharedModal',
                'href'        => $view['router']->path('autoborna_contact_action', ['objectAction' => 'batchDnc']),
                'data-header' => $view['translator']->trans('autoborna.lead.batch.dnc'),
            ],
            'btnText'   => $view['translator']->trans('autoborna.lead.batch.dnc'),
            'iconClass' => 'fa fa-ban text-danger',
        ],
    ];
}
?>

<?php if (count($items)): ?>
<div class="table-responsive">
    <table class="table table-hover table-striped table-bordered" id="leadTable">
        <thead>
            <tr>
                <?php
                echo $view->render('AutobornaCoreBundle:Helper:tableheader.html.php', [
                    'checkall'        => 'true',
                    'target'          => '#leadTable',
                    'templateButtons' => [
                        'delete' => $permissions['lead:leads:deleteown'] || $permissions['lead:leads:deleteother'],
                    ],
                    'customButtons' => $customButtons,
                    'langVar'       => 'lead.lead',
                    'routeBase'     => 'contact',
                    'tooltip'       => $view['translator']->trans('autoborna.lead.list.checkall.help'),
                ]);

                $columsAliases = array_flip($columns);
                foreach ($columns as $column=>$label) {
                    $template = 'AutobornaLeadBundle:Lead\header:'.$column.'.html.php';
                    if (!$view->exists($template)) {
                        $template = 'AutobornaLeadBundle:Lead\header:default.html.php';
                    }
                    echo $view->render(
                        $template,
                        [
                            'label'  => $label,
                            'column' => $column,
                            'class'  => array_search($column, $columsAliases) > 1 ? 'hidden-xs' : '',
                        ]
                    );
                }
                ?>
            </tr>
        </thead>
        <tbody>
        <?php echo $view->render('AutobornaLeadBundle:Lead:list_rows.html.php', [
            'items'         => $items,
            'columns'       => $columns,
            'security'      => $security,
            'currentList'   => $currentList,
            'permissions'   => $permissions,
            'noContactList' => $noContactList,
        ]); ?>
        </tbody>
    </table>
</div>
<div class="panel-footer">
    <?php echo $view->render('AutobornaCoreBundle:Helper:pagination.html.php', [
        'totalItems' => $totalItems,
        'page'       => $page,
        'limit'      => $limit,
        'menuLinkId' => 'autoborna_contact_index',
        'baseUrl'    => $view['router']->path('autoborna_contact_index'),
        'tmpl'       => $indexMode,
        'sessionVar' => 'lead',
    ]); ?>
</div>
<?php else: ?>
<?php echo $view->render('AutobornaCoreBundle:Helper:noresults.html.php'); ?>
<?php endif; ?>
