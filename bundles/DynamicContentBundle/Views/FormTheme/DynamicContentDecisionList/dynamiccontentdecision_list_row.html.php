<?php

/*
 * @copyright   2016 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
?>

<div class="row">
    <div class="col-xs-8">
        <?php echo $view['form']->row($form['dynamicContent']); ?>
    </div>
    <div class="col-xs-4 mt-lg">
        <div class="mt-3">
            <?php echo $view['form']->row($form['newDynamicContentButton']); ?>
        </div>
    </div>
</div>