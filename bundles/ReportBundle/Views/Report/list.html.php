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
    $view->extend('AutobornaReportBundle:Report:index.html.php');
}
?>
<?php if (count($items)): ?>
    <div class="table-responsive panel-collapse pull out page-list">
        <table class="table table-hover table-striped table-bordered report-list" id="reportTable">
            <thead>
            <tr>
                <?php
                echo $view->render(
                    'AutobornaCoreBundle:Helper:tableheader.html.php',
                    [
                        'checkall'        => 'true',
                        'target'          => '#reportTable',
                        'langVar'         => 'report.report',
                        'routeBase'       => 'report',
                        'templateButtons' => [
                            'delete' => $permissions['report:reports:deleteown'] || $permissions['report:reports:deleteother'],
                        ],
                    ]
                );

                echo $view->render(
                    'AutobornaCoreBundle:Helper:tableheader.html.php',
                    [
                        'sessionVar' => 'report',
                        'orderBy'    => 'r.name',
                        'text'       => 'autoborna.core.name',
                        'class'      => 'col-report-name',
                        'default'    => true,
                    ]
                );

                echo $view->render(
                    'AutobornaCoreBundle:Helper:tableheader.html.php',
                    [
                        'sessionVar' => 'report',
                        'orderBy'    => 'r.id',
                        'text'       => 'autoborna.core.id',
                        'class'      => 'col-report-id visible-md visible-lg',
                    ]
                );
                ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td>
                        <?php
                        echo $view->render(
                            'AutobornaCoreBundle:Helper:list_actions.html.php',
                            [
                                'item'            => $item,
                                'templateButtons' => [
                                    'edit' => $security->hasEntityAccess(
                                        $permissions['report:reports:editown'],
                                        $permissions['report:reports:editother'],
                                        $item->getCreatedBy()
                                    ),
                                    'clone'  => $permissions['report:reports:create'],
                                    'delete' => $security->hasEntityAccess(
                                        $permissions['report:reports:deleteown'],
                                        $permissions['report:reports:deleteother'],
                                        $item->getCreatedBy()
                                    ),
                                ],
                                'routeBase'     => 'report',
                                'langVar'       => 'report.report',
                                'customButtons' => $item->isScheduled() ? [] : [
                                    [
                                        'attr' => [
                                            'data-toggle' => 'ajaxmodal',
                                            'data-target' => '#AssetPreviewModal',
                                            'href'        => $view['router']->path('autoborna_report_schedule', ['reportId' => $item->getId()]),
                                        ],
                                        'btnText'   => $view['translator']->trans('autoborna.report.export.and.send'),
                                        'iconClass' => 'fa fa-send-o',
                                    ],
                                ],
                            ]
                        );
                        ?>
                    </td>
                    <td>
                        <div>
                            <?php echo $view->render(
                                'AutobornaCoreBundle:Helper:publishstatus_icon.html.php',
                                ['item' => $item, 'model' => 'report.report']
                            ); ?>
                            <a href="<?php echo $view['router']->path('autoborna_report_view', ['objectId' => $item->getId()]); ?>" data-toggle="ajax">
                                <?php echo $view->escape($item->getName()); ?>
                            </a>
                        </div>
                        <?php if ($description = $item->getDescription()): ?>
                            <div class="text-muted mt-4">
                                <small><?php echo $view->escape($description); ?></small>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td class="visible-md visible-lg"><?php echo $item->getId(); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="panel-footer">
            <?php echo $view->render(
                'AutobornaCoreBundle:Helper:pagination.html.php',
                [
                    'totalItems' => $totalItems,
                    'page'       => $page,
                    'limit'      => $limit,
                    'menuLinkId' => 'autoborna_report_index',
                    'baseUrl'    => $view['router']->path('autoborna_report_index'),
                    'sessionVar' => 'report',
                ]
            ); ?>
        </div>
    </div>
<?php else: ?>
    <?php echo $view->render('AutobornaCoreBundle:Helper:noresults.html.php'); ?>
<?php endif; ?>
