<?php

/*
 * @copyright   2014 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
if ('index' == $tmpl):
    $view->extend('AutobornaLeadBundle:Import:index.html.php');
endif;
?>

<?php if (count($items)): ?>
<div class="table-responsive">
    <table class="table table-hover table-striped table-bordered" id="importTable">
        <thead>
            <tr>
                <?php
                echo $view->render('AutobornaCoreBundle:Helper:tableheader.html.php', [
                    'sessionVar' => $sessionVar,
                    'orderBy'    => $tablePrefix.'.status',
                    'text'       => 'autoborna.lead.import.status',
                    'class'      => 'col-status',
                ]);

                echo $view->render('AutobornaCoreBundle:Helper:tableheader.html.php', [
                    'sessionVar' => $sessionVar,
                    'orderBy'    => $tablePrefix.'.originalFile',
                    'text'       => 'autoborna.lead.import.source.file',
                    'class'      => 'col-original-file',
                ]);

                echo $view->render('AutobornaCoreBundle:Helper:tableheader.html.php', [
                    'text'  => 'autoborna.lead.import.runtime',
                    'class' => 'col-runtime',
                ]);

                echo $view->render('AutobornaCoreBundle:Helper:tableheader.html.php', [
                    'text'  => 'autoborna.lead.import.progress',
                    'class' => 'col-progress',
                ]);

                echo $view->render('AutobornaCoreBundle:Helper:tableheader.html.php', [
                    'sessionVar' => $sessionVar,
                    'orderBy'    => $tablePrefix.'.lineCount',
                    'text'       => 'autoborna.lead.import.line.count',
                    'class'      => 'col-line-count',
                ]);

                echo $view->render('AutobornaCoreBundle:Helper:tableheader.html.php', [
                    'sessionVar' => $sessionVar,
                    'orderBy'    => $tablePrefix.'.insertedCount',
                    'text'       => 'autoborna.lead.import.inserted.count',
                    'class'      => 'col-inserted-count',
                ]);

                echo $view->render('AutobornaCoreBundle:Helper:tableheader.html.php', [
                    'sessionVar' => $sessionVar,
                    'orderBy'    => $tablePrefix.'.updatedCount',
                    'text'       => 'autoborna.lead.import.updated.count',
                    'class'      => 'col-updated-count',
                ]);

                echo $view->render('AutobornaCoreBundle:Helper:tableheader.html.php', [
                    'sessionVar' => $sessionVar,
                    'orderBy'    => $tablePrefix.'.ignoredCount',
                    'text'       => 'autoborna.lead.import.ignored.count',
                    'class'      => 'col-ignored-count',
                ]);

                echo $view->render('AutobornaCoreBundle:Helper:tableheader.html.php', [
                    'sessionVar' => $sessionVar,
                    'orderBy'    => $tablePrefix.'.createdByUser',
                    'text'       => 'autoborna.core.create.by.past.tense',
                    'class'      => 'col-created visible-md visible-lg',
                ]);

                echo $view->render('AutobornaCoreBundle:Helper:tableheader.html.php', [
                    'sessionVar' => $sessionVar,
                    'orderBy'    => $tablePrefix.'.dateAdded',
                    'text'       => 'autoborna.core.date.added',
                    'class'      => 'col-date-added visible-md visible-lg',
                    'default'    => true,
                ]);

                echo $view->render('AutobornaCoreBundle:Helper:tableheader.html.php', [
                    'sessionVar' => $sessionVar,
                    'orderBy'    => $tablePrefix.'.id',
                    'text'       => 'autoborna.core.id',
                    'class'      => 'col-lead-id visible-md visible-lg',
                ]);
                ?>
            </tr>
        </thead>
        <tbody>
        <?php echo $view->render('AutobornaLeadBundle:Import:list_rows.html.php', [
            'items'           => $items,
            'permissions'     => $permissions,
            'indexRoute'      => $indexRoute,
            'permissionBase'  => $permissionBase,
            'translationBase' => $translationBase,
            'actionRoute'     => $actionRoute,
        ]); ?>
        </tbody>
    </table>
</div>
<div class="panel-footer">
    <?php echo $view->render('AutobornaCoreBundle:Helper:pagination.html.php', [
        'totalItems' => $totalItems,
        'page'       => $page,
        'limit'      => $limit,
        'menuLinkId' => $indexRoute,
        'baseUrl'    => $view['router']->path($indexRoute, ['object' => $app->getRequest()->get('object', 'contacts')]),
        'sessionVar' => $sessionVar,
    ]); ?>
</div>
<?php else: ?>
<?php echo $view->render('AutobornaCoreBundle:Helper:noresults.html.php'); ?>
<?php endif; ?>
