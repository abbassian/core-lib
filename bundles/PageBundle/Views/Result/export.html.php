<?php

/*
 * @copyright   2021 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
$view->extend('AutobornaCoreBundle:Default:slim.html.php');
$view['slots']->set('pageTitle', $pageTitle);
$view['slots']->set('headerTitle', $view['translator']->trans('autoborna.page.result.header.index', [
    '%name%' => $page->getName(),
]));
?>

<div class="pageresults">
    <table class="table table-hover table-striped table-bordered pageresult-list">
        <thead>
        <tr>
            <th class="col-pageresult-id"></th>
            <th class="col-pageresult-leadId"><?php echo $view['translator']->trans('autoborna.lead.report.contact_id'); ?></th>
            <th class="col-pageresult-formId"><?php echo $view['translator']->trans('autoborna.form.report.form_id'); ?></th>
            <th class="col-pageresult-date"><?php echo $view['translator']->trans('autoborna.form.result.thead.date'); ?></th>
            <th class="col-pageresult-ip"><?php echo $view['translator']->trans('autoborna.core.ipaddress'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($results as $item):?>
            <tr>
                <td><?php echo $item['id']; ?></td>
                <td><?php echo $item['leadId']; ?></td>
                <td><?php echo $item['formId']; ?></td>
                <td><?php echo $view['date']->toFull($item['dateSubmitted'], 'UTC'); ?></td>
                <td><?php echo $item['ipAddress']; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
