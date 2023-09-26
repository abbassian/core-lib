<?php

/*
 * @copyright   2021 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
if ('index' == $tmpl):
    $view->extend('AutobornaPageBundle:Result:index.html.php');
endif;

$pageId = $activePage->getId();
?>
<div class="table-responsive table-responsive-force">
    <table class="table table-hover table-striped table-bordered pageresult-list" id="pageResultsTable">
        <thead>
            <tr>
                <?php
                echo $view->render('AutobornaCoreBundle:Helper:tableheader.html.php', [
                    'sessionVar' => 'pageresult.'.$pageId,
                    'orderBy'    => 's.id',
                    'text'       => 'autoborna.form.report.submission.id',
                    'class'      => 'col-pageresult-id',
                    'filterBy'   => 's.id',
                ]);

                echo $view->render('AutobornaCoreBundle:Helper:tableheader.html.php', [
                    'sessionVar' => 'pageresult.'.$pageId,
                    'orderBy'    => 's.lead_id',
                    'text'       => 'autoborna.lead.report.contact_id',
                    'class'      => 'col-pageresult-lead-id',
                    'filterBy'   => 's.lead_id',
                ]);

                echo $view->render('AutobornaCoreBundle:Helper:tableheader.html.php', [
                    'sessionVar' => 'pageresult.'.$pageId,
                    'orderBy'    => 's.form_id',
                    'text'       => 'autoborna.form.report.form_id',
                    'class'      => 'col-pageresult-form-id',
                    'filterBy'   => 's.form_id',
                ]);

                echo $view->render('AutobornaCoreBundle:Helper:tableheader.html.php', [
                    'sessionVar' => 'pageresult.'.$pageId,
                    'orderBy'    => 's.date_submitted',
                    'text'       => 'autoborna.form.result.thead.date',
                    'class'      => 'col-pageresult-date',
                    'default'    => true,
                    'filterBy'   => 's.date_submitted',
                    'dataToggle' => 'date',
                ]);

                echo $view->render('AutobornaCoreBundle:Helper:tableheader.html.php', [
                    'sessionVar' => 'pageresult.'.$pageId,
                    'orderBy'    => 'i.ip_address',
                    'text'       => 'autoborna.core.ipaddress',
                    'class'      => 'col-pageresult-ip',
                    'filterBy'   => 'i.ip_address',
                ]);
                ?>
            </tr>
        </thead>
        <tbody>
        <?php if (count($items)): ?>
        <?php foreach ($items as $item): ?>
            <?php $item['name'] = $view['translator']->trans('autoborna.form.form.results.name', ['%id%' => $item['id']]); ?>
            <tr>
                <td><?php echo $view->escape($item['id']); ?></td>
                <td>
                    <?php if (!empty($item['leadId'])): ?>
                    <a href="<?php echo $view['router']->path('autoborna_contact_action', ['objectAction' => 'view', 'objectId' => $item['leadId']]); ?>" data-toggle="ajax">
                        <?php echo $view->escape($item['leadId']); ?>
                    </a>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (!empty($item['formId'])): ?>
                    <a href="<?php echo $view['router']->path('autoborna_form_action', ['objectAction' => 'view', 'objectId' => $item['formId']]); ?>" data-toggle="ajax">
                        <?php echo $view->escape($item['formId']); ?>
                    </a>
                    <?php endif; ?>
                </td>
                <td><?php echo $view['date']->toFull($item['dateSubmitted'], 'UTC'); ?></td>
                <td><?php echo $view->escape($item['ipAddress']); ?></td>
            </tr>
        <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">
                    <?php echo $view->render('AutobornaCoreBundle:Helper:noresults.html.php'); ?>
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<div class="panel-footer">
    <?php echo $view->render('AutobornaCoreBundle:Helper:pagination.html.php', [
        'totalItems' => $totalCount,
        'page'       => $page,
        'limit'      => $limit,
        'baseUrl'    => $view['router']->path('autoborna_page_results', ['objectId' => $activePage->getId()]),
        'sessionVar' => 'pageresult.'.$pageId,
    ]); ?>
</div>
