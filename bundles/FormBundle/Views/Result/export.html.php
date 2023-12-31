<?php

/*
 * @copyright   2014 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
$view->extend('AutobornaCoreBundle:Default:slim.html.php');
$view['slots']->set('pageTitle', $pageTitle);
$view['slots']->set('headerTitle', $view['translator']->trans('autoborna.form.result.header.index', [
    '%name%' => $form->getName(),
]));
?>

<div class="formresults">
    <table class="table table-hover table-striped table-bordered formresult-list">
        <thead>
        <tr>
            <th class="col-formresult-id"></th>
            <th class="col-formresult-date"><?php echo $view['translator']->trans('autoborna.form.result.thead.date'); ?></th>
            <th class="col-formresult-ip"><?php echo $view['translator']->trans('autoborna.core.ipaddress'); ?></th>
            <?php
            $fields = $form->getFields();
            foreach ($fields as $f):
            if (in_array($f->getType(), $viewOnlyFields) || false === $f->getSaveResult()) {
                continue;
            }
            ?>
            <th class="col-formresult-field col-formresult-<?php echo $f->getType(); ?> col-formresult-field<?php echo $f->getId(); ?>"><?php echo $f->getLabel(); ?></th>
            <?php endforeach; ?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($results as $item):?>
            <tr>
                <td><?php echo $item['id']; ?></td>
                <td><?php echo $view['date']->toFull($item['dateSubmitted'], 'UTC'); ?></td>
                <td><?php echo $item['ipAddress']; ?></td>
                <?php foreach ($item['results'] as $r):?>
                    <td><?php echo $r['value']; ?></td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
