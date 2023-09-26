<?php

/*
 * @copyright   2015 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
?>

<div class="row">
    <div class="col-sm-6">
        <div id="iplookup_fetch_button_container">
            <?php echo $view['form']->widget($form['fetch_button']); ?>
            <span class="fa fa-spinner fa-spin hide"></span>
            <?php if (isset($ipDataStoreLastDownloaded)): ?>
            <div class="small text-muted"><?php echo $ipDataStoreLastDownloaded; ?></div>
            <?php endif; ?>
        </div>
        <div class="col-md-9 help-block"></div>
    </div>
</div>