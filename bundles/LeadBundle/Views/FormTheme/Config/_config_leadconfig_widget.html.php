<?php

/*
 * @copyright   2014 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
?>

<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $view['translator']->trans('autoborna.config.tab.contact.merge.settings'); ?></h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <?php echo $view['form']->row($form['contact_unique_identifiers_operator']); ?>
            </div>
        </div>
    </div>
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $view['translator']->trans('autoborna.config.tab.contact.list.settings'); ?></h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <?php echo $view['form']->row($form['contact_columns']); ?>
            </div>
        </div>
    </div>
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $view['translator']->trans('autoborna.config.tab.importconfig'); ?></h3>
    </div>
    <div class="panel-body">
        <?php foreach ($form->children as $f): ?>
            <div class="row">
                <div class="col-md-6">
                    <?php echo $view['form']->row($f); ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>