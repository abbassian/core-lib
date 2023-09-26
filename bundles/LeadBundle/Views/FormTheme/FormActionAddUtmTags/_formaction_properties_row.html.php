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

<div class="row">
    <div class="col-xs-6">
        <?php foreach ($form->children as $child): ?>
        <div class="form-group col-xs-6">
            <?php echo $view['form']->label($child); ?>
            <?php echo $view['form']->widget($child); ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>